<aside class="lg:w-1/4">
    <div class="sticky top-24 space-y-6">
        <!-- Filter Actions -->
        <div class="bg-white p-5 rounded-lg shadow-sm observe-card">
            <h3 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-3">Actions</h3>
            <div class="flex flex-col gap-3">
                <button id="apply-filters" class="w-full bg-blue-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-blue-700 transition-all shadow-md">Apply Filters</button>
                <a href="store" class="w-full bg-gray-200 text-gray-800 font-bold py-2.5 px-4 rounded-lg hover:bg-gray-300 transition-all shadow-md text-center">Clear Filters</a>
            </div>
        </div>
        <!-- Search Filter -->
        <div class="bg-white p-5 rounded-lg shadow-sm observe-card">
            <h3 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-3">Search</h3>
            <div class="relative">
                <input type="text" id="search-input" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= $search_query ?>">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="bg-white p-5 rounded-lg shadow-sm observe-card">
            <h3 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-3">Categories</h3>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                <a href="#" class="filter-category flex justify-between items-center text-gray-700 hover:text-blue-600 font-medium p-2 rounded-md" data-slug="">
                    All Categories
                </a>
                <?php mysqli_data_seek($categories_result, 0); ?>
                <?php while ($category = mysqli_fetch_assoc($categories_result)) : ?>
                    <a href="#" class="filter-category flex justify-between items-center text-gray-600 hover:text-blue-600 p-2 rounded-md" data-slug="<?= $category['slug'] ?>">
                        <?= htmlspecialchars($category['name']) ?>
                        <span class="text-sm bg-gray-100 text-gray-500 rounded-full px-2 py-0.5"><?= $category['product_count'] ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Price Range Filter -->
        <div class="bg-white p-5 rounded-lg shadow-sm observe-card">
            <h3 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-3">Price Range</h3>
            <div id="price-slider" class="mb-4"></div>
            <div class="flex justify-between items-center text-sm text-gray-600">
                <span id="price-min">Frw <?= number_format($price_min_filter ?: $global_min_price) ?></span>
                <span id="price-max">Frw <?= number_format($price_max_filter ?: $global_max_price) ?></span>
            </div>
            <input type="hidden" id="price-min-hidden" value="<?= $price_min_filter ?: $global_min_price ?>">
            <input type="hidden" id="price-max-hidden" value="<?= $price_max_filter ?: $global_max_price ?>">
        </div>

    </div>
</aside>
