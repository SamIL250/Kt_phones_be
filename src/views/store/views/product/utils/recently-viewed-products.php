<?php
// recently-viewed-products.php

if (!empty($_SESSION['recently_viewed']) && count($_SESSION['recently_viewed']) > 0) {
    $recently_viewed_ids = $_SESSION['recently_viewed'];
    // Ensure the current product is not in the list of its own recently viewed
    $current_product_id_to_exclude = $_GET['product'] ?? 0;
    $recently_viewed_ids = array_filter($recently_viewed_ids, function ($id) use ($current_product_id_to_exclude) {
        return $id != $current_product_id_to_exclude;
    });

    if (!empty($recently_viewed_ids)) {
        $ids_str = "'" . implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($recently_viewed_ids), $conn), $recently_viewed_ids)) . "'";

        $recently_viewed_products_sql = "
            SELECT p.product_id, p.name as product_name, p.base_price, p.discount_price, b.name as brand_name,
            (SELECT image_url FROM product_images WHERE product_id = p.product_id ORDER BY display_order LIMIT 1) as image_url
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            WHERE p.product_id IN ($ids_str)
            ORDER BY FIELD(p.product_id, $ids_str)
            LIMIT 4
        ";

        $recently_viewed_result = mysqli_query($conn, $recently_viewed_products_sql);
        $recently_viewed_products = [];
        if ($recently_viewed_result) {
            while ($row = mysqli_fetch_assoc($recently_viewed_result)) {
                $recently_viewed_products[] = $row;
            }
        }

        if (!empty($recently_viewed_products)) {
?>
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-left observe-card">Recently Viewed</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 my-4 observe-card">
                    <?php foreach ($recently_viewed_products as $product) :
                        $firstImage = !empty($product['image_url']) ? trim($product['image_url']) : 'src/assets/images/placeholder.jpg';
                        if (!file_exists($firstImage)) {
                            $image_path_try = 'dashboard/src/assets/img/products/' . basename($firstImage);
                            if (file_exists($image_path_try)) {
                                $firstImage = $image_path_try;
                            }
                        }
                    ?>
                        <div class="p-4 flex flex-col group transition-all duration-300">
                                <!-- Image & Icons -->
                                <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                                   
                                    <img src="<?= $firstImage ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
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
                                        <span class="text-lg font-bold text-blue-400"><?php echo $product['discount_price'] ? ' Frw ' . number_format($product['discount_price']) : ' Frw ' . number_format($product['base_price']); ?></span>
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
<?php
        }
    }
}
?>