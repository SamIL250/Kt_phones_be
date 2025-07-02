<?php
// Include database connection and fetch popular products
include __DIR__ . '/../../../../config/config.php';

// Function to fetch popular products (can be moved to a helper file)
function getPopularProducts($conn, $limit = 6)
{
    $sql = "SELECT 
            p.product_id,
            p.name AS product_name,
            p.base_price,
            p.discount_price,
            GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.display_order SEPARATOR ', ') AS image_urls
            FROM products p
            LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
            WHERE p.is_active = 1 AND p.published = 'true'
            GROUP BY p.product_id
            ORDER BY p.stock_quantity DESC, p.created_at DESC
            LIMIT " . (int)$limit;

    $result = mysqli_query($conn, $sql);
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['primary_image'] = !empty($row['image_urls']) ? explode(', ', $row['image_urls'])[0] : './src/assets/images/product_placeholder.png';
            $products[] = $row;
        }
    }
    return $products;
}

$popular_products = getPopularProducts($conn, 6);
?>

<!-- Enhanced Signup Page -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Top Section with Hero Design -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'2\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <div class="flex items-center gap-3 bg-white/20 backdrop-blur-sm rounded-full px-6 py-3">
                        <img src="./src/assets/icons/kt_logo.png" class="w-[50px]" alt="KT-Phones">
                        <span class="text-white font-bold text-xl">KT-Phones</span>
                    </div>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">
                    Create Your Account
                </h1>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Join our community to enjoy exclusive deals and a seamless shopping experience.
                </p>
            </div>
        </div>
    </div>

    <!-- Signup Form Section -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="bg-white rounded-2xl shadow-md border border-gray-300 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left Side - Welcome -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 lg:p-12">
                    <div class="h-full flex flex-col justify-center">
                        <div class="text-center lg:text-left">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-6">
                                <i class="bi bi-person-plus text-white text-2xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                                Start Your Journey With Us
                            </h2>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Create an account to get started. Already have an account? Log in to access your dashboard and continue shopping.
                            </p>
                            <button type="button" onclick="window.location.replace('signin')"
                                class="w-full lg:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-300 transform hover:scale-105">
                                Sign In
                            </button>
                        </div>
            </div>
                </div>

                <!-- Right Side - Signup Form -->
                <div class="p-8 lg:p-12">
                    <div class="max-w-sm mx-auto">
                        <div class="text-center lg:text-left mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Sign Up</h3>
                            <p class="text-gray-600">It's quick and easy!</p>
                        </div>

                        <form action="./src/services/auth/signup.php" method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" name="fname" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        placeholder="John">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="lname" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        placeholder="Doe">
                                </div>
                </div>
                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    placeholder="you@example.com">
                </div>
                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    placeholder="Create a password">
                </div>
                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" name="confirm_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                    placeholder="Confirm your password">
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 transform hover:scale-105">
                                    Create Account
                                </button>
                </div>

                            <p class="text-xs text-gray-500 text-center pt-2">
                                By creating an account, you agree to our <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Products Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Products</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Check out some of our most popular items
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php foreach ($popular_products as $product): ?>
                <?php
                $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                $original_price = $product['base_price'];
                $discount_exists = $product['discount_price'] > 0;
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

        <!-- View All Products Button -->
        <div class="text-center mt-8">
            <button onclick="window.location.replace('store')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors duration-300 flex items-center gap-2 mx-auto">
                View All Products
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>