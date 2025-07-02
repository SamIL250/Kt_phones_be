<main class="">
    <!-- Toolbar -->
    

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 bg-white/90 rounded-full border border-gray-200 px-4 py-3 gap-4">
            <p class="text-gray-700 mb-2 sm:mb-0 text-base">
                <span class="inline-block bg-blue-100 text-blue-600 font-semibold rounded-full px-3 py-1 mr-2 text-sm"><i class="bi bi-funnel-fill mr-1"></i> Filter</span>
                Showing <span class="font-bold text-blue-600"><?= $total_products > 0 ? $offset + 1 : 0 ?>-<?= min($offset + $limit, $total_products) ?></span> of
                <span class="font-bold text-blue-600"><?= $total_products ?></span> products
            </p>
            <div class="flex items-center gap-2">
                <label for="sort" class="text-gray-600 font-medium mr-2"><i class="bi bi-sort-down-alt mr-1"></i>Sort by:</label>
                <div class="relative">
                    <select id="sort" name="sort" onchange="location = this.value;"
                        class="pl-4 pr-10 py-2 rounded-full bg-gray-100 border border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-gray-700 font-semibold appearance-none transition-all duration-200">
                        <option value="newest" <?= $sort_by === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="price_asc" <?= $sort_by === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= $sort_by === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="rating" <?= $sort_by === 'rating' ? 'selected' : '' ?>>Highest Rated</option>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"><i class="bi bi-chevron-down"></i></span>
                </div>
            </div>
        </div>

    <?php
    $wishlist_product_ids = [];
    if (isset($is_logged_in) && $is_logged_in && !empty($customer_id)) {
        $wishlist_query = mysqli_query($conn, "
            SELECT wi.product_id
            FROM wishlists w
            JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id
            WHERE w.user_id = '" . mysqli_real_escape_string($conn, $customer_id) . "'"
        );
        if ($wishlist_query) {
            while ($row = mysqli_fetch_assoc($wishlist_query)) {
                $wishlist_product_ids[] = $row['product_id'];
            }
        }
    }
    ?>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $product) :
                $imageUrls = !empty($product['image_urls']) ? explode(',', $product['image_urls']) : [];
                $firstImage = !empty($imageUrls) ? trim($imageUrls[0]) : 'src/assets/images/placeholder.jpg';
                if (!file_exists($firstImage)) {
                    $image_path_try = 'dashboard/src/assets/img/products/' . basename($firstImage);
                    if (file_exists($image_path_try)) {
                        $firstImage = $image_path_try;
                    }
                }
                $discount_amount = $product['base_price'] - $product['discount_price'];
                $discount_percentage = $product['base_price'] > 0 ? round(($discount_amount / $product['base_price']) * 100) : 0;
                $is_in_wishlist = in_array($product['product_id'], $wishlist_product_ids ?? []);
                $heart_icon_class = $is_in_wishlist ? 'bi-heart-fill text-red-500' : 'bi-heart';
            ?>
                <!-- <div class="product-card group bg-white rounded-lg shadow-sm overflow-hidden relative observe-card" data-product-id="<?= $product['product_id'] ?>">
                    <a href="product-view?product=<?= $product['product_id'] ?>" class="absolute inset-0 z-10"></a>
                    <div class="relative">
                        <?php if ($discount_percentage > 0) : ?>
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full z-20">-<?= $discount_percentage ?>%</div>
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-3 flex flex-col">
                        <div class="flex-grow">
                            <p class="text-xs text-gray-500 mb-1"><?= htmlspecialchars($product['brand_name'] ?? 'Brand') ?></p>
                            <h3 class="text-sm font-bold text-gray-800 truncate mb-2 group-hover:text-blue-600 transition-colors">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400 text-xs">
                                    <?php
                                    $rating = round($product['average_rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo '<i class="bi ' . ($i <= $rating ? 'bi-star-fill' : 'bi-star') . '"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="text-md font-extrabold text-gray-900">
                                <span>Frw <?= number_format($product['discount_price']) ?></span>
                                <?php if ($discount_percentage > 0) : ?>
                                    <span class="text-xs font-normal text-gray-400 line-through ml-1">Frw <?= number_format($product['base_price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2 z-20 relative">
                            <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                <?php if ($is_logged_in): ?>
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                <?php endif; ?>
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="add-to-cart-btn w-full bg-blue-600 text-white text-xs font-bold py-2 px-2 rounded-md hover:bg-blue-700 transition-all flex items-center justify-center gap-1">
                                    <i class="bi bi-cart-plus"></i> Add to cart
                                </button>
                            </form>
                            <?php if (isset($is_logged_in) && $is_logged_in): ?>
                                <form method="POST" action="./src/services/wishlist/new_wishlist.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                                    <button type="submit" class="add-to-wishlist-btn border border-gray-300 text-gray-500 text-xs font-bold py-2 px-2 rounded-md hover:bg-gray-100 hover:text-red-500 transition-all">
                                        <i class="bi <?= $heart_icon_class ?>"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <button onclick="showAuthModal()" class="add-to-wishlist-btn border border-gray-300 text-gray-500 text-xs font-bold py-2 px-2 rounded-md hover:bg-gray-100 hover:text-red-500 transition-all">
                                    <i class="bi bi-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> -->


                <div class="p-4 flex flex-col group transition-all duration-300">
                    <!-- Image & Icons -->
                    <div class="relative rounded-md overflow-hidden flex items-center justify-center min-h-[220px]" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
                        
                    <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain h-40 w-full transition-transform duration-300 group-hover:scale-105" />
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
                        <h3 class="font-semibold text-gray-900 text-base text-center mb-1"><?= htmlspecialchars($product['name']) ?></h3>
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
        <?php else : ?>
            <div class="sm:col-span-2 xl:col-span-3 text-center py-16 bg-white border border-gray-200">
                <i class="bi bi-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-700">No Products Found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your filters or search term.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <nav class="inline-flex items-center -space-x-px rounded-md shadow-sm bg-white" aria-label="Pagination">
            <?php if ($page > 1) : ?>
                <a href="#" data-page="<?= $page - 1 ?>" class="page-link relative inline-flex items-center px-2 py-2 text-gray-500 hover:bg-gray-50 focus:z-20 rounded-l-md">
                    <i class="bi bi-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="#" data-page="<?= $i ?>" class="page-link relative inline-flex items-center px-4 py-2 text-sm font-medium <?= $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600 z-20' : 'text-gray-700 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages) : ?>
                <a href="#" data-page="<?= $page + 1 ?>" class="page-link relative inline-flex items-center px-2 py-2 text-gray-500 hover:bg-gray-50 focus:z-20 rounded-r-md">
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </nav>
    </div>

</main>