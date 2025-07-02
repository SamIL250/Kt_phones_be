<?php
$search_query = htmlspecialchars($_GET['search'] ?? '');
$sort_by = htmlspecialchars($_GET['sort'] ?? 'newest');
$category_filter = htmlspecialchars($_GET['category'] ?? '');
$brand_filter = htmlspecialchars($_GET['brand'] ?? '');
$price_min_filter = htmlspecialchars($_GET['price_min'] ?? '');
$price_max_filter = htmlspecialchars($_GET['price_max'] ?? '');

// Fetch categories for the filter sidebar
$categories_sql = "SELECT c.name, c.slug, COUNT(p.product_id) as product_count 
                   FROM categories c 
                   LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1 AND p.published = 'true'
                   GROUP BY c.category_id 
                   HAVING product_count > 0 
                   ORDER BY product_count DESC";
$categories_result = mysqli_query($conn, $categories_sql);

// Fetch brands for the filter sidebar
$brands_sql = "SELECT b.name, COUNT(p.product_id) as product_count 
               FROM brands b 
               LEFT JOIN products p ON b.brand_id = p.brand_id AND p.is_active = 1 AND p.published = 'true'
               GROUP BY b.brand_id 
               HAVING product_count > 0 
               ORDER BY product_count DESC";
$brands_result = mysqli_query($conn, $brands_sql);

// Get overall price range for the filter
$price_range_sql = "SELECT MIN(discount_price) as min_price, MAX(discount_price) as max_price FROM products WHERE is_active = 1 AND published = 'true'";
$price_range_result = mysqli_query($conn, $price_range_sql);
$price_range = mysqli_fetch_assoc($price_range_result);
$global_min_price = $price_range['min_price'] ?? 0;
$global_max_price = $price_range['max_price'] ?? 500000;

// Base query for products
$products_sql = "
    SELECT p.*, b.name as brand_name,
    (SELECT GROUP_CONCAT(image_url ORDER BY display_order SEPARATOR ',') FROM product_images WHERE product_id = p.product_id) as image_urls
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.is_active = 1 AND p.published = 'true'
";

// Apply filters
if (!empty($search_query)) {
    $products_sql .= " AND p.name LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%'";
}
if (!empty($category_filter)) {
    $products_sql .= " AND c.slug = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
}
if (!empty($brand_filter)) {
    $brands_array = explode(',', $brand_filter);
    $brands_array = array_map(function ($b) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $b) . "'";
    }, $brands_array);
    $products_sql .= " AND b.name IN (" . implode(',', $brands_array) . ")";
}
if (!empty($price_min_filter)) {
    $products_sql .= " AND p.discount_price >= " . (int)$price_min_filter;
}
if (!empty($price_max_filter)) {
    $products_sql .= " AND p.discount_price <= " . (int)$price_max_filter;
}


// Get total product count with filters applied
$count_sql = "SELECT COUNT(DISTINCT p.product_id) as total 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id
              LEFT JOIN brands b ON p.brand_id = b.brand_id
              WHERE p.is_active = 1 AND p.published = 'true'";
if (!empty($search_query)) {
    $count_sql .= " AND p.name LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%'";
}
if (!empty($category_filter)) {
    $count_sql .= " AND c.slug = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
}
if (!empty($brand_filter)) {
    $brands_array = explode(',', $brand_filter);
    $brands_array = array_map(function ($b) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $b) . "'";
    }, $brands_array);
    $count_sql .= " AND b.name IN (" . implode(',', $brands_array) . ")";
}
if (!empty($price_min_filter)) {
    $count_sql .= " AND p.discount_price >= " . (int)$price_min_filter;
}
if (!empty($price_max_filter)) {
    $count_sql .= " AND p.discount_price <= " . (int)$price_max_filter;
}
$total_products_result = mysqli_query($conn, $count_sql);
$total_products = mysqli_fetch_assoc($total_products_result)['total'];


// Apply sorting
$order_by = 'p.created_at DESC'; // Default: newest
if ($sort_by === 'price_asc') {
    $order_by = 'p.discount_price ASC';
} elseif ($sort_by === 'price_desc') {
    $order_by = 'p.discount_price DESC';
} elseif ($sort_by === 'rating') {
    $order_by = 'p.average_rating DESC';
}
$products_sql .= " GROUP BY p.product_id ORDER BY $order_by";


// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_products / $limit);

$products_sql .= " LIMIT $limit OFFSET $offset";

$products_result = mysqli_query($conn, $products_sql);

$products = [];
if ($products_result) {
    while ($row = mysqli_fetch_assoc($products_result)) {
        $products[] = $row;
    }
}

// Fetch "You might also like" products (e.g., newest 4)
$related_products_sql = "
    SELECT p.*, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.product_id ORDER BY display_order LIMIT 1) as image_url
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.is_active = 1 AND p.published = 'true'
    ORDER BY p.created_at DESC
    LIMIT 4
";
$related_products_result = mysqli_query($conn, $related_products_sql);
$related_products = [];
if ($related_products_result) {
    while ($row = mysqli_fetch_assoc($related_products_result)) {
        $related_products[] = $row;
    }
}

$category_name = 'All Products';
if (!empty($category_filter)) {
    $cat_name_sql = "SELECT name FROM categories WHERE slug = '" . mysqli_real_escape_string($conn, $category_filter) . "' LIMIT 1";
    $cat_name_result = mysqli_query($conn, $cat_name_sql);
    if ($cat_name_row = mysqli_fetch_assoc($cat_name_result)) {
        $category_name = $cat_name_row['name'];
    }
}

?>
<div class="relative bg-cover bg-center text-white mb-8 py-16 px-8 text-center observe-card" style="background-image: url('src/assets/images/header1.jpg');">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative">
        <nav class="flex justify-center mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-200">
                <li><a href="home" class="hover:text-white transition-colors">Home</a></li>
                <li><i class="bi bi-chevron-right text-xs"></i></li>
                <li><a href="store" class="hover:text-white transition-colors">Store</a></li>
                <?php if (!empty($category_filter) && $category_name !== 'All Products') : ?>
                    <li><i class="bi bi-chevron-right text-xs"></i></li>
                    <li class="font-medium text-white"><?= htmlspecialchars($category_name) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-3"><?= htmlspecialchars($category_name) ?></h1>
        <p class="text-lg text-gray-200 max-w-2xl mx-auto">Discover the perfect phone that fits your lifestyle from our curated selection.</p>
    </div>
</div>

<div class="bg-gray-50 font-sans">
    <div class="">

        <!-- Hero Section -->
        


        <div class="container max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
            <!-- Product Grid -->
            <?php
                include 'utils/products.php';
            ?>
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

        <!-- You Might Also Like Section -->
        <div class="container max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
            <?php
                include 'utils/you_may_also_like.php';
            ?>
        </div>
        
    </div>
</div>

<!-- Add noUiSlider CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" />

<!-- Add noUiSlider JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js"></script>

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

<script>
    function showAuthModal() {
        const modal = document.getElementById('loginModal');
        const modalContent = document.getElementById('modalContent');
        if (!modal || !modalContent) {
            alert('Login modal not found in DOM!');
            return;
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeLoginModal() {
        const modal = document.getElementById('loginModal');
        const modalContent = document.getElementById('modalContent');
        if (!modal || !modalContent) return;
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
