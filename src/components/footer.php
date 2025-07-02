<!-- Trust Badges Section -->
<div class="bg-white border-y border-gray-200 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Why Trust KT Phones?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">We're committed to providing the best shopping experience with premium products and exceptional service.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-truck text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Free Shipping</h3>
                <p class="text-gray-600 text-sm">Free delivery on orders over 500,000 Frw</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-shield-check text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Secure Payment</h3>
                <p class="text-gray-600 text-sm">100% secure payment processing</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-arrow-clockwise text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Easy Returns</h3>
                <p class="text-gray-600 text-sm">30-day return policy for all products</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-headset text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Get help and support from our team anytime</p>
            </div>
        </div>
    </div>
</div>

<?php
// Fetch other products (not phones or Apple products)
$other_sql = "SELECT p.*, pi.image_url, c.name as category_name, b.name as brand_name
    FROM products p
    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.is_active = 1 AND p.published = 'true'
        AND (c.name IS NULL OR LOWER(c.name) != 'phones')
        AND (b.name IS NULL OR LOWER(b.name) != 'apple')
    ORDER BY p.created_at DESC
    LIMIT 8";
$other_result = mysqli_query($conn, $other_sql);
$other_products = [];
if ($other_result) {
    while ($row = mysqli_fetch_assoc($other_result)) {
        $other_products[] = $row;
    }
}
?>
<?php if (!empty($other_products)): ?>
<div class="max-w-[100%] mx-auto px-4 lg:px-36 py-10 bg-[whitesmoke]">
    <div>
        <span class="text-blue-500 font-semibold text-sm">Other Products</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-1">Explore Other Products</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mt-5">
        <?php foreach ($other_products as $product): ?>
            <div class="p-4 flex flex-col group transition-all duration-300">
                <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                    <?php $img = !empty($product['image_url']) ? $product['image_url'] : './src/assets/images/placeholder.png'; ?>
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
                    <div class="absolute top-3 right-3 flex flex-col gap-2">
                        <button onclick="window.location.replace('product-view?product=<?= $product['product_id'] ?>')" class="bg-white rounded-full w-9 h-9 flex items-center justify-center shadow hover:bg-gray-100 transition"><i class="bi bi-eye text-lg"></i></button>
                    </div>
                    <!-- Add To Cart Button (hover only) -->
                    <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="absolute left-0 bottom-0 w-full bg-black text-white font-semibold py-3 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Add to cart
                        </button>
                    </form>
                </div>
                <div class="mt-4 flex flex-col items-center">
                    <h3 class="font-semibold text-gray-900 text-base text-center mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="flex items-center justify-center gap-1 mb-1">
                        <span class="text-lg font-bold text-blue-400"><?php echo $product['discount_price'] ? '$' . number_format($product['discount_price']) : '$' . number_format($product['base_price']); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Company Info -->
            <div class="lg:col-span-1">
                <div class="flex items-center mb-6">
                    <img src="./src/assets/icons/kt_logo_2.png" class="w-10 h-10 mr-3" alt="KT Phones Logo">
                    <div>
                        <h3 class="text-xl font-bold">KT Phones</h3>
                        <p class="text-sm text-gray-400">Premium Smartphones</p>
                    </div>
                </div>
                <p class="text-gray-400 mb-6 leading-relaxed">
                    Your trusted destination for premium smartphones and exceptional customer service.
                    We bring you the latest technology with unbeatable prices and outstanding support.
                </p>

                <!-- Social Media -->
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-facebook text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-twitter-x text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-linkedin text-xl"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="home" class="text-gray-400 hover:text-white hover:underline">Home</a></li>
                    <li><a href="store" class="text-gray-400 hover:text-white hover:underline">Store</a></li>
                    <li><a href="about" class="text-gray-400 hover:text-white hover:underline">About Us</a></li>
                    <li><a href="contact" class="text-gray-400 hover:text-white hover:underline">Contact Us</a></li>
                    <li><a href="faq" class="text-gray-400 hover:text-white hover:underline">FAQ</a></li>
                </ul>
            </div>

            <!-- Shop by Category -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Shop</h4>
                <ul class="space-y-3">
                    <?php
                    // Assuming $categories is available from navbar.php
                    if (!empty($categories)) {
                        foreach (array_slice($categories, 0, 5) as $category) {
                            echo '<li><a href="category?category=' . $category['slug'] . '" class="text-gray-400 hover:text-white hover:underline">' . htmlspecialchars($category['name']) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <!-- Legal & Support -->
            <div>
                <h4 class="text-lg font-semibold mb-6">Support</h4>
                <ul class="space-y-3">
                    <li><a href="terms" class="text-gray-400 hover:text-white hover:underline">Terms of Service</a></li>
                    <li><a href="privacy" class="text-gray-400 hover:text-white hover:underline">Privacy Policy</a></li>
                    <li><a href="returns" class="text-gray-400 hover:text-white hover:underline">Return Policy</a></li>
                    <li><a href="sitemap" class="text-gray-400 hover:text-white hover:underline">Sitemap</a></li>
                </ul>
            </div>
        </div>

        <!-- Newsletter Signup -->
        <div class="border-t border-gray-800 pt-8 mb-8">
            <div class="max-w-md">
                <h4 class="text-lg font-semibold mb-4">Stay Updated</h4>
                <p class="text-gray-400 mb-4">Get the latest updates on new products, exclusive offers, and tech tips.</p>
                <div class="flex">
                    <input type="email" placeholder="Enter your email"
                        class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-l-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-r-lg font-semibold transition-colors duration-300">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © 2024 KT Phones. All rights reserved.
                </div>

                <!-- Payment Methods -->
                <div class="flex items-center space-x-4">
                    <span class="text-gray-400 text-sm">We Accept:</span>
                    <div class="flex space-x-2">
                        <div class="w-8 h-5 bg-gray-700 rounded flex items-center justify-center">
                            <span class="text-xs text-gray-400">Visa</span>
                        </div>
                        <div class="w-8 h-5 bg-gray-700 rounded flex items-center justify-center">
                            <span class="text-xs text-gray-400">MC</span>
                        </div>
                        <div class="w-8 h-5 bg-gray-700 rounded flex items-center justify-center">
                            <span class="text-xs text-gray-400">PayPal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>