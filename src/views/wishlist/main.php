<style>
    /* Pagination Section Container */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.875rem;
    }

    /* Info & View Toggle Section */
    .pagination-container .text-gray-400 {
        color: #9fa6bc;
        margin-left: 0.75rem;
    }

    .pagination-container a {
        font-weight: 600;
        color: #3874ff;
        text-decoration: none;
        margin-left: 1rem;
    }

    .pagination-container a:hover {
        color: #003cc7;
        text-decoration: underline;
    }

    /* Pagination Buttons */
    .page-link {
        background-color: transparent;
        border: none;
        color: #3874ff;
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        margin: 0 0.25rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .page-link:hover {
        color: #003cc7;
    }

    /* Icon Sizes */
    .page-link .fas {
        font-size: 0.75rem;
    }

    /* Pagination List */
    .pagination {
        display: flex;
        list-style: none;
        margin: 0 0.5rem;
        padding: 0;
    }

    /* Pagination Items */
    .pagination li {
        margin: 0 0.25rem;
    }

    .pagination li a {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        color: #3874ff;
        border: 1px solid #cbd0dd;
        border-radius: 0.25rem;
        text-decoration: none;
    }

    .pagination li a:hover {
        background-color: #e5edff;
        border-color: #003cc7;
        color: #003cc7;
    }

    .pagination li.active a {
        background-color: #3874ff;
        color: white;
        border-color: #3874ff;
    }
</style>

<?php
// --- CONFIGURATION & PAGINATION ---
$items_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// --- Fetch Wishlist Items ---
$wishlist_items = [];
$total_wishlist_items = 0;

if ($is_logged_in) {
    // Get total count for pagination
    $count_query = mysqli_prepare($conn, "SELECT COUNT(wi.product_id) as total FROM wishlist_items wi JOIN wishlists w ON wi.wishlist_id = w.wishlist_id WHERE w.user_id = ?");
    if ($count_query) {
        mysqli_stmt_bind_param($count_query, "s", $customer_id);
        mysqli_stmt_execute($count_query);
        $total_wishlist_items = mysqli_fetch_assoc(mysqli_stmt_get_result($count_query))['total'];
        mysqli_stmt_close($count_query);
    }
    $total_pages = ceil($total_wishlist_items / $items_per_page);

    // Fetch paginated items
    $wishlist_query = mysqli_prepare(
    $conn,
    "SELECT 
            p.product_id, p.name, p.base_price, p.discount_price, p.stock_quantity,
            (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
        FROM wishlist_items wi
        JOIN wishlists w ON wi.wishlist_id = w.wishlist_id
        JOIN products p ON wi.product_id = p.product_id
        WHERE w.user_id = ? AND p.is_active = 1
        ORDER BY wi.added_at DESC
        LIMIT ? OFFSET ?"
    );

    if ($wishlist_query) {
        mysqli_stmt_bind_param($wishlist_query, "sii", $customer_id, $items_per_page, $offset);
        mysqli_stmt_execute($wishlist_query);
        $result = mysqli_stmt_get_result($wishlist_query);
        while ($item = mysqli_fetch_assoc($result)) {
            $wishlist_items[] = $item;
        }
        mysqli_stmt_close($wishlist_query);
    }
}

// --- Get Product IDs from Wishlist for exclusion in suggestions ---
$wishlist_product_ids = [0]; // Initialize with a non-existent ID to prevent empty IN () errors
if (!empty($wishlist_items)) {
    foreach ($wishlist_items as $item) {
        $wishlist_product_ids[] = $item['product_id'];
    }
}
$excluded_ids_sql = implode(',', array_map('intval', $wishlist_product_ids));

// --- Fetch "Discover More" Products (for sidebar) using a LEFT JOIN ---
$discover_products = [];
$discover_sql = "
    SELECT p.product_id, p.name, p.base_price, p.discount_price, img.image_url as primary_image
    FROM products p
    LEFT JOIN (
        SELECT wi.product_id 
        FROM wishlist_items wi
        JOIN wishlists w ON wi.wishlist_id = w.wishlist_id
        WHERE w.user_id = ?
    ) as user_wishlist ON p.product_id = user_wishlist.product_id
    LEFT JOIN product_images img ON p.product_id = img.product_id AND img.is_primary = 1
    WHERE p.is_active = 1 AND p.published = 'true' AND user_wishlist.product_id IS NULL
    GROUP BY p.product_id
    ORDER BY RAND()
    LIMIT 5";

$discover_stmt = mysqli_prepare($conn, $discover_sql);
if ($discover_stmt) {
    mysqli_stmt_bind_param($discover_stmt, "s", $customer_id);
    mysqli_stmt_execute($discover_stmt);
    $discover_result = mysqli_stmt_get_result($discover_stmt);
    while ($row = mysqli_fetch_assoc($discover_result)) {
        $discover_products[] = $row;
    }
    mysqli_stmt_close($discover_stmt);
}

// --- Fetch "You May Also Like" Products (for bottom section) using a LEFT JOIN ---
$related_products = [];
$related_sql = "
    SELECT p.product_id, p.name, p.base_price, p.discount_price, img.image_url as primary_image
    FROM products p
    LEFT JOIN (
        SELECT wi.product_id 
        FROM wishlist_items wi
        JOIN wishlists w ON wi.wishlist_id = w.wishlist_id
        WHERE w.user_id = ?
    ) as user_wishlist ON p.product_id = user_wishlist.product_id
    LEFT JOIN product_images img ON p.product_id = img.product_id AND img.is_primary = 1
    WHERE p.is_active = 1 AND p.published = 'true' AND user_wishlist.product_id IS NULL
    GROUP BY p.product_id
    ORDER BY p.created_at DESC
    LIMIT 4";

$related_stmt = mysqli_prepare($conn, $related_sql);
if ($related_stmt) {
    mysqli_stmt_bind_param($related_stmt, "s", $customer_id);
    mysqli_stmt_execute($related_stmt);
    $related_result = mysqli_stmt_get_result($related_stmt);
    while ($row = mysqli_fetch_assoc($related_result)) {
        $related_products[] = $row;
    }
    mysqli_stmt_close($related_stmt);
}

?>

<!-- Main Content -->
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs & Header -->
        <div class="mb-6">
            <nav class="text-sm mb-2" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex space-x-2">
                    <li class="flex items-center"><a href="home" class="text-gray-500 hover:text-blue-600">Home</a></li>
                    <li class="flex items-center"><span class="text-gray-400 mx-2">/</span><a href="store" class="text-gray-500 hover:text-blue-600">Store</a></li>
                    <li class="flex items-center"><span class="text-gray-400 mx-2">/</span><span class="text-gray-800 font-medium">My Wishlist</span></li>
                </ol>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">My Wishlist (<?= $total_wishlist_items ?>)</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Wishlist Column -->
            <main class="lg:col-span-2">
                <?php if (!$is_logged_in): ?>
                    <!-- Not Logged In State -->
                    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6"><i class="bi bi-box-arrow-in-right text-3xl"></i></div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Please Log In</h2>
                        <p class="text-gray-600 mb-6">Log in to view your wishlist and save your favorite products for later.</p>
                        <a href="signin" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors duration-300">Sign In</a>
                    </div>

                <?php elseif (empty($wishlist_items)): ?>
                    <!-- Empty Wishlist State -->
                    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6"><i class="bi bi-heart text-3xl"></i></div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Your Wishlist is Empty</h2>
                        <p class="text-gray-600 mb-6">You haven't added any products yet. Start exploring to find products you'll love!</p>
                        <a href="store" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors duration-300">Discover Products</a>
                    </div>

                <?php else: ?>
                    <!-- Wishlist Content -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center p-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Your Items</h2>
                            <button onclick="clearWishlist('<?= $customer_id ?>')" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors"><i class="bi bi-trash-fill mr-1"></i> Clear All</button>
    </div>

                        <!-- Wishlist Items -->
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($wishlist_items as $item): ?>
                            <?php
                                $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['base_price'];
                                $is_in_stock = $item['stock_quantity'] > 0;
                                ?>
                                <div id="wishlist-item-<?= $item['product_id'] ?>" class="p-4 flex flex-col md:flex-row items-start md:items-center gap-4">
                                    <!-- Image & Name -->
                                    <div class="flex items-center gap-4 flex-1">
                                        <a href="product-view?product=<?= $item['product_id'] ?>" class="flex-shrink-0">
                                            <img src="<?= htmlspecialchars($item['primary_image'] ?? './src/assets/images/product_placeholder.png') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-20 h-20 object-contain rounded-md border border-gray-200">
                                        </a>
                                        <div>
                                            <a href="product-view?product=<?= $item['product_id'] ?>" class="font-semibold text-gray-800 hover:text-blue-600 transition-colors"><?= htmlspecialchars($item['name']) ?></a>
                                            <div class="mt-1 text-sm">
                                                <?php if ($is_in_stock): ?>
                                                    <span class="text-green-600">In Stock</span>
                                                <?php else: ?>
                                                    <span class="text-red-600">Out of Stock</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="flex-shrink-0 w-full md:w-32 text-left md:text-center">
                                        <span class="md:hidden font-semibold text-gray-500 text-sm">Price: </span>
                                        <span class="font-semibold text-gray-900"><?= number_format($price, 0) ?> Frw</span>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex-shrink-0 w-full md:w-auto flex items-center gap-3">
                                        <button onclick="handleCartAction('<?= $item['product_id'] ?>', '<?= $customer_id ?>', '<?= $price ?>')"
                                            class="flex-1 md:flex-none bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-semibold flex items-center justify-center gap-2"
                                            <?= !$is_in_stock ? 'disabled style="background-color: #9ca3af; cursor: not-allowed;"' : '' ?>>
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                        <button onclick="removeFromWishlist(this, '<?= $item['product_id'] ?>', '<?= $customer_id ?>')" title="Remove from wishlist"
                                            class="text-gray-400 hover:text-red-500 transition-colors p-2"><i class="bi bi-trash-fill text-xl"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="p-4 border-t border-gray-200 flex justify-center" aria-label="Pagination">
                                <ul class="inline-flex items-center -space-x-px">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li>
                                            <a href="?page=<?= $i ?>"
                                                class="py-2 px-3 leading-tight text-sm rounded-md
                                              <?= $i === $current_page
                                                    ? 'bg-blue-600 text-white font-semibold shadow-sm'
                                                    : 'bg-white text-gray-600 hover:bg-gray-100 hover:text-gray-800' ?>
                                                border border-gray-300 transition-colors mx-1">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>

            <!-- Sidebar -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-4 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Discover More</h3>
                    <div class="space-y-4">
                        <?php foreach ($discover_products as $product): ?>
                            <?php
                            $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                            // Simplified wishlist check for suggested items
                            $is_in_wishlist = false; // By definition, these items are not in the wishlist
                            ?>
                            <div class="flex items-center gap-3">
                                <a href="product-view?product=<?= $product['product_id'] ?>" class="flex-shrink-0">
                                    <img src="<?= htmlspecialchars($product['primary_image'] ?? './src/assets/images/product_placeholder.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-16 h-16 object-contain rounded-md border border-gray-200">
                                </a>
                                <div class="flex-grow">
                                    <a href="product-view?product=<?= $product['product_id'] ?>" class="text-sm font-semibold text-gray-700 hover:text-blue-600 line-clamp-2"><?= htmlspecialchars($product['name']) ?></a>
                                    <p class="text-sm font-bold text-gray-800 mt-1"><?= number_format($price, 0) ?> Frw</p>
                </div>
                                <div class="flex flex-col gap-1">
                                    <button onclick="handleWishlistAction(this, '<?= $product['product_id'] ?>', '<?= $customer_id ?>')" data-in-wishlist="false" class="p-1.5 rounded-md bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-500 transition-colors">
                                        <i class="bi bi-heart text-sm"></i>
                            </button>
                                    <button onclick="handleCartAction('<?= $product['product_id'] ?>', '<?= $customer_id ?>', '<?= $price ?>')" class="p-1.5 rounded-md bg-gray-200 text-gray-600 hover:bg-blue-100 hover:text-blue-600 transition-colors">
                                        <i class="bi bi-cart-plus text-sm"></i>
                            </button>
                        </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>

        <!-- You May Also Like Section (underneath) -->
        <?php if (!empty($related_products)): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($related_products as $product): ?>
                        <?php
                        $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                        $original_price = $product['base_price'];
                        $discount_exists = $product['discount_price'] > 0 && $product['discount_price'] < $product['base_price'];
                        ?>
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden group">
                            <a href="product-view?product=<?= $product['product_id'] ?>" class="block relative">
                                <img src="<?= htmlspecialchars($product['primary_image'] ?? './src/assets/images/product_placeholder.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-40 object-contain p-3 bg-gray-50 transition-transform duration-300 group-hover:scale-105">
                                <?php if ($discount_exists): ?>
                                    <?php $discount_percentage = round((($original_price - $price) / $original_price) * 100); ?>
                                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">-<?= $discount_percentage ?>%</div>
                                <?php endif; ?>
                            </a>
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-800 truncate" title="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-base font-bold text-gray-900"><?= number_format($price, 0) ?> Frw</span>
                                    <?php if ($discount_exists): ?>
                                        <span class="text-xs text-gray-500 line-through"><?= number_format($original_price, 0) ?> Frw</span>
                                    <?php endif; ?>
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
    // Keep existing handleCartAction, updateCartCount, showToast functions

    function removeFromWishlist(button, productId, userId) {
        if (!userId) return;

        const itemElement = document.getElementById(`wishlist-item-${productId}`);

        fetch(`./src/services/wishlist/remove_item.php?product_id=${productId}&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (itemElement) {
                        itemElement.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        itemElement.style.opacity = '0';
                        itemElement.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            itemElement.remove();
                            // Reloading is the simplest way to handle pagination correctly
                            // after an item is removed.
                            location.reload();
                        }, 500);
                    }
                    showToast(data.message, 'success');
                    updateWishlistCount(userId); // Update count in navbar
                } else {
                    showToast(data.message || 'Failed to remove item.', 'error');
                }
            })
            .catch(error => {
                console.error('Remove from wishlist failed:', error);
                showToast('Request failed. Please try again.', 'error');
            });
    }

    function clearWishlist(userId) {
        if (!userId) return;

        if (confirm('Are you sure you want to clear your entire wishlist?')) {
            fetch(`./src/services/wishlist/remove_item.php?user_id=${userId}&clear_all=true`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Wishlist cleared successfully.', 'success');
                        // Reload the page to show the empty state
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Failed to clear wishlist.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Clear wishlist failed:', error);
                    showToast('Request failed. Please try again.', 'error');
                });
        }
    }

    // Ensure these functions are globally available or imported
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

    // These functions should also exist from the previous steps
    // handleCartAction(productId, userId, price)
    // updateCartCount(userId)
    // showToast(message, type)

    function handleWishlistAction(button, productId, userId) {
        if (!userId) {
            showLoginModal(); // This function should be defined elsewhere
            return;
        }

        const isInWishlist = button.getAttribute('data-in-wishlist') === 'true';
        const url = isInWishlist ?
            `./src/services/wishlist/remove_item.php?product_id=${productId}&user_id=${userId}`
            // For adding, we reuse the existing new_wishlist script
            :
            `./src/services/wishlist/new_wishlist.php?product=${productId}&user=${userId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    updateWishlistCount(userId);

                    const icon = button.querySelector('i');
                    // If it's a suggestion button, visually update it without a page reload
                    if (button.closest('aside')) {
                        button.setAttribute('data-in-wishlist', 'true');
                        button.classList.add('bg-red-500', 'text-white');
                        button.classList.remove('bg-gray-200', 'text-gray-600');
                        icon.classList.add('bi-heart-fill');
                        icon.classList.remove('bi-heart');
                        button.onclick = null; // Disable further clicks to prevent errors
                    } else {
                        // If it's a main wishlist item, remove it from the DOM
                        const itemElement = document.getElementById(`wishlist-item-${productId}`);
                        if (itemElement) {
                            itemElement.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            itemElement.style.opacity = '0';
                            itemElement.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        }
                    }
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(error => {
                console.error('Wishlist action failed:', error);
                showToast('Request failed. Please try again.', 'error');
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