<?php
include './src/layout/layout.php';

// Fetch Categories
$categories_sql = "SELECT name, slug FROM categories WHERE is_active = 1 ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
if ($categories_result) {
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $row;
    }
}

// Fetch Brands
$brands_sql = "SELECT name FROM brands ORDER BY name ASC";
$brands_result = mysqli_query($conn, $brands_sql);
$brands = [];
if ($brands_result) {
    while ($row = mysqli_fetch_assoc($brands_result)) {
        $brands[] = $row;
    }
}
?>

<div class="bg-gray-50 font-sans">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Header -->
        <div class="text-center mb-12 observe-card">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800">Sitemap</h1>
            <p class="text-lg text-gray-500 mt-2">Explore our website structure and find exactly what you're looking for.</p>
            <div class="mt-4 mx-auto w-24 h-1 bg-blue-600 rounded"></div>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg observe-card">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                <!-- Main Pages -->
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-4">Main Pages</h2>
                    <ul class="space-y-3">
                        <li><a href="home" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-house-door-fill mr-3 text-blue-500"></i>Home</a></li>
                        <li><a href="store" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-shop mr-3 text-blue-500"></i>Store</a></li>
                        <li><a href="about" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-info-circle-fill mr-3 text-blue-500"></i>About Us</a></li>
                        <li><a href="contact" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-envelope-fill mr-3 text-blue-500"></i>Contact Us</a></li>
                        <li><a href="cart" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-cart-fill mr-3 text-blue-500"></i>Your Cart</a></li>
                        <li><a href="profile" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-person-fill mr-3 text-blue-500"></i>Your Profile</a></li>
                    </ul>
                </div>

                <!-- Shop by Category -->
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-4">Shop by Category</h2>
                    <ul class="space-y-3">
                        <?php foreach ($categories as $category) : ?>
                            <li>
                                <a href="store?category=<?= htmlspecialchars($category['slug']) ?>" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-tag-fill mr-3 text-gray-400"></i>
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Shop by Brand -->
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-4">Shop by Brand</h2>
                    <ul class="space-y-3">
                        <?php foreach ($brands as $brand) : ?>
                            <li>
                                <a href="store?brand=<?= urlencode($brand['name']) ?>" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-building-fill mr-3 text-gray-400"></i>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>

            <!-- Customer Service Section -->
            <div class="mt-12 border-t pt-8">
                <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Customer Service</h2>
                <div class="flex justify-center gap-8 text-center">
                    <a href="privacy" class="text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-shield-lock-fill mr-2"></i>Privacy Policy</a>
                    <a href="returns" class="text-gray-600 hover:text-blue-600 transition-colors"><i class="bi bi-box-seam-fill mr-2"></i>Returns Policy</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include './src/components/footer.php'; ?>
</body>

</html>