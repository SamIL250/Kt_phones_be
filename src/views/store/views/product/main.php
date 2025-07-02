<?php
// Session logic for recently viewed products
if (!isset($_SESSION['recently_viewed'])) {
    $_SESSION['recently_viewed'] = [];
}

// Add current product to recently viewed
if (isset($_GET['product'])) {
    $current_product_id = $_GET['product'];
    // Add to the beginning of the array
    array_unshift($_SESSION['recently_viewed'], $current_product_id);
    // Remove duplicates
    $_SESSION['recently_viewed'] = array_unique($_SESSION['recently_viewed']);
    // Keep only the last 5 items (as we show 4, and one might be the current product)
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 5);
}

// Validate and sanitize product_id
$product_id = isset($_GET['product']) ? trim($_GET['product']) : '';
if (empty($product_id)) {
    header('Location: store');
    exit;
}

// Prepare statement to prevent SQL injection
$get_product_details = mysqli_prepare($conn, "
    SELECT 
        p.*,  -- All product columns
        c.name AS category_name,
        c.description AS category_description,
        c.slug AS category_slug,
        b.name AS brand_name,
        b.description AS brand_description,

        -- Aggregated attributes
        GROUP_CONCAT(DISTINCT CONCAT(at.name, ': ', av.value) SEPARATOR ', ') AS attributes,

        -- All images (not just primary)
        GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.display_order SEPARATOR ', ') AS image_urls,
        
        -- Aggregated tags
        GROUP_CONCAT(DISTINCT pt.tag_name SEPARATOR ', ') AS tags,
        
        -- Reviews and ratings
        AVG(pr.rating) as avg_rating,
        COUNT(pr.review_id) as review_count

        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
        LEFT JOIN attribute_type at ON pa.attribute_type_id = at.attribute_type_id
        LEFT JOIN attribute_value av ON pa.attribute_value_id = av.attribute_value_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        LEFT JOIN product_tag_map ptm ON p.product_id = ptm.product_id
        LEFT JOIN product_tags pt ON ptm.tag_id = pt.tag_id
    LEFT JOIN product_reviews pr ON p.product_id = pr.product_id
    WHERE p.product_id = ? AND p.is_active = 1 AND p.published = 'true'
        GROUP BY p.product_id
    ORDER BY p.created_at DESC
");

mysqli_stmt_bind_param($get_product_details, "s", $product_id);
mysqli_stmt_execute($get_product_details);
$result = mysqli_stmt_get_result($get_product_details);

$product = [];
if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
    
    // Process image URLs
    $product['image_urls_array'] = $product['image_urls'] ? explode(', ', $product['image_urls']) : [];
    $product['primary_image'] = !empty($product['image_urls_array']) ? $product['image_urls_array'][0] : './src/assets/images/product_placeholder.png';
    
    // Process tags
    $product['tags_array'] = $product['tags'] ? explode(', ', $product['tags']) : [];
    
    // Process attributes
    $product['attributes_array'] = [];
    if ($product['attributes']) {
        $attributes_pairs = explode(', ', $product['attributes']);
        foreach ($attributes_pairs as $pair) {
            $parts = explode(': ', $pair, 2);
            if (count($parts) == 2) {
                $product['attributes_array'][trim($parts[0])] = trim($parts[1]);
            }
        }
}

    $product_name = $product['name'];
} else {
    // Product not found or not active
    header('Location: store');
    exit;
}

// Check if product is in user's wishlist
$is_in_wishlist = false;
if (isset($is_logged_in) && $is_logged_in && isset($customer_id)) {
    $check_wishlist_query = mysqli_prepare($conn, "
        SELECT 1 FROM wishlists w 
        JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id 
        WHERE w.user_id = ? AND wi.product_id = ?
    ");
    if ($check_wishlist_query) {
        mysqli_stmt_bind_param($check_wishlist_query, "ss", $customer_id, $product_id);
        mysqli_stmt_execute($check_wishlist_query);
        mysqli_stmt_store_result($check_wishlist_query);
        if (mysqli_stmt_num_rows($check_wishlist_query) > 0) {
            $is_in_wishlist = true;
        }
        mysqli_stmt_close($check_wishlist_query);
    }
}

// Fetch product reviews
$reviews = [];
$get_reviews_query = mysqli_prepare($conn, "
    SELECT 
        pr.rating,
        pr.comment,
        pr.created_at,
        pr.is_verified_purchase,
        u.first_name,
        u.last_name
    FROM product_reviews pr
    JOIN users u ON pr.user_id = u.user_id
    WHERE pr.product_id = ?
    ORDER BY pr.created_at DESC
    LIMIT 10
");

mysqli_stmt_bind_param($get_reviews_query, "s", $product_id);
mysqli_stmt_execute($get_reviews_query);
$reviews_result = mysqli_stmt_get_result($get_reviews_query);

if ($reviews_result) {
    while ($row = mysqli_fetch_assoc($reviews_result)) {
        $reviews[] = $row;
    }
}
mysqli_stmt_close($get_reviews_query);

// Calculate price and discount
$current_price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
$original_price = $product['base_price'];
$discount_percentage = 0;
if ($product['discount_price'] > 0) {
    $discount_percentage = round((($original_price - $current_price) / $original_price) * 100);
}

// Fetch product variants for storage and color selection
$variants = [];
$variants_query = mysqli_prepare($conn, "
    SELECT 
        pv.variant_id,
        pv.price as variant_price,
        pv.stock_quantity as variant_stock,
        pv.is_active,
        size_val.value as storage_value,
        color_val.value as color_value,
        size_val.attribute_value_id as storage_id,
        color_val.attribute_value_id as color_id
    FROM product_variants pv
    LEFT JOIN attribute_value size_val ON pv.size_id = size_val.attribute_value_id
    LEFT JOIN attribute_value color_val ON pv.color_id = color_val.attribute_value_id
    WHERE pv.product_id = ? AND pv.is_active = 1
    ORDER BY pv.variant_id
");

mysqli_stmt_bind_param($variants_query, "s", $product_id);
mysqli_stmt_execute($variants_query);
$variants_result = mysqli_stmt_get_result($variants_query);

if ($variants_result) {
    while ($row = mysqli_fetch_assoc($variants_result)) {
        $variants[] = $row;
    }
}
mysqli_stmt_close($variants_query);

// Organize variants by storage and color options
$storage_options = [];
$color_options = [];
$variant_prices = [];
$variant_stocks = [];

foreach ($variants as $variant) {
    if ($variant['storage_value']) {
        $storage_options[$variant['storage_id']] = $variant['storage_value'];
    }
    if ($variant['color_value']) {
        $color_options[$variant['color_id']] = $variant['color_value'];
    }
    
    // Store variant pricing and stock info
    $key = $variant['storage_id'] . '_' . $variant['color_id'];
    $variant_prices[$key] = $variant['variant_price'];
    $variant_stocks[$key] = $variant['variant_stock'];
}

// Generate star rating HTML
$avg_rating = round($product['avg_rating'] ?? 0);
$stars_html = '';
for ($i = 1; $i <= 5; $i++) {
    if ($i <= $avg_rating) {
        $stars_html .= '<i class="bi bi-star-fill text-yellow-400"></i>';
    } else {
        $stars_html .= '<i class="bi bi-star text-gray-300"></i>';
    }
}
?>

<style>
    .color-swatch {
        width: 40px;
        height: 40px;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .color-swatch.selected {
        border-color: #3b82f6;
        transform: scale(1.1);
    }

    .star-filled {
        color: #f59e0b;
    }

    .breadcrumb-arrow:after {
        content: '›';
        margin: 0 0.5rem;
        color: #6b7280;
    }

    .product-gallery {
        position: relative;
    }

    .main-image {
        transition: all 0.3s ease;
    }

    .thumbnail {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .thumbnail:hover {
        border-color: #3b82f6;
        transform: scale(1.05);
    }

    .thumbnail.active {
        border-color: #3b82f6;
    }

    .quantity-input {
        width: 60px;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quantity-btn:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .add-to-cart-btn {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        transform: translateY(-1px);
    }

    .wishlist-btn {
        transition: all 0.3s ease;
    }

    .wishlist-btn:hover {
        transform: scale(1.1);
    }

    .wishlist-btn.active {
        color: #ef4444;
    }

    .attribute-badge {
        background: #f3f4f6;
        color: #374151;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .review-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .review-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center text-sm text-blue-500 mb-6">
        <a href="store" class="hover:underline">Store</a>
        <span class="breadcrumb-arrow"></span>
        <?php if (!empty($product['category_name'])): ?>
            <a href="category?category=<?= htmlspecialchars($product['category_slug']) ?>" class="hover:underline"><?= htmlspecialchars($product['category_name']) ?></a>
            <span class="breadcrumb-arrow"></span>
        <?php endif; ?>
        <span class="text-gray-600"><?= htmlspecialchars($product_name) ?></span>
    </nav>

    <!-- Product Details Section -->
    <?php
        include 'utils/product.php'
    ?>

    <!-- Related Products -->
    <div class="mt-16">
        <?php include 'utils/related-products.php'; ?>
    </div>

    <!-- Recently Viewed Products -->
    <div class="mt-16">
        <?php include 'utils/recently-viewed-products.php'; ?>
    </div>
</div>

    <div class="relative min-h-[40vh]">
    <div class="w-full mx-auto mb-6 absolute">
        <div class="swiper mySwiper overflow-hidden">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="./src/assets/images/552406The-future-is-here-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 1" class="w-full h-48 sm:h-64 object-cover" /></div>
                <div class="swiper-slide"><img src="./src/assets/images/Get-more-wad-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 2" class="w-full h-48 sm:h-64 object-cover" /></div>
                <div class="swiper-slide"><img src="./src/assets/images/Visit-Our-Store-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 3" class="w-full h-48 sm:h-64 object-cover" /></div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
      var swiper = new Swiper('.mySwiper', {
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        autoplay: {
          delay: 3500,
          disableOnInteraction: false,
        },
        slidesPerView: 1,
        spaceBetween: 0,
      });
    </script>
    </div>

<!-- Login Modal -->
<div id="loginModal" class="fixed inset-0 bg-white/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <!-- Modal Header -->
        <div class="relative p-6 border-b border-gray-100">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <i class="bi bi-heart-fill text-white text-2xl"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Add to Wishlist</h3>
            <p class="text-gray-600 text-center text-sm">Sign in to save your favorite products and get personalized recommendations</p>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="space-y-4">
                <p class="text-gray-700 text-center text-sm leading-relaxed">
                    Create an account or sign in to save products to your wishlist, track your orders, and enjoy exclusive offers.
                </p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-100 space-y-3">
            <button onclick="window.location.href='signin'" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In / Sign Up
            </button>
            <button onclick="closeLoginModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-300">
                No, Thanks!
            </button>
        </div>

        <!-- Close Button -->
        <button onclick="closeLoginModal()" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors duration-300">
            <i class="bi bi-x text-lg"></i>
        </button>
    </div>
</div>

<?php
    include './src/views/store/javascript.php';
?>