    <?php

// Related products - based on same brand and category
    $related_products = array();

// Get current product's brand and category
$current_brand = $product_data['brand_name'] ?? '';
$current_category = $product_data['category_name'] ?? '';

// Prepare statement to prevent SQL injection
$get_related_products = mysqli_prepare($conn, "
    SELECT 
        p.product_id,
        p.name AS product_name,
        p.base_price,
        p.discount_price,
        p.stock_quantity,
        p.created_at,
        c.name AS category_name,
        b.name AS brand_name,
        
        -- Aggregated attributes
        GROUP_CONCAT(DISTINCT CONCAT(at.name, ': ', av.value) SEPARATOR ', ') AS attributes,
        
        -- Primary image
        GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.display_order SEPARATOR ', ') AS image_urls,
        
        -- Aggregated tags
        GROUP_CONCAT(DISTINCT pt.tag_name SEPARATOR ', ') AS tags,
        
        -- Reviews and ratings
        AVG(pr.rating) as avg_rating,
        COUNT(pr.review_id) as review_count
        
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN attribute_type at ON pa.attribute_type_id = at.attribute_type_id
    LEFT JOIN attribute_value av ON pa.attribute_value_id = av.attribute_value_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
    LEFT JOIN product_tag_map ptm ON p.product_id = ptm.product_id
    LEFT JOIN product_tags pt ON ptm.tag_id = pt.tag_id
    LEFT JOIN product_reviews pr ON p.product_id = pr.product_id
    
    WHERE p.is_active = 1 AND p.published = 'true' 
    AND p.product_id != ?
    AND (b.name = ? OR c.name = ?)
    
    GROUP BY p.product_id
    ORDER BY p.created_at DESC
    LIMIT 8
");

mysqli_stmt_bind_param($get_related_products, "sss", $product_id, $current_brand, $current_category);
mysqli_stmt_execute($get_related_products);
$result = mysqli_stmt_get_result($get_related_products);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Process image URLs
        $row['image_urls_array'] = $row['image_urls'] ? explode(', ', $row['image_urls']) : [];
        $row['primary_image'] = !empty($row['image_urls_array']) ? $row['image_urls_array'][0] : './src/assets/images/product_placeholder.png';
        
        // Process tags
        $row['tags_array'] = $row['tags'] ? explode(', ', $row['tags']) : [];
        
        $related_products[] = $row;
    }
}
mysqli_stmt_close($get_related_products);

// If no related products found by brand/category, get some general products
if (empty($related_products)) {
    $get_general_products = mysqli_prepare($conn, "
        SELECT 
            p.product_id,
            p.name AS product_name,
            p.base_price,
            p.discount_price,
            p.stock_quantity,
            p.created_at,
            c.name AS category_name,
            b.name AS brand_name,
            GROUP_CONCAT(DISTINCT pi.image_url ORDER BY pi.display_order SEPARATOR ', ') AS image_urls,
            AVG(pr.rating) as avg_rating,
            COUNT(pr.review_id) as review_count

            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN product_reviews pr ON p.product_id = pr.product_id
        
        WHERE p.is_active = 1 AND p.published = 'true' 
        AND p.product_id != ?

            GROUP BY p.product_id
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    
    mysqli_stmt_bind_param($get_general_products, "s", $product_id);
    mysqli_stmt_execute($get_general_products);
    $result = mysqli_stmt_get_result($get_general_products);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['image_urls_array'] = $row['image_urls'] ? explode(', ', $row['image_urls']) : [];
            $row['primary_image'] = !empty($row['image_urls_array']) ? $row['image_urls_array'][0] : './src/assets/images/product_placeholder.png';
            $related_products[] = $row;
        }
    }
    mysqli_stmt_close($get_general_products);
}
?>

    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-left observe-card">You may also like</h2>
    </div>

<?php if (empty($related_products)): ?>
    <div class="my-10">
        <div class="bg-gray-50 text-gray-500 border border-gray-200 observe-card p-4 rounded-lg text-center">
            <i class="bi bi-info-circle text-2xl mb-2"></i>
            <p>No related products available at the moment.</p>
            </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 my-4 observe-card">
        <?php foreach ($related_products as $product): ?>
        <?php
            $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['base_price'];
            $original_price = $product['base_price'];
            $discount_exists = $product['discount_price'] > 0;
            
            $avg_rating = round($product['avg_rating'] ?? 0);
            $num_reviews = $product['review_count'] ?? 0;
            
            // Generate star rating HTML
            $stars_html = '';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $avg_rating) {
                    $stars_html .= '<i class="bi bi-star-fill text-yellow-400 text-xs"></i>';
                } else {
                    $stars_html .= '<i class="bi bi-star text-gray-300 text-xs"></i>';
                }
            }
            
            // Check if product is in wishlist
            $is_in_wishlist = false;
            if (isset($is_logged_in) && $is_logged_in && isset($customer_id)) {
                $check_wishlist_query = mysqli_prepare($conn, "
                    SELECT 1 FROM wishlists w 
                    JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id 
                    WHERE w.user_id = ? AND wi.product_id = ?
                ");
                if ($check_wishlist_query) {
                    mysqli_stmt_bind_param($check_wishlist_query, "ss", $customer_id, $data['product_id']);
                    mysqli_stmt_execute($check_wishlist_query);
                    mysqli_stmt_store_result($check_wishlist_query);
                    if (mysqli_stmt_num_rows($check_wishlist_query) > 0) {
                        $is_in_wishlist = true;
                    }
                    mysqli_stmt_close($check_wishlist_query);
                }
            }
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
<?php endif; ?>

