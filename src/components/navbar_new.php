<?php
// Database connection is already included in layout.php
// No need to include config.php again

$categories = array();

// Check if connection is successful before querying
if (isset($conn) && $conn) {
    // Get all categories with product counts
    $all_categories_query = mysqli_query($conn, "
        SELECT c.name, c.slug, COUNT(p.product_id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.category_id = p.category_id
        GROUP BY c.category_id
        ORDER BY product_count DESC
    ");

    $all_categories = [];
    if ($all_categories_query) {
        while ($row = mysqli_fetch_assoc($all_categories_query)) {
            $all_categories[] = $row;
        }
    } else {
        // Fallback to simple query if the complex one fails
        error_log("Product count query failed: " . mysqli_error($conn));
        $get_categories = mysqli_query($conn, "SELECT * FROM categories");
        if ($get_categories) {
            while ($category = mysqli_fetch_assoc($get_categories)) {
                $all_categories[] = $category;
            }
        }
    }

    $categories = $all_categories; // For other components that rely on this variable
    $top_categories = array_slice($all_categories, 0, 3);
    $other_categories = array_slice($all_categories, 3);
} else {
    // Log connection error but don't break the page
    error_log("Database connection failed in navbar.php");
    $top_categories = [];
    $other_categories = [];
}
?>

<!-- Add Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Announcement Bar -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-center py-2 px-4 shadow-md">
    <p class="text-sm font-medium animate-pulse">
        🚀 Now shipping internationally! Free delivery on orders over $100.
    </p>
</div>

<!-- Modern Navigation Bar -->
<nav id="main-navbar" class="sticky top-0 bg-white/95 backdrop-blur-lg border-b border-gray-200 shadow-sm z-50 transition-opacity duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <div onclick="window.location.replace('home')" class="flex items-center cursor-pointer group">
                    <img src="./src/assets/icons/kt_logo_2.png" class="w-10 h-10 mr-3 group-hover:scale-110 transition-transform duration-300" alt="KT Phones Logo">
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-bold text-gray-900">KT Phones</h1>
                        <p class="text-xs text-gray-500">Premium Smartphones</p>
                    </div>
                </div>
            </div>

            <!-- Search Bar (centered, always visible) -->
            <div class="flex-1 flex items-center justify-center px-2 mobile-search-bar desktop-search-bar">
                <?php include './src/components/search.php'; ?>
            </div>

            <!-- Desktop Navigation - Centered -->
            <div class="hidden lg:flex lg:flex-1 lg:justify-center lg:items-center">
                <div class="flex items-center space-x-6">
                    <a href="store" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300 relative group">
                        Store
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <?php foreach ($top_categories as $category): ?>
                        <a href="category?category=<?= htmlspecialchars($category['slug']) ?>" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300 relative group">
                            <?= htmlspecialchars($category['name']) ?>
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    <?php endforeach; ?>

                    <?php if (!empty($other_categories)): ?>
                        <div class="relative" id="categories-dropdown-container">
                            <button id="categories-dropdown-toggle" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300 relative group flex items-center cursor-pointer">
                                More
                                <i class="bi bi-chevron-down text-xs ml-1"></i>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                            </button>
                            <div id="categories-dropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 opacity-0 invisible transform scale-95 transition-all duration-200 z-50">
                                <div class="py-2">
                                    <?php foreach ($other_categories as $category): ?>
                                        <a href="category?category=<?= htmlspecialchars($category['slug']) ?>" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Actions & Mobile Toggle -->
            <div class="flex items-center">
                <!-- Desktop User Actions -->
                <div class="hidden lg:flex items-center space-x-4">
                    <!-- Wishlist -->
                    <?php if (isset($is_logged_in) && $is_logged_in): ?>
                        <button onclick="window.location.replace('wishlist')" class="relative p-2 text-gray-600 hover:text-red-500 transition-colors duration-300 group cursor-pointer">
                            <i class="bi bi-heart text-xl group-hover:scale-110 transition-transform duration-300"></i>
                            <span id="wishlist-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php
                                $wishlist_count = 0;
                                if (isset($is_logged_in) && $is_logged_in && isset($customer_id)) {
                                    $wishlist_query_str = "SELECT COUNT(wi.wishlist_item_id) as count FROM wishlists w JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id WHERE w.user_id = ?";
                                    $wishlist_query = mysqli_prepare($conn, $wishlist_query_str);
                                    if ($wishlist_query) {
                                        mysqli_stmt_bind_param($wishlist_query, "s", $customer_id);
                                        mysqli_stmt_execute($wishlist_query);
                                        $result = mysqli_stmt_get_result($wishlist_query);
                                        if ($row = mysqli_fetch_assoc($result)) {
                                            $wishlist_count = $row['count'] ?? 0;
                                        }
                                        mysqli_stmt_close($wishlist_query);
                                    } else {
                                        error_log("Wishlist count query failed: " . mysqli_error($conn));
                                    }
                                }
                                echo $wishlist_count;
                                ?>
                            </span>
                        </button>
                    <?php endif; ?>

                    <!-- Cart -->
                    <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group cursor-pointer">
                        <i class="bi bi-cart text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            <?php
                            $cart_count = 0;
                            if (isset($is_logged_in) && $is_logged_in && isset($customer_id)) {
                                $cart_query_str = "SELECT SUM(ci.quantity) as total FROM cart c JOIN cart_item ci ON c.cart_id = ci.cart_id WHERE c.user_id = ?";
                                $cart_query = mysqli_prepare($conn, $cart_query_str);
                                if ($cart_query) {
                                    mysqli_stmt_bind_param($cart_query, "s", $customer_id);
                                    mysqli_stmt_execute($cart_query);
                                    $result = mysqli_stmt_get_result($cart_query);
                                    if ($row = mysqli_fetch_assoc($result)) {
                                        $cart_count = (int)($row['total'] ?? 0);
                                    }
                                    mysqli_stmt_close($cart_query);
                                } else {
                                    error_log("Cart count query failed: " . mysqli_error($conn));
                                }
                            } else {
                                if (isset($_COOKIE['guest_cart'])) {
                                    $cart_data = json_decode($_COOKIE['guest_cart'], true);
                                    if (is_array($cart_data)) {
                                        $cart_count = array_sum(array_column($cart_data, 'quantity'));
                                    }
                                }
                            }
                            echo $cart_count;
                            ?>
                        </span>
                    </button>

                    <!-- User Menu -->
                    <?php if (isset($is_logged_in) && $is_logged_in): ?>
                        <div class="relative">
                            <button id="userMenuToggle" class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-lg"><?= strtoupper(substr($customer_name ?? 'U', 0, 1)) ?></span>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userMenuDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible transform scale-95 transition-all duration-200 z-50 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-800"><?= $customer_name ?? 'User' ?></h3>
                                    <?php if (isset($customer_email)): ?>
                                        <p class="text-sm text-gray-500 truncate"><?= $customer_email ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="py-2">
                                    <a href="profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-person-circle w-6 text-xl mr-3 text-gray-500"></i>
                                        <span class="font-medium">My Profile</span>
                                    </a>
                                    <a href="wishlist" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-heart-fill w-6 text-xl mr-3 text-gray-500"></i>
                                        <span class="font-medium">My Wishlist</span>
                                    </a>
                                    <a href="cart" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-cart-check-fill w-6 text-xl mr-3 text-gray-500"></i>
                                        <span class="font-medium">My Cart</span>
                                    </a>
                                    <hr class="my-2 border-gray-100">
                                    <a href="./src/services/auth/sign_out.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-300">
                                        <i class="bi bi-box-arrow-right w-6 text-xl mr-3"></i>
                                        <span class="font-medium">Sign Out</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="signin" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-300 transform hover:scale-105">
                            Sign In
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Cart and Menu Toggle -->
                <div class="lg:hidden flex items-center">
                    <!-- Mobile Search Icon -->
                    <button id="mobileSearchToggle" class="p-2 rounded-lg text-gray-600 hover:text-blue-600 transition-colors duration-300 cursor-pointer ml-1" aria-label="Open search">
                        <i class="bi bi-search text-2xl"></i>
                    </button>
                    <?php if (isset($is_logged_in) && $is_logged_in): ?>
                        <button onclick="window.location.replace('wishlist')" class="relative p-2 text-gray-600 hover:text-red-500 transition-colors duration-300 group cursor-pointer">
                            <i class="bi bi-heart text-xl"></i>
                            <span id="mobile-wishlist-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php echo $wishlist_count; ?>
                            </span>
                        </button>
                    <?php endif; ?>
                    <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group cursor-pointer">
                        <i class="bi bi-cart text-xl"></i>
                        <span id="mobile-cart-count" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            <?php echo $cart_count; ?>
                        </span>
                    </button>
                    <button id="mobileMenuToggle" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-300 cursor-pointer">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-gray-200">
        <div class="px-4 pt-4 pb-6 space-y-6">
            <!-- Mobile Navigation Links -->
            <div class="space-y-1">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider px-3 flex items-center">
                    <i class="bi bi-grid-3x3-gap-fill mr-2"></i>
                    Shop by Category
                </h3>
                <p class="text-sm text-gray-500 px-3 mt-1 mb-3">Explore our wide range of smartphone categories and find the perfect device for your needs.</p>

                <div class="grid grid-cols-2 gap-3 px-2">
                    <a href="store" class="flex flex-col items-center justify-center text-center p-3 text-gray-700 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                        <i class="bi bi-phone text-2xl mb-1 text-blue-500"></i>
                        <span class="text-sm font-medium">All Products</span>
                    </a>
                    <?php foreach ($all_categories as $category): ?>
                        <a href="category?category=<?= htmlspecialchars($category['slug']) ?>" class="flex flex-col items-center justify-center text-center p-3 text-gray-700 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                            <i class="bi bi-tag-fill text-2xl mb-1 text-blue-500"></i>
                            <span class="text-sm font-medium"><?= htmlspecialchars($category['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mobile User Actions -->
            <div class="pt-6 border-t border-gray-200">
                <?php if (isset($is_logged_in) && $is_logged_in): ?>
                    <div class="flex items-center justify-between py-2 px-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md mr-3">
                                <span class="text-white font-bold text-base"><?= strtoupper(substr($customer_name ?? 'U', 0, 1)) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-800 font-semibold block"><?= $customer_name ?? 'User' ?></span>
                                <a href="./src/services/auth/sign_out.php" class="text-xs font-medium text-red-600 hover:text-red-700">Sign Out</a>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1 mt-2">
                        <a href="profile" class="flex items-center justify-between text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-md p-3 transition-colors duration-200">
                            <span class="text-base font-medium">My Profile</span>
                            <i class="bi bi-chevron-right text-sm"></i>
                        </a>
                        <a href="wishlist" class="flex items-center justify-between text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-md p-3 transition-colors duration-200">
                            <span class="text-base font-medium">My Wishlist</span>
                            <i class="bi bi-chevron-right text-sm"></i>
                        </a>
                        <a href="cart" class="flex items-center justify-between text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-md p-3 transition-colors duration-200">
                            <span class="text-base font-medium">My Cart</span>
                            <i class="bi bi-chevron-right text-sm"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="signin" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-semibold transition-colors duration-300">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Search Modal -->
<div id="mobileSearchModal" class="fixed inset-0 z-[100002] bg-blue-500 bg-opacity-30 flex items-start justify-center pt-24 px-2 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto p-6 relative animate-fade-in">
        <button id="closeMobileSearch" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none" aria-label="Close search">
            <i class="bi bi-x-lg"></i>
        </button>
        <form class="flex w-full relative" action="store" method="get" role="search" onsubmit="document.getElementById('mobileSearchModal').classList.add('hidden');" autocomplete="off">
            <input id="mobile-search-input" type="text" name="q" placeholder="Search for products, brands, categories..." class="flex-1 rounded-l-full border border-gray-300 px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white min-w-0" aria-label="Search" autofocus required autocomplete="off">
            <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-r-full font-semibold text-base hover:bg-blue-600 transition-colors" aria-label="Search">
                Search
            </button>
            <div id="mobile-search-results-dropdown" class="absolute left-0 right-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden">
                <div id="mobile-search-loading" class="flex items-center justify-center py-6 text-blue-500 hidden">
                    <span class="spinner-border animate-spin inline-block w-6 h-6 border-4 rounded-full border-blue-400 border-t-transparent"></span>
                    <span class="ml-2">Searching...</span>
                </div>
                <ul id="mobile-search-results-list" class="divide-y divide-gray-100"></ul>
                <div id="mobile-search-more-less" class="flex justify-center py-2"></div>
            </div>
        </form>
    </div>
</div>

<style>
@media (max-width: 640px) {
    .mobile-search-bar { display: none !important; }
}
@keyframes fade-in {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.2s ease; }

/* Modal backdrop blue for mobile search */
#mobileSearchModal {
    background-color: rgba(59, 130, 246, 0.18) !important; /* blue-500 with more transparency */
}

/* Force desktop search bar to be wide and slim */
@media (min-width: 1024px) {
    .desktop-search-bar,
    .desktop-search-bar > div,
    .desktop-search-bar form {
        max-width: 340px !important;
        width: 100% !important;
        min-width: 0 !important;
        margin-left: 20px !important;
        margin-right: auto !important;
        background: white !important;
        min-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        padding: 0 !important;
    }
    .desktop-search-bar input[type="text"] {
        height: 1.6rem !important;
        line-height: 1.6rem !important;
        min-height: 0 !important;
        min-width: 120px !important;
        box-sizing: border-box !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        font-size: 0.97rem !important;
        border-radius: 9999px 0 0 9999px !important;
    }
    .desktop-search-bar button[type="submit"] {
        height: 1.6rem !important;
        line-height: 1.6rem !important;
        min-height: 0 !important;
        box-sizing: border-box !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        font-size: 0.97rem !important;
        border-radius: 0 9999px 9999px 0 !important;
        cursor: pointer !important;
    }
    .desktop-search-bar form {
        border-radius: 9999px !important;
        min-height: unset !important;
        box-shadow: none !important;
        /* background: white !important; */
        padding: 8px !important;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        let isMobileMenuOpen = false;

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                isMobileMenuOpen = !isMobileMenuOpen;
                if (isMobileMenuOpen) {
                    mobileMenu.classList.remove('hidden');
                } else {
                    mobileMenu.classList.add('hidden');
                }
            });
        }

        // User menu toggle
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        if (userMenuToggle && userMenuDropdown) {
            userMenuToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                userMenuDropdown.classList.toggle('opacity-0');
                userMenuDropdown.classList.toggle('invisible');
                userMenuDropdown.classList.toggle('scale-95');
            });
        }

        // Categories dropdown toggle
        const categoriesMenuToggle = document.getElementById('categories-dropdown-toggle');
        const categoriesMenuDropdown = document.getElementById('categories-dropdown');
        if (categoriesMenuToggle && categoriesMenuDropdown) {
            categoriesMenuToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                categoriesMenuDropdown.classList.toggle('opacity-0');
                categoriesMenuDropdown.classList.toggle('invisible');
                categoriesMenuDropdown.classList.toggle('scale-95');
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            // Close user menu
            if (userMenuDropdown && !userMenuDropdown.classList.contains('invisible') && userMenuToggle && !userMenuToggle.contains(event.target)) {
                userMenuDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            }
            // Close categories menu
            if (categoriesMenuDropdown && !categoriesMenuDropdown.classList.contains('invisible') && categoriesMenuToggle && !categoriesMenuToggle.contains(event.target)) {
                categoriesMenuDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            }
            // Close mobile menu when clicking outside
            if (isMobileMenuOpen && mobileMenu && mobileMenuToggle && !mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                isMobileMenuOpen = false;
            }
        });

        // Update cart and wishlist counts
        function updateCounts() {
            const isUserLoggedIn = <?php echo (isset($is_logged_in) && $is_logged_in) ? 'true' : 'false'; ?>;

            if (isUserLoggedIn) {
                const customerId = <?php echo isset($customer_id) ? json_encode($customer_id) : 'null'; ?>;
                if (customerId) {
                    // Update wishlist count
                    fetch(`./src/services/wishlist/wishlist_count.php`)
                        .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch'))
                        .then(data => {
                            const wishlistCount = data.count || 0;
                            const wishlistElement = document.getElementById("wishlist-count");
                            if (wishlistElement) wishlistElement.innerText = wishlistCount;
                            const mobileWishlistElement = document.getElementById("mobile-wishlist-count");
                            if (mobileWishlistElement) mobileWishlistElement.innerText = wishlistCount;
                        })
                        .catch(err => console.error("Error fetching wishlist count:", err));
                }
            }

            // Update cart count for both logged-in and guest users
            fetch(`./src/services/cart/cart_count.php`)
                .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch'))
                .then(data => {
                    const cartCount = data.count || 0;
                    const cartElement = document.getElementById("cart-count");
                    if (cartElement) cartElement.innerText = cartCount;
                    const mobileCartElement = document.getElementById("mobile-cart-count");
                    if (mobileCartElement) mobileCartElement.innerText = cartCount;
                })
                .catch(err => console.error("Error fetching cart count:", err));
        }

        // Update counts on page load and periodically
        updateCounts();
        setInterval(updateCounts, 5000); // 5 seconds

        // Mobile search modal toggle
        const mobileSearchToggle = document.getElementById('mobileSearchToggle');
        const mobileSearchModal = document.getElementById('mobileSearchModal');
        const closeMobileSearch = document.getElementById('closeMobileSearch');
        if (mobileSearchToggle && mobileSearchModal) {
            mobileSearchToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileSearchModal.classList.remove('hidden');
                setTimeout(function() {
                    const input = mobileSearchModal.querySelector('input[name="q"]');
                    if (input) input.focus();
                }, 100);
            });
        }
        if (closeMobileSearch && mobileSearchModal) {
            closeMobileSearch.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileSearchModal.classList.add('hidden');
            });
        }
        // Close modal when clicking outside
        if (mobileSearchModal) {
            mobileSearchModal.addEventListener('click', function(e) {
                if (e.target === mobileSearchModal) {
                    mobileSearchModal.classList.add('hidden');
                }
            });
        }

        // --- Mobile search autocomplete logic ---
        (function() {
            const input = document.getElementById('mobile-search-input');
            const dropdown = document.getElementById('mobile-search-results-dropdown');
            const resultsList = document.getElementById('mobile-search-results-list');
            const loading = document.getElementById('mobile-search-loading');
            const moreLess = document.getElementById('mobile-search-more-less');
            let results = [];
            let showingAll = false;
            let lastQuery = '';
            let totalResults = 0;
            let debounceTimeout;

            function renderResults(items, total) {
                resultsList.innerHTML = '';
                if (!items.length) {
                    resultsList.innerHTML = '<li class="py-6 text-center text-gray-400">No results found</li>';
                    moreLess.innerHTML = '';
                    return;
                }
                items.forEach(item => {
                    resultsList.innerHTML += `
                        <li class="flex items-center gap-4 px-4 py-3 hover:bg-blue-50 cursor-pointer transition" onclick="window.location='product-view?product=${item.product_id}'">
                            <img src="${item.primary_image}" alt="${item.product_name}" class="w-12 h-12 object-cover rounded-md border border-gray-200 flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 truncate">${item.product_name}</div>
                                <div class="text-xs text-gray-500 truncate">${item.brand_name || ''} ${item.category_name ? '· ' + item.category_name : ''}</div>
                                <div class="text-xs text-blue-500 font-bold mt-1">${item.discount_price > 0 ? Number(item.discount_price).toLocaleString() : Number(item.base_price).toLocaleString()} Frw</div>
                            </div>
                            <div class="flex flex-col items-end ml-2">
                                <span class="text-yellow-400 text-sm flex items-center">${'★'.repeat(Math.round(item.avg_rating))}${'☆'.repeat(5-Math.round(item.avg_rating))}</span>
                                <span class="text-xs text-gray-400">${item.num_reviews} reviews</span>
                            </div>
                        </li>
                    `;
                });
                // More/Less button
                if (total > items.length) {
                    moreLess.innerHTML = `<button type="button" class="text-blue-500 hover:underline font-semibold" id="mobile-show-more-btn">Show more</button>`;
                } else if (items.length > 5) {
                    moreLess.innerHTML = `<button type="button" class="text-blue-500 hover:underline font-semibold" id="mobile-show-less-btn">Show less</button>`;
                } else {
                    moreLess.innerHTML = '';
                }
            }

            function showDropdown() {
                dropdown.classList.remove('hidden');
            }
            function hideDropdown() {
                dropdown.classList.add('hidden');
            }
            function setLoading(isLoading) {
                loading.classList.toggle('hidden', !isLoading);
                resultsList.classList.toggle('hidden', isLoading);
                moreLess.classList.toggle('hidden', isLoading);
            }

            async function fetchResults(query, limit = 5) {
                setLoading(true);
                try {
                    const res = await fetch(`./src/services/products/filter.php?search=${encodeURIComponent(query)}&page=1&limit=${limit}`);
                    const data = await res.json();
                    results = data.results || [];
                    totalResults = typeof data.total === 'number' ? data.total : results.length;
                    renderResults(results, totalResults);
                    showDropdown();
                } catch (e) {
                    resultsList.innerHTML = '<li class="py-6 text-center text-gray-400">Error searching</li>';
                    moreLess.innerHTML = '';
                    showDropdown();
                } finally {
                    setLoading(false);
                }
            }

            input.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                lastQuery = query;
                showingAll = false;
                if (debounceTimeout) clearTimeout(debounceTimeout);
                if (!query) {
                    hideDropdown();
                    return;
                }
                debounceTimeout = setTimeout(() => fetchResults(query, 5), 250);
            });

            // Show more/less logic
            moreLess.addEventListener('click', function(e) {
                if (e.target.id === 'mobile-show-more-btn') {
                    showingAll = true;
                    fetchResults(lastQuery, 20); // Show up to 20
                } else if (e.target.id === 'mobile-show-less-btn') {
                    showingAll = false;
                    fetchResults(lastQuery, 5);
                }
            });

            // Hide dropdown on outside click
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== input) {
                    hideDropdown();
                }
            });
            // Show dropdown on focus if there are results
            input.addEventListener('focus', function() {
                if (results.length) showDropdown();
            });
        })();
        // --- end mobile search autocomplete ---
    });
</script>