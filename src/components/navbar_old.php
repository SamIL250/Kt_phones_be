<?php
// Database connection is already included in layout.php
// No need to include config.php again

$categories = array();

// Check if connection is successful before querying
if (isset($conn) && $conn) {
    $get_categories = mysqli_query($conn, "SELECT * FROM categories");

    if ($get_categories) {
        while ($category = mysqli_fetch_assoc($get_categories)) {
            $categories[] = $category;
        }
    } else {
        // Log error but don't break the page
        error_log("MySQL query error: " . mysqli_error($conn));
    }
} else {
    // Log connection error but don't break the page
    error_log("Database connection failed in navbar.php");
}
?>

<!-- Add Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Modern Navigation Bar -->
<nav class="sticky top-0 bg-white/95 backdrop-blur-lg border-b border-gray-200 shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div onclick="window.location.replace('home')" class="flex items-center cursor-pointer group">
                <img src="./src/assets/icons/kt_logo_2.png" class="w-10 h-10 mr-3 group-hover:scale-110 transition-transform duration-300" alt="KT Phones Logo">
                <div class="hidden sm:block">
                    <h1 class="text-xl font-bold text-gray-900">KT Phones</h1>
                    <p class="text-xs text-gray-500">Premium Smartphones</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8">
                <!-- Main Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="store" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300 relative group">
                        Store
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="category?category=<?= $category['slug'] ?>" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300 relative group">
                            <?= $category['name'] ?>
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Wishlist -->
                    <?php if ($is_logged_in): ?>
                        <button onclick="window.location.replace('wishlist')" class="relative p-2 text-gray-600 hover:text-red-500 transition-colors duration-300 group">
                            <i class="bi bi-heart text-xl group-hover:scale-110 transition-transform duration-300"></i>
                            <span id="wishlist-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php
                                $wishlist_count = 0;
                                if ($is_logged_in) {
                                    $wishlist_query = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM wishlists w JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id WHERE w.user_id = ?");
                                    if ($wishlist_query) {
                                        mysqli_stmt_bind_param($wishlist_query, "s", $customer_id);
                                        mysqli_stmt_execute($wishlist_query);
                                        $result = mysqli_stmt_get_result($wishlist_query);
                                        if ($row = mysqli_fetch_assoc($result)) {
                                            $wishlist_count = $row['count'];
                                        }
                                        mysqli_stmt_close($wishlist_query);
                                    }
                                }
                                echo $wishlist_count;
                                ?>
                            </span>
                        </button>
                    <?php endif; ?>

                    <!-- Cart -->
                    <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group">
                        <i class="bi bi-cart text-xl group-hover:scale-110 transition-transform duration-300"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            <?php
                            $cart_count = 0;
                            if ($is_logged_in) {
                                $cart_query = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
                                if ($cart_query) {
                                    mysqli_stmt_bind_param($cart_query, "s", $customer_id);
                                    mysqli_stmt_execute($cart_query);
                                    $result = mysqli_stmt_get_result($cart_query);
                                    if ($row = mysqli_fetch_assoc($result)) {
                                        $cart_count = $row['count'];
                                    }
                                    mysqli_stmt_close($cart_query);
                                }
                            } else {
                                if (isset($_COOKIE['guest_cart'])) {
                                    $cart_data = json_decode($_COOKIE['guest_cart'], true);
                                    if (is_array($cart_data)) {
                                        foreach ($cart_data as $item) {
                                            $cart_count += isset($item['quantity']) ? $item['quantity'] : 1;
                                        }
                                    }
                                }
                            }
                            echo $cart_count;
                            ?>
                        </span>
                    </button>

                    <!-- User Menu -->
                    <?php if ($is_logged_in): ?>
                        <div class="relative">
                            <button id="userMenuToggle" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <span class="text-gray-700 font-medium"><?= $customer_name ?? 'User' ?></span>
                                <i class="bi bi-chevron-down text-gray-500"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userMenuDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 opacity-0 invisible transform scale-95 transition-all duration-200 z-50">
                                <div class="py-2">
                                    <a href="profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-person mr-3"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="wishlist" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-heart mr-3"></i>
                                        <span>My Wishlist</span>
                                    </a>
                                    <a href="cart" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-300">
                                        <i class="bi bi-cart mr-3"></i>
                                        <span>My Cart</span>
                                    </a>
                                    <hr class="my-2 border-gray-200">
                                    <a href="./src/services/auth/sign_out.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-300">
                                        <i class="bi bi-box-arrow-right mr-3"></i>
                                        <span>Sign Out</span>
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
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button id="mobileMenuToggle" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-300">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="lg:hidden hidden bg-white border-t border-gray-200">
        <div class="px-4 py-6 space-y-4">
            <!-- Mobile Navigation Links -->
            <div class="space-y-3">
                <a href="store" class="block py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">Store</a>
                <?php foreach ($categories as $category): ?>
                    <a href="category?category=<?= $category['slug'] ?>" class="block py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">
                        <?= $category['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Mobile User Actions -->
            <div class="pt-4 border-t border-gray-200">
                <?php if ($is_logged_in): ?>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-700 font-medium"><?= $customer_name ?? 'User' ?></span>
                        <a href="./src/services/auth/sign_out.php" class="text-red-600 hover:text-red-700">Sign Out</a>
                    </div>
                    <div class="flex space-x-4 py-2">
                        <a href="profile" class="flex items-center text-gray-700 hover:text-blue-600">
                            <i class="bi bi-person mr-2"></i>
                            <span>Profile</span>
                        </a>
                        <a href="wishlist" class="flex items-center text-gray-700 hover:text-blue-600">
                            <i class="bi bi-heart mr-2"></i>
                            <span>Wishlist</span>
                        </a>
                        <a href="cart" class="flex items-center text-gray-700 hover:text-blue-600">
                            <i class="bi bi-cart mr-2"></i>
                            <span>Cart</span>
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
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // User menu toggle
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userMenuDropdown = document.getElementById('userMenuDropdown');

    if (userMenuToggle && userMenuDropdown) {
        userMenuToggle.addEventListener('click', () => {
            userMenuDropdown.classList.toggle('opacity-0');
            userMenuDropdown.classList.toggle('invisible');
            userMenuDropdown.classList.toggle('scale-95');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!userMenuToggle.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                userMenuDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
            }
        });
    }

    // Update cart and wishlist counts
    function updateCounts() {
        const customerId = <?= (int)$customer_id ?>;

        if (customerId > 0) {
            // Update wishlist count
            fetch(`./src/services/wishlist/wishlist_count.php?user=${customerId}`)
                .then(res => res.json())
                .then(data => {
                    const wishlistElement = document.getElementById("wishlist-count");
                    if (wishlistElement && data.wishlist_count !== undefined) {
                        wishlistElement.innerText = data.wishlist_count;
                    }
                })
                .catch(err => console.error("Error fetching wishlist count:", err));

            // Update cart count
            fetch(`./src/services/cart/cart_count.php?user=${customerId}`)
                .then(res => res.json())
                .then(data => {
                    const cartElement = document.getElementById("cart-count");
                    if (cartElement && data.cart_count !== undefined) {
                        cartElement.innerText = data.cart_count;
                    }
                })
                .catch(err => console.error("Error fetching cart count:", err));
        }
    }

    // Update counts on page load and periodically
    document.addEventListener('DOMContentLoaded', updateCounts);
    setInterval(updateCounts, 5000);
</script>