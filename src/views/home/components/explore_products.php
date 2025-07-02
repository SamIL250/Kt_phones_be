<?php
// Fetch products with their primary image
$sql = "SELECT p.*, pi.image_url
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.is_active = 1 AND p.published = 'true' 
        ORDER BY p.created_at DESC
        LIMIT 8";
$result = mysqli_query($conn, $sql);
$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

if (!function_exists('getProductRating')) {
    function getProductRating($conn, $product_id) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM product_reviews WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "'";
        $result = mysqli_query($conn, $sql);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row;
        }
        return ['avg_rating' => 0, 'review_count' => 0];
    }
}
?>
<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex items-center justify-between mb-6 fade-in-up">
        <div>
            <span class="text-blue-500 font-semibold text-sm">Our Products</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-1">Explore Our Products</h2>
        </div>
        <div class="flex gap-2">
            <button class="w-10 h-10 rounded-full border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition"><i class="bi bi-arrow-left"></i></button>
            <button class="w-10 h-10 rounded-full border flex items-center justify-center text-gray-500 hover:bg-gray-100 transition"><i class="bi bi-arrow-right"></i></button>
        </div>
    </div>
    <?php if (empty($products)): ?>
        <div class="text-center text-blue-400 font-bold py-10">No products found or there was a problem fetching products.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
        <?php foreach ($products as $product): ?>
           


            <div class="p-4 flex flex-col group transition-all duration-300">
                <!-- Image & Icons -->
                <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                    
                <?php $img = !empty($product['image_url']) ? $product['image_url'] : './src/assets/images/placeholder.png'; ?>
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
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
                    <h3 class="font-semibold text-gray-900 text-base text-center mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <?php
                    $rating = getProductRating($conn, $product['product_id']);
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
    <div class="flex justify-center mt-10">
        <a href="store" class="bg-blue-500 hover:bg-blue-550 text-white font-semibold px-8 py-3 rounded transition-all duration-300">View All Products</a>
    </div>
</div>
<style>
.fade-in-up {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.8s cubic-bezier(.4,2,.6,1), transform 0.8s cubic-bezier(.4,2,.6,1);
}
.fade-in-up.visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
</style>



<script>
// Animate in on scroll
const fadeEls = document.querySelectorAll('.fade-in-up');
if (window.IntersectionObserver && fadeEls.length) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    fadeEls.forEach(el => observer.observe(el));
}
</script>

