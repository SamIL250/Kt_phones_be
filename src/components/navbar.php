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
                            <span class="wishlist-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
                        </button>
                    <?php endif; ?>

                    <!-- Cart -->
                    <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group cursor-pointer">
                        <i class="bi bi-cart text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="cart-count absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
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
                    <?php if (isset($is_logged_in) && $is_logged_in): ?>
                        <button onclick="window.location.replace('wishlist')" class="relative p-2 text-gray-600 hover:text-red-500 transition-colors duration-300 group cursor-pointer">
                            <i class="bi bi-heart text-xl"></i>
                            <span class="wishlist-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
                        </button>
                    <?php endif; ?>
                    <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group cursor-pointer">
                        <i class="bi bi-cart text-xl"></i>
                        <span class="cart-count absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
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
            const customerId = <?php echo isset($customer_id) ? json_encode($customer_id) : 'null'; ?>;

            // Update wishlist count (if logged in)
            if (isUserLoggedIn && customerId) {
                fetch(`./src/services/wishlist/wishlist_count.php?customer_id=${encodeURIComponent(customerId)}&is_logged_in=true`)
                    .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch'))
                    .then(data => {
                        const wishlistCount = data.count || 0;
                        document.querySelectorAll('.wishlist-count').forEach(el => { el.innerText = wishlistCount; });
                    })
                    .catch(err => console.error("Error fetching wishlist count:", err));
            }

            // Update cart count for both logged-in and guest users
            fetch(`./src/services/cart/cart_count.php?customer_id=${encodeURIComponent(customerId || '')}&is_logged_in=${isUserLoggedIn ? 'true' : 'false'}`)
                .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch'))
                .then(data => {
                    const cartCount = data.count || 0;
                    document.querySelectorAll('.cart-count').forEach(el => { el.innerText = cartCount; });
                })
                .catch(err => console.error("Error fetching cart count:", err));
        }

        // Update counts on page load and periodically
        updateCounts();
        setInterval(updateCounts, 300); // 5 seconds
    });
</script>