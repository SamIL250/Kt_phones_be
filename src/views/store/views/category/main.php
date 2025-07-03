<?php
// --- CONFIGURATION ---
$products_per_page = 9;

// --- GET PARAMETERS ---
$current_category_slug = $_GET['category'] ?? null;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$sort_order = $_GET['sort'] ?? 'latest';

// --- CALCULATE OFFSET for PAGINATION ---
$offset = ($current_page - 1) * $products_per_page;

// --- FETCH ALL CATEGORIES FOR THE SIDEBAR ---
$all_categories = [];
$category_query = mysqli_query($conn, "SELECT name, slug FROM categories WHERE is_active = 1 ORDER BY name ASC");
if ($category_query) {
    while ($row = mysqli_fetch_assoc($category_query)) {
        $all_categories[] = $row;
    }
}

// --- FETCH PRODUCTS FOR THE CURRENT CATEGORY ---
$category_name = 'Products'; // Default title
$category_description = '';
$category_products = [];
$total_products = 0;

if ($current_category_slug) {
    // --- SORTING LOGIC ---
    $order_by_sql = "ORDER BY p.created_at DESC"; // Default
    if ($sort_order === 'price_asc') {
        $order_by_sql = "ORDER BY price ASC";
    } elseif ($sort_order === 'price_desc') {
        $order_by_sql = "ORDER BY price DESC";
    }

    // --- GET TOTAL PRODUCT COUNT FOR PAGINATION ---
    $count_stmt = mysqli_prepare($conn, "SELECT COUNT(p.product_id) as total FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE c.slug = ? AND p.is_active = 1 AND p.published = 'true'");
    if ($count_stmt) {
        mysqli_stmt_bind_param($count_stmt, "s", $current_category_slug);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total_products = mysqli_fetch_assoc($count_result)['total'];
        mysqli_stmt_close($count_stmt);
    }
    $total_pages = ceil($total_products / $products_per_page);


    // --- FETCH PRODUCTS FOR THE CURRENT PAGE ---
    $sql = "SELECT 
                p.product_id, p.name AS product_name, p.base_price, p.discount_price,
                (IF(p.discount_price > 0, p.discount_price, p.base_price)) as price,
                c.name AS category_name, c.description AS category_description, c.slug,
                GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.is_primary DESC SEPARATOR ', ') AS image_urls
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            WHERE c.slug = ? AND p.is_active = 1 AND p.published = 'true'
            GROUP BY p.product_id
            $order_by_sql
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sii", $current_category_slug, $products_per_page, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($product = mysqli_fetch_assoc($result)) {
            $images = !empty($product['image_urls']) ? explode(', ', $product['image_urls']) : [];
            $product['primary_image'] = $images[0] ?? './src/assets/images/product_placeholder.png';
            $category_products[] = $product;
        }
        mysqli_stmt_close($stmt);

        if (!empty($category_products)) {
            $category_name = htmlspecialchars($category_products[0]['category_name']);
            $category_description = htmlspecialchars($category_products[0]['category_description']);
        } elseif ($total_products === 0) {
            $cat_info_query = mysqli_prepare($conn, "SELECT name, description FROM categories WHERE slug = ?");
            if ($cat_info_query) {
                mysqli_stmt_bind_param($cat_info_query, "s", $current_category_slug);
                mysqli_stmt_execute($cat_info_query);
                $cat_info_result = mysqli_stmt_get_result($cat_info_query);
                if ($cat_info = mysqli_fetch_assoc($cat_info_result)) {
                    $category_name = htmlspecialchars($cat_info['name']);
                    $category_description = htmlspecialchars($cat_info['description']);
                }
                mysqli_stmt_close($cat_info_query);
            }
        }
    }
}

// --- FETCH "YOU MAY ALSO LIKE" PRODUCTS ---
$related_products = [];
if ($current_category_slug) {
    $related_sql = "SELECT p.product_id, p.name AS product_name, p.base_price, p.discount_price, 
                           (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
                    FROM products p
                    JOIN categories c ON p.category_id = c.category_id
                    WHERE c.slug != ? AND p.is_active = 1 AND p.published = 'true'
                    ORDER BY RAND()
                    LIMIT 4";
    $related_stmt = mysqli_prepare($conn, $related_sql);
    if ($related_stmt) {
        mysqli_stmt_bind_param($related_stmt, "s", $current_category_slug);
        mysqli_stmt_execute($related_stmt);
        $related_result = mysqli_stmt_get_result($related_stmt);
        while ($row = mysqli_fetch_assoc($related_result)) {
            $related_products[] = $row;
        }
        mysqli_stmt_close($related_stmt);
    }
}
?>

<!-- Main Content -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav class="text-sm mb-4" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex space-x-2">
                <li class="flex items-center">
                    <a href="home" class="text-gray-500 hover:text-blue-600">Home</a>
                </li>
                <li class="flex items-center">
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="store" class="text-gray-500 hover:text-blue-600">Store</a>
                </li>
                <?php if ($current_category_slug): ?>
                    <li class="flex items-center">
                        <span class="text-gray-400 mx-2">/</span>
                        <span class="text-gray-800 text-[50px]"><?= $category_name ?></span>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>

        <div class="">

            <!-- Product Grid -->
            <main class="lg:col-span-3">

                <!-- Toolbar: Sorting and Product Count -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 bg-white/90 rounded-full border border-gray-200 px-4 py-3 gap-4">
                    <p class="text-gray-700 mb-2 sm:mb-0 text-base">
                        <span class="inline-block bg-blue-100 text-blue-600 font-semibold rounded-full px-3 py-1 mr-2 text-sm"><i class="bi bi-funnel-fill mr-1"></i> Filter</span>
                        Showing <span class="font-bold text-blue-600"><?= count($category_products) ?></span> of
                        <span class="font-bold text-blue-600"><?= $total_products ?></span> products
                    </p>
                    <div class="flex items-center gap-2">
                        <label for="sort" class="text-gray-600 font-medium mr-2"><i class="bi bi-sort-down-alt mr-1"></i>Sort by:</label>
                        <div class="relative">
                        <select id="sort" name="sort" onchange="location = this.value;"
                                class="pl-4 pr-10 py-2 rounded-full bg-gray-100 border border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-gray-700 font-semibold appearance-none transition-all duration-200">
                            <option value="?category=<?= $current_category_slug ?>&sort=latest" <?= $sort_order === 'latest' ? 'selected' : '' ?>>Latest</option>
                            <option value="?category=<?= $current_category_slug ?>&sort=price_asc" <?= $sort_order === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="?category=<?= $current_category_slug ?>&sort=price_desc" <?= $sort_order === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="bi bi-chevron-down"></i></span>
                        </div>
                    </div>
                </div>


                <?php if (empty($category_products) && $current_category_slug): ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                        <i class="bi bi-search text-5xl text-gray-400 mb-4"></i>
                        <p class="text-xl font-semibold text-gray-800">No products found in this category.</p>
                        <p class="mt-2 text-gray-500">Please check back later or browse other categories.</p>
                    </div>
                <?php elseif (!$current_category_slug): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="bi bi-tag text-5xl text-gray-400 mb-4"></i>
                        <p class="text-xl font-semibold text-gray-800">Please select a category</p>
                        <p class="mt-2 text-gray-500">Choose a category from the list to see the available products.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php foreach ($category_products as $product): ?>
                            <?php
                            $price = $product['price'];
                            $original_price = $product['base_price'];
                            $discount_exists = $product['discount_price'] > 0 && $product['discount_price'] < $product['base_price'];
                            $is_in_wishlist = in_array($product['product_id'], $wishlist_product_ids ?? []);
                            $heart_icon_class = $is_in_wishlist ? 'bi-heart-fill text-red-500' : 'bi-heart';
                            // Wishlist logic
                            $is_in_wishlist = false;
                            if ($is_logged_in) {
                                $wishlist_check_stmt = mysqli_prepare($conn, "SELECT 1 FROM wishlists w JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id WHERE w.user_id = ? AND wi.product_id = ?");
                                mysqli_stmt_bind_param($wishlist_check_stmt, "ss", $customer_id, $product['product_id']);
                                mysqli_stmt_execute($wishlist_check_stmt);
                                mysqli_stmt_store_result($wishlist_check_stmt);
                                $is_in_wishlist = mysqli_stmt_num_rows($wishlist_check_stmt) > 0;
                                mysqli_stmt_close($wishlist_check_stmt);
                            }
                            ?>
                            <!-- <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                <a href="product-view?product=<?= $product['product_id'] ?>" class="block relative">
                                    <img src="<?= htmlspecialchars($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>"
                                        class="w-full h-40 object-contain p-3 bg-gray-50 transition-transform duration-300 group-hover:scale-105">
                                    <?php if ($discount_exists): ?>
                                        <?php $discount_percentage = round((($original_price - $price) / $original_price) * 100); ?>
                                        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">-<?= $discount_percentage ?>%</div>
                                    <?php endif; ?>
                                </a>

                                <div class="p-3 flex flex-col flex-grow">
                                    <h3 class="text-sm font-semibold text-gray-800 mb-2 truncate" title="<?= htmlspecialchars($product['product_name']) ?>">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </h3>

                                    <div class="flex items-baseline gap-2 mb-3">
                                        <span class="text-base font-bold text-gray-900"><?= number_format($price, 0) ?> Frw</span>
                                        <?php if ($discount_exists): ?>
                                            <span class="text-xs text-gray-500 line-through"><?= number_format($original_price, 0) ?> Frw</span>
                                        <?php endif; ?>
                                    </div>

                                        <div class="mt-3 flex gap-2 z-20 relative">
                                            <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                                <?php if ($is_logged_in): ?>
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                                <?php endif; ?>
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="add-to-cart-btn w-full bg-blue-600 text-white text-xs font-bold py-2 px-2 rounded-md hover:bg-blue-700 transition-all flex items-center justify-center gap-1">
                                                    <i class="bi bi-cart-plus"></i> Add to cart
                                                </button>
                                            </form>
                                            <?php if (isset($is_logged_in) && $is_logged_in): ?>
                                                <form method="POST" action="./src/services/wishlist/new_wishlist.php" style="display:inline;">
                                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                                    <button type="submit" class="add-to-wishlist-btn border border-gray-300 text-gray-500 text-xs font-bold py-2 px-2 rounded-md hover:bg-gray-100 hover:text-red-500 transition-all">
                                                        <i class="bi <?= $heart_icon_class ?>"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button onclick="showAuthModal()" class="add-to-wishlist-btn border border-gray-300 text-gray-500 text-xs font-bold py-2 px-2 rounded-md hover:bg-gray-100 hover:text-red-500 transition-all">
                                                    <i class="bi bi-heart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                </div>
                            </div> -->


                            <div class="p-4 flex flex-col group transition-all duration-300">
                                <!-- Image & Icons -->
                                <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                                   
                                    <img src="<?= htmlspecialchars($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
                                    <!-- Wishlist and View Icons -->
                                    <div class="absolute top-3 right-3 flex flex-col gap-2">
                                        <?php if (isset($is_logged_in) && $is_logged_in): ?>
                                            <form method="POST" action="./src/services/wishlist/new_wishlist.php" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                                <button class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition">
                                                    <i class="bi <?= $heart_icon_class ?>"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                           
                                        <?php endif; ?>
                                        <!-- <button class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition"><i class="bi bi-heart text-lg"></i></button> -->

                                        <button onclick="window.location.replace('product-view?product=<?= $product['product_id'] ?>')" class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition"><i class="bi bi-eye text-lg"></i></button>
                                    </div>
                                    <!-- Add To Cart Button (hover only) -->
                                    <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                        <?php if ($is_logged_in): ?>
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="absolute left-0 bottom-0 w-full bg-black text-white font-semibold py-3 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            Add to cart
                                        </button>
                                    </form>
                                    
                                </div>
                                <!-- Product Info -->
                                <div class="mt-4 flex flex-col items-center">
                                    <h3 class="font-semibold text-gray-900 text-base text-center mb-1"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <?php
                                    // $rating = getProductRating($conn, $product['product_id']);
                                    $stars = round(isset($rating['avg_rating']) ? $rating['avg_rating'] : 0);
                                    ?>
                                    <div class="flex items-center justify-center gap-1 mb-1">
                                        <span class="text-lg font-bold text-blue-400"><?php echo $product['discount_price'] ? '$' . number_format($product['discount_price']) : '$' . number_format($product['base_price']); ?></span>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi <?php echo ($i <= $stars ? 'bi-star-fill text-yellow-400' : 'bi-star text-gray-300'); ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-xs text-gray-500 ml-1">(<?php echo isset($rating['review_count']) ? $rating['review_count'] : 0; ?>)</span>
                                    </div>
                                </div>
                            </div>


                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-8 flex justify-center" aria-label="Pagination">
                        <ul class="inline-flex items-center -space-x-px">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li>
                                    <a href="?category=<?= $current_category_slug ?>&sort=<?= $sort_order ?>&page=<?= $i ?>"
                                        class="py-2 px-4 leading-tight 
                                      <?= $i === $current_page
                                            ? 'bg-blue-600 text-white font-semibold shadow-md'
                                            : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700' ?>
                                      border border-gray-300 transition-colors">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </main>
        </div>

        <!-- "You May Also Like" Section -->
        <?php if (!empty($related_products)): ?>
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($related_products as $product): ?>
                    <?php
                        $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                        ?>
                        <div class="p-4 flex flex-col group transition-all duration-300">
                                <!-- Image & Icons -->
                                <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                                   
                                    <img src="<?= htmlspecialchars($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
                                    <!-- Wishlist and View Icons -->
                                    <div class="absolute top-3 right-3 flex flex-col gap-2">
                                        <?php if (isset($is_logged_in) && $is_logged_in): ?>
                                            <form method="POST" action="./src/services/wishlist/new_wishlist.php" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                                <button class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition">
                                                    <i class="bi <?= $heart_icon_class ?>"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            
                                        <?php endif; ?>
                                        <!-- <button class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition"><i class="bi bi-heart text-lg"></i></button> -->

                                        <button onclick="window.location.replace('product-view?product=<?= $product['product_id'] ?>')" class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition"><i class="bi bi-eye text-lg"></i></button>
                                    </div>
                                    <!-- Add To Cart Button (hover only) -->
                                    <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                        <?php if ($is_logged_in): ?>
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="absolute left-0 bottom-0 w-full bg-black text-white font-semibold py-3 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            Add to cart
                                        </button>
                                    </form>
                                    
                                </div>
                                <!-- Product Info -->
                                <div class="mt-4 flex flex-col items-center">
                                    <h3 class="font-semibold text-gray-900 text-base text-center mb-1"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <?php
                                    // $rating = getProductRating($conn, $product['product_id']);
                                    $stars = round(isset($rating['avg_rating']) ? $rating['avg_rating'] : 0);
                                    ?>
                                    <div class="flex items-center justify-center gap-1 mb-1">
                                        <span class="text-lg font-bold text-blue-400"><?php echo $product['discount_price'] ? '$' . number_format($product['discount_price']) : '$' . number_format($product['base_price']); ?></span>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi <?php echo ($i <= $stars ? 'bi-star-fill text-yellow-400' : 'bi-star text-gray-300'); ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-xs text-gray-500 ml-1">(<?php echo isset($rating['review_count']) ? $rating['review_count'] : 0; ?>)</span>
                                    </div>
                                </div>
                            </div>
                    <?php endforeach; ?>
                </div>
</div>
        <?php endif; ?>

    </div>
</div>

<script>
    function showLoginModal() {
        // This function should be defined globally, perhaps in your main layout file.
        // It should display a modal asking the user to log in.
        alert('Please log in to add items to your wishlist.');
        window.location.href = 'signin'; // Fallback redirect
    }

    function handleWishlistAction(button, productId, userId) {
        if (!userId) {
            showLoginModal();
            return;
        }

        const isInWishlist = button.getAttribute('data-in-wishlist') === 'true';
        const url = isInWishlist ?
            `./src/services/wishlist/remove_item.php?product_id=${productId}&user_id=${userId}` :
            `./src/services/wishlist/new_wishlist.php?product=${productId}&user=${userId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                    const icon = button.querySelector('i');
                    if (isInWishlist) {
                        button.setAttribute('data-in-wishlist', 'false');
                        button.classList.remove('bg-red-100', 'text-red-500');
                        button.classList.add('bg-gray-200', 'text-gray-600', 'hover:bg-red-100', 'hover:text-red-500');
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                    } else {
                        button.setAttribute('data-in-wishlist', 'true');
                        button.classList.remove('bg-gray-200', 'text-gray-600', 'hover:bg-red-100', 'hover:text-red-500');
                        button.classList.add('bg-red-100', 'text-red-500');
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    }
                    showToast(data.message, 'success');
            } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
                // Update wishlist count in navbar
                updateWishlistCount(userId);
            })
            .catch(error => {
                console.error('Wishlist action failed:', error);
                showToast('Request failed. Please try again.', 'error');
            });
    }


    function handleCartAction(productId, userId, price) {
        if (!userId) {
            // Handle guest cart logic if needed, or prompt login
            showToast('Please log in to add items to your cart.', 'info');
            // Example: save to localStorage and redirect
            // localStorage.setItem('pending_cart_item', productId);
            // window.location.href = 'signin';
            return;
        }

        fetch(`./src/services/cart/new_cart.php?product=${productId}&user=${userId}&price=${price}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    updateCartCount(userId); // Update cart count in navbar
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Add to cart failed:', error);
                showToast('Could not add to cart. Please try again.', 'error');
            });
    }

    function updateWishlistCount(userId) {
        if (!userId) return;
        fetch(`./src/services/wishlist/wishlist_count.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const countElement = document.getElementById('wishlist-count');
                if (countElement && data.success) {
                    countElement.textContent = data.count;
                    countElement.style.display = data.count > 0 ? 'flex' : 'none';
                }
            });
    }

    function updateCartCount(userId) {
        if (!userId) return;
        fetch(`./src/services/cart/cart_count.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const countElement = document.getElementById('cart-count');
                if (countElement && data.success) {
                    countElement.textContent = data.count;
                    countElement.style.display = data.count > 0 ? 'flex' : 'none';
                }
            });
    }

    // Toast notification
    if (typeof window.Notyf !== 'undefined') {
        window.notyfInstance = window.notyfInstance || new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } });
    }
    function showToast(message, type = 'info') {
        if (!window.notyfInstance) return;
        if (type === 'success') return notyfInstance.success(message);
        if (type === 'error' || type === 'warning') return notyfInstance.error(message);
        notyfInstance.open({ type: 'info', message });
    }
</script>