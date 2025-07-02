<?php
// Database connection is already included in layout.php
// No need to include config.php again

$categories = array();

if (isset($conn) && $conn) {
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
        $get_categories = mysqli_query($conn, "SELECT * FROM categories");
        if ($get_categories) {
            while ($category = mysqli_fetch_assoc($get_categories)) {
                $all_categories[] = $category;
            }
        }
    }
    $categories = $all_categories;
    $top_categories = array_slice($all_categories, 0, 3);
    $other_categories = array_slice($all_categories, 3);
} else {
    $top_categories = [];
    $other_categories = [];
}
?>
<div>
    <div class="w-full bg-[whitesmoke] px-4 sm:px-6 md:px-8 py-2 md:py-3 flex flex-wrap md:flex-nowrap items-center justify-between gap-4 md:gap-8 sticky top-0 z-[100001] border-b border-gray-200" style="backdrop-filter: blur(8px);">
        <!-- Logo -->
        <div onclick="window.location.replace('home')" class="flex cursor-pointer items-center gap-2 md:gap-3 flex-shrink-0 w-full md:w-auto justify-center md:justify-start">
            <img src="./src/assets/icons/kt_logo_2.png" alt="KT Phones Logo" class="h-10 sm:h-12 w-auto drop-shadow-md" />
            <span class="text-blue-400 text-lg sm:text-2xl font-extrabold tracking-widest ml-1">KT Phones</span>
        </div>
        <!-- Search Bar -->
        <?php
            include './src/components/search.php';
        ?>
        <!-- Icons -->
        <div class="flex items-center gap-3 sm:gap-5 w-full md:w-auto justify-center md:justify-end mt-2 md:mt-0">
            <?php if (isset($is_logged_in) && $is_logged_in): ?>
                <button onclick="window.location.replace('wishlist')" class="relative p-2 text-gray-600 hover:text-red-500 transition-colors duration-300 group cursor-pointer">
                    <i class="bi bi-heart text-xl sm:text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="wishlist-count absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full px-1.5 py-0.5 font-bold shadow"></span>
                </button>
            <?php endif; ?>
            <button onclick="window.location.replace('cart')" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors duration-300 group cursor-pointer">
                <i class="bi bi-cart text-xl sm:text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                <span class="cart-count absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full px-1.5 py-0.5 font-bold shadow"></span>
            </button>
            <?php if (isset($is_logged_in) && $is_logged_in): ?>
                <div class="relative">
                    <button id="userMenuToggle" class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                            <span class="text-white font-bold text-lg"><?php echo strtoupper(substr($customer_name ?? 'U', 0, 1)); ?></span>
                        </div>
                    </button>
                    <div id="userMenuDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible transform scale-95 transition-all duration-200 z-50 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800"><?php echo $customer_name ?? 'User'; ?></h3>
                            <?php if (isset($customer_email)): ?>
                                <p class="text-sm text-gray-500 truncate"><?php echo $customer_email; ?></p>
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
                <div>
                <a href="signin" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-300 transform hover:scale-105">
                    Sign In
                </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Main Navigation Bar -->
    <div style="z-index: 100000;" class="sticky top-0 w-[100%] flex items-center justify-between px-12 bg-white/70 backdrop-blur-md border-b border-gray-200 py-5">
        <!-- Category Navigation: Responsive -->
        <div class="flex gap-10 items-center">
            <!-- Desktop: Inline categories -->
            <div class="hidden md:flex gap-10">
                <?php foreach ($categories as $cat): ?>
                    <a href="category?category=<?php echo urlencode($cat['slug']); ?>" class="text-gray-900 text-base font-medium tracking-wide hover:text-blue-500 transition"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endforeach; ?>
            </div>
            <!-- Mobile: Dropdown menu -->
            <div class="md:hidden relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-full shadow-sm text-gray-900 font-medium hover:bg-gray-100 transition">
                    <i class="bi bi-list text-xl"></i>
                    Categories
                    <i :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" class="bi ml-1 text-base"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50 py-2">
            <?php foreach ($categories as $cat): ?>
                        <a href="category?category=<?php echo urlencode($cat['slug']); ?>"
                           class="block px-5 py-3 text-gray-800 hover:bg-blue-50 hover:text-blue-600 transition rounded-lg">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
            <?php endforeach; ?>
                </div>
            </div>
        </div>
        <button onclick="window.location.replace('store')" class="bg-blue-500 text-white font-bold rounded-full px-8 py-3 text-base shadow-md hover:bg-white hover:text-blue-500 border-2 border-transparent hover:border-blue-500 transition">Shop Now</button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (userMenuDropdown && !userMenuDropdown.classList.contains('invisible') && userMenuToggle && !userMenuToggle.contains(event.target)) {
                userMenuDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
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
        updateCounts();
        setInterval(updateCounts, 3000); // 3 seconds
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>