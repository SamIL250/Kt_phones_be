<?php
// Aside Sections - Best Sellers and Today Deals

// Function to fetch products for aside sections
if (!function_exists('getAsideProducts')) {
    function getAsideProducts($conn, $where_clause = "", $limit = 5, $order_by = "p.created_at DESC")
    {
        $sql = "SELECT 
                p.product_id,
                p.name AS product_name,
                p.base_price,
                p.discount_price,
                p.stock_quantity,
                c.name AS category_name,
                b.name AS brand_name,
                GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.display_order SEPARATOR ', ') AS image_urls,
                AVG(pr.rating) as avg_rating, COUNT(pr.review_id) as num_reviews
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_reviews pr ON p.product_id = pr.product_id
                WHERE p.is_active = 1 AND p.published = 'true' " . $where_clause . "
                GROUP BY p.product_id
                ORDER BY $order_by
                LIMIT " . (int)$limit;

        $result = mysqli_query($conn, $sql);
        $products = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['image_urls_array'] = $row['image_urls'] ? explode(', ', $row['image_urls']) : [];
                $row['primary_image'] = !empty($row['image_urls_array']) ? $row['image_urls_array'][0] : './src/assets/images/product_placeholder.png';
                $products[] = $row;
            }
        }
        return $products;
    }
}

// Fetch Best Sellers and Today Deals
$best_sellers = getAsideProducts($conn, "AND pr.rating >= 4", 5, "AVG(pr.rating) DESC, COUNT(pr.review_id) DESC");
$today_deals = getAsideProducts($conn, "AND p.discount_price > 0 AND p.discount_price < p.base_price * 0.9", 5, "(p.base_price - p.discount_price) / p.base_price DESC");

// Fallback products
if (empty($best_sellers)) {
    $best_sellers = getAsideProducts($conn, "", 5);
}
if (empty($today_deals)) {
    $today_deals = getAsideProducts($conn, "AND p.discount_price > 0", 5);
}

// Aside product card renderer
if (!function_exists('renderAsideCard')) {
    function renderAsideCard($product, $is_logged_in, $customer_id, $conn)
    {
        $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
        $original_price = $product['base_price'];
        $discount_exists = $product['discount_price'] > 0;

        $badges = [];
        if ($discount_exists) {
            $discount_percentage = round((($original_price - $price) / $original_price) * 100);
            $badges[] = '<span class="bg-red-500 text-white text-xs font-bold px-1 py-0.5 rounded text-xs">-' . $discount_percentage . '%</span>';
        }

?>
        <div class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="flex p-3">
                <div class="relative w-16 h-16 flex-shrink-0 mr-3">
                    <img src="<?= $product['primary_image'] ?>" alt="<?= $product['product_name'] ?>" class="w-full h-full object-contain rounded">
                    <?php if (!empty($badges)): ?>
                        <div class="absolute -top-1 -right-1">
                            <?= $badges[0] ?? '' ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-gray-900 text-sm line-clamp-2 hover:text-blue-600 transition-colors mb-1">
                        <a href="product-view?product=<?= $product['product_id'] ?>"><?= $product['product_name'] ?></a>
                    </h4>
                    <div class="flex items-baseline gap-2">
                        <span class="text-sm font-bold text-gray-900"><?= number_format($price, 2) ?> Frw</span>
                        <?php if ($discount_exists): ?>
                            <span class="text-xs text-gray-500 line-through"><?= number_format($original_price, 2) ?> Frw</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>

<!-- Sidebar Sections -->
<div class="space-y-8">
    <!-- Best Sellers -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Best Sellers</h3>
        <div class="space-y-4">
            <?php foreach (array_slice($best_sellers, 0, 3) as $product): ?>
                <?php
                $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                ?>
                <a href="product-view?product=<?= $product['product_id'] ?>" class="flex items-center gap-4 group">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0">
                        <img src="<?= $product['primary_image'] ?>" alt="<?= $product['product_name'] ?>" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-tight">
                            <?= $product['product_name'] ?>
                        </h4>
                        <p class="text-sm font-bold text-gray-900 mt-1"><?= number_format($price, 0) ?> Frw</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Today's Deals -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Today's Deals</h3>
        <div class="space-y-4">
            <?php foreach (array_slice($today_deals, 0, 3) as $product): ?>
                <?php
                $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                ?>
                <a href="product-view?product=<?= $product['product_id'] ?>" class="flex items-center gap-4 group">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0">
                        <img src="<?= $product['primary_image'] ?>" alt="<?= $product['product_name'] ?>" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-tight">
                            <?= $product['product_name'] ?>
                        </h4>
                        <p class="text-sm font-bold text-gray-900 mt-1"><?= number_format($price, 0) ?> Frw</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Weekly Products -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4">This Week's Highlights</h3>
        <div class="space-y-4">
            <?php foreach (array_slice($weekly_products, 0, 3) as $product): ?>
                <?php
                $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
                ?>
                <a href="product-view?product=<?= $product['product_id'] ?>" class="flex items-center gap-4 group">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0">
                        <img src="<?= $product['primary_image'] ?>" alt="<?= $product['product_name'] ?>" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-tight">
                            <?= $product['product_name'] ?>
                        </h4>
                        <p class="text-sm font-bold text-gray-900 mt-1"><?= number_format($price, 0) ?> Frw</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>