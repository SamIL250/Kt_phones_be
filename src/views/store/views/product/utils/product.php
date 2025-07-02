<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- Product Images -->
    <div class="product-gallery">
        <div class="mb-4" style="background-image: url('./src/assets/images/frame.png'); background-size: cover; background-position: center;">
            <img id="mainImage" src="<?= htmlspecialchars($product['primary_image']) ?>" 
                    alt="<?= htmlspecialchars($product['name']) ?>" 
                    class="w-full h-96 object-contain rounded-lg border border-gray-200 main-image">
        </div>
        
        <canvas id="mainImageCanvas" width="600" height="400" style="display:none;" class="w-full h-96 object-contain rounded-lg border border-gray-200"></canvas>
        
        <?php if (count($product['image_urls_array']) > 1): ?>
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ($product['image_urls_array'] as $index => $image_url): ?>
                    <img src="<?= htmlspecialchars($image_url) ?>" 
                            alt="<?= htmlspecialchars($product['name']) ?> - Image <?= $index + 1 ?>" 
                            class="thumbnail w-20 h-20 object-cover rounded-lg <?= $index === 0 ? 'active' : '' ?>"
                            onclick="changeMainImage('<?= htmlspecialchars($image_url) ?>', this)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Info -->
    <div class="space-y-6">
        <!-- Product Title and Brand -->
        <div>
            <?php if (!empty($product['brand_name'])): ?>
                <p class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($product['brand_name']) ?></p>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            
            <!-- Rating -->
            <?php if ($avg_rating > 0): ?>
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex items-center">
                        <?= $stars_html ?>
                    </div>
                    <span class="text-sm text-gray-600">(<?= $product['review_count'] ?> reviews)</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Price -->
        <div class="space-y-2">
            <div class="flex items-baseline gap-3">
                <span id="mainProductPrice" class="text-3xl font-bold text-gray-900"><?= number_format($current_price, 0) ?> Frw</span>
                <?php if ($product['discount_price'] > 0): ?>
                    <span class="text-xl text-gray-500 line-through"><?= number_format($original_price, 0) ?> Frw</span>
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-semibold">-<?= $discount_percentage ?>%</span>
                <?php endif; ?>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <p class="text-green-600 text-sm font-medium">
                    <i class="bi bi-check-circle mr-1"></i>
                    In Stock (<?= $product['stock_quantity'] ?> available)
                </p>
            <?php else: ?>
                <p class="text-red-600 text-sm font-medium">
                    <i class="bi bi-x-circle mr-1"></i>
                    Out of Stock
                </p>
            <?php endif; ?>
        </div>

        <!-- Product Description -->
        <?php if (!empty($product['description'])): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Product Variants Selection -->
        <?php if (!empty($storage_options) || !empty($color_options)): ?>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Choose Your Options</h3>
                
                <!-- Storage Selection -->
                <?php if (!empty($storage_options)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Storage</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($storage_options as $storage_id => $storage_value): ?>
                                <button type="button" 
                                        class="storage-option px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition-all duration-200 hover:border-blue-500 hover:bg-blue-50"
                                        data-storage-id="<?= $storage_id ?>"
                                        data-storage-value="<?= htmlspecialchars($storage_value) ?>">
                                    <?= htmlspecialchars($storage_value) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Color Selection -->
                <?php if (!empty($color_options)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <div class="flex flex-wrap gap-3">
                            <?php 
                            // Color mapping for common colors
                            $color_map = [
                                'black' => '#000000',
                                'white' => '#FFFFFF',
                                'blue' => '#3B82F6',
                                'red' => '#EF4444',
                                'green' => '#10B981',
                                'gold' => '#F59E0B',
                                'silver' => '#9CA3AF',
                                'gray' => '#6B7280',
                                'pink' => '#EC4899',
                                'purple' => '#8B5CF6',
                                'orange' => '#F97316',
                                'yellow' => '#EAB308',
                                'brown' => '#A0522D',
                                'navy' => '#1E3A8A',
                                'rose' => '#F43F5E',
                                'indigo' => '#6366F1',
                                'teal' => '#14B8A6',
                                'cyan' => '#06B6D4',
                                'lime' => '#84CC16',
                                'emerald' => '#059669',
                                'violet' => '#7C3AED',
                                'fuchsia' => '#D946EF',
                                'slate' => '#64748B',
                                'zinc' => '#71717A',
                                'neutral' => '#737373',
                                'stone' => '#78716C',
                                'amber' => '#F59E0B',
                                'sky' => '#0EA5E9',
                                'indingo' => '#6366F1',
                                'calmer' => '#F0F9FF'
                            ];
                            
                            foreach ($color_options as $color_id => $color_value): 
                                $color_lower = strtolower(trim($color_value));
                                $hex_color = $color_map[$color_lower] ?? '#6B7280'; // Default gray if not found
                            ?>
                                <button type="button" 
                                        class="color-option w-12 h-12 rounded-full border-2 border-gray-300 transition-all duration-200 hover:border-blue-500 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        data-color-id="<?= $color_id ?>"
                                        data-color-value="<?= htmlspecialchars($color_value) ?>"
                                        data-hex="<?= $hex_color ?>"
                                        style="background-color: <?= $hex_color ?>;"
                                        title="<?= htmlspecialchars($color_value) ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Selected Variant Info -->
                <div id="selectedVariantInfo" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Selected: <span id="selectedVariantText" class="font-medium text-gray-900"></span></p>
                            <p class="text-sm text-gray-600">Stock: <span id="selectedVariantStock" class="font-medium text-gray-900"></span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600" id="selectedVariantPrice"></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Product Specifications (Non-variant attributes) -->
        <?php if (!empty($product['attributes_array'])): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Specifications</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($product['attributes_array'] as $attr_name => $attr_value): ?>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($attr_name) ?></span>
                            <span class="text-sm text-gray-900"><?= htmlspecialchars($attr_value) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Product Tags -->
        <?php if (!empty($product['tags_array'])): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($product['tags_array'] as $tag): ?>
                        <span class="attribute-badge"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add to Cart Section -->
        <?php if ($product['stock_quantity'] > 0): ?>
            <div class="space-y-4">
               
                <form method="POST" action="./src/services/cart/cart_handler.php" id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
                    <input type="hidden" name="quantity" id="form-quantity" value="1">
                    <input type="hidden" name="selected_storage" id="selectedStorage" value="">
                    <input type="hidden" name="selected_color" id="selectedColor" value="">
                    <input type="hidden" name="variant_id" id="selectedVariantId" value="">
                    <?php if ($is_logged_in): ?>
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($customer_id) ?>">
                    <?php endif; ?>
                    <button type="submit" class="add-to-cart-btn w-full py-4 text-white font-semibold rounded-lg flex items-center justify-center gap-2" id="addToCartBtn">
                        <i class="bi bi-cart-plus text-xl"></i>
                        Add to Cart
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reviews Section -->
<?php if (!empty($reviews)): ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
        <div class="grid gap-4">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                            </h4>
                            <div class="flex items-center gap-2 mt-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?> text-yellow-400"></i>
                                <?php endfor; ?>
                                <span class="text-sm text-gray-500"><?= $review['rating'] ?>/5</span>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">
                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                        </span>
                    </div>
                    <?php if (!empty($review['comment'])): ?>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <?php endif; ?>
                    <?php if ($review['is_verified_purchase']): ?>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="bi bi-check-circle mr-1"></i>
                                Verified Purchase
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php foreach ($variants as $v) { echo '<!-- VARIANT: ' . json_encode($v) . ' -->'; } ?>

<script>
function changeMainImage(imageUrl, thumbEl) {
    var mainImg = document.getElementById('mainImage');
    if (mainImg) {
        mainImg.src = imageUrl;
    }
    // Remove 'active' class from all thumbnails
    var thumbs = document.querySelectorAll('.thumbnail');
    thumbs.forEach(function(thumb) {
        thumb.classList.remove('active');
    });
    // Add 'active' class to the clicked thumbnail
    if (thumbEl) {
        thumbEl.classList.add('active');
    }
}

// Variant selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const storageOptions = document.querySelectorAll('.storage-option');
    const colorOptions = document.querySelectorAll('.color-option');
    const selectedVariantInfo = document.getElementById('selectedVariantInfo');
    const selectedVariantText = document.getElementById('selectedVariantText');
    const selectedVariantPrice = document.getElementById('selectedVariantPrice');
    const selectedVariantStock = document.getElementById('selectedVariantStock');
    const addToCartForm = document.getElementById('addToCartForm');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    // Variant data from PHP
    const variantPrices = <?= json_encode($variant_prices) ?>;
    const variantStocks = <?= json_encode($variant_stocks) ?>;
    const variants = <?= json_encode($variants) ?>;
    
    let selectedStorage = null;
    let selectedColor = null;
    
    // Storage selection
    storageOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all storage options
            storageOptions.forEach(opt => opt.classList.remove('bg-blue-500', 'text-white', 'border-blue-500'));
            // Add active class to selected option
            this.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
            
            selectedStorage = {
                id: this.dataset.storageId,
                value: this.dataset.storageValue
            };
            
            updateVariantInfo();
        });
    });
    
    // Color selection
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all color options
            colorOptions.forEach(opt => opt.classList.remove('border-blue-500', 'scale-110', 'ring-2', 'ring-blue-500'));
            // Add active class to selected option
            this.classList.add('border-blue-500', 'scale-110', 'ring-2', 'ring-blue-500');
            
            selectedColor = {
                id: this.dataset.colorId,
                value: this.dataset.colorValue
            };
            
            updateVariantInfo();
        });
    });
    
    function updateVariantInfo() {
        const mainProductPrice = document.getElementById('mainProductPrice');
        if (selectedStorage || selectedColor) {
            const storageId = selectedStorage ? selectedStorage.id : '';
            const colorId = selectedColor ? selectedColor.id : '';
            const variantKey = storageId + '_' + colorId;
            
            // Debug logs
            console.log('Looking for storageId:', storageId, 'colorId:', colorId);
            console.log('All variants:', variants);
            
            // Find the matching variant (compare as strings)
            const matchingVariant = variants.find(variant => {
                const variantStorageId = (variant.storage_id || '').toString();
                const variantColorId = (variant.color_id || '').toString();
                return variantStorageId === storageId.toString() && variantColorId === colorId.toString();
            });
            
            console.log('Selected variant:', matchingVariant);
            
            if (matchingVariant) {
                // Update variant info display
                const variantText = [];
                if (selectedStorage) variantText.push(selectedStorage.value);
                if (selectedColor) variantText.push(selectedColor.value);
                
                selectedVariantText.textContent = variantText.join(' - ');
                selectedVariantPrice.textContent = new Intl.NumberFormat('en-US').format(matchingVariant.variant_price) + ' Frw';
                selectedVariantStock.textContent = matchingVariant.variant_stock > 0 ? 
                    matchingVariant.variant_stock + ' available' : 'Out of stock';
                
                // Update form fields
                document.getElementById('selectedStorage').value = selectedStorage ? selectedStorage.id : '';
                document.getElementById('selectedColor').value = selectedColor ? selectedColor.id : '';
                document.getElementById('selectedVariantId').value = matchingVariant.variant_id;
                
                // Show variant info
                selectedVariantInfo.classList.remove('hidden');
                
                // Update add to cart button
                if (matchingVariant.variant_stock > 0) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    addToCartBtn.innerHTML = '<i class="bi bi-cart-plus text-xl"></i>Add to Cart';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    addToCartBtn.innerHTML = '<i class="bi bi-x-circle text-xl"></i>Out of Stock';
                }
                // Update main product price at the top
                if (mainProductPrice && matchingVariant.variant_price) {
                    mainProductPrice.textContent = new Intl.NumberFormat('en-US').format(matchingVariant.variant_price) + ' Frw';
                }
            } else {
                // Hide variant info if no matching variant
                selectedVariantInfo.classList.add('hidden');
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
                addToCartBtn.innerHTML = '<i class="bi bi-exclamation-triangle text-xl"></i>Select Options';
                // Restore original price
                if (mainProductPrice) {
                    mainProductPrice.textContent = '<?= number_format($current_price, 0) ?> Frw';
                }
            }
        } else {
            // Hide variant info if no selection
            selectedVariantInfo.classList.add('hidden');
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
            addToCartBtn.innerHTML = '<i class="bi bi-exclamation-triangle text-xl"></i>Select Options';
            // Restore original price
            if (mainProductPrice) {
                mainProductPrice.textContent = '<?= number_format($current_price, 0) ?> Frw';
            }
        }
    }
    
    // Form submission validation
    addToCartForm.addEventListener('submit', function(e) {
        if (!selectedStorage && !selectedColor) {
            e.preventDefault();
            alert('Please select at least one option (Storage or Color) before adding to cart.');
            return false;
        }
        
        // Check if variant is in stock
        const variantId = document.getElementById('selectedVariantId').value;
        const selectedVariant = variants.find(v => v.variant_id == variantId);
        
        if (selectedVariant && selectedVariant.variant_stock <= 0) {
            e.preventDefault();
            alert('This variant is out of stock.');
            return false;
        }
    });
    
    // Auto-select first option if only one option is available
    if (storageOptions.length === 1 && colorOptions.length === 0) {
        storageOptions[0].click();
    } else if (colorOptions.length === 1 && storageOptions.length === 0) {
        colorOptions[0].click();
    }

    // Helper: Convert hex to RGBA
    function hexToRgba(hex, alpha = 0.5) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(x => x + x).join('');
        const num = parseInt(hex, 16);
        return `rgba(${(num >> 16) & 255}, ${(num >> 8) & 255}, ${num & 255}, ${alpha})`;
    }

    // Colorization logic
    function colorizeMainImage(hexColor) {
        const img = document.getElementById('mainImage');
        const canvas = document.getElementById('mainImageCanvas');
        const ctx = canvas.getContext('2d');
        // Ensure canvas matches image size
        canvas.width = img.naturalWidth || img.width;
        canvas.height = img.naturalHeight || img.height;
        // Draw the image
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        // Apply color overlay
        ctx.globalCompositeOperation = 'source-atop';
        ctx.fillStyle = hexToRgba(hexColor, 0.5); // 0.5 = 50% tint, adjust as needed
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.globalCompositeOperation = 'source-over';
        // Show canvas, hide img
        img.style.display = 'none';
        canvas.style.display = '';
    }

    // Restore original image
    function restoreMainImage() {
        const img = document.getElementById('mainImage');
        const canvas = document.getElementById('mainImageCanvas');
        img.style.display = '';
        canvas.style.display = 'none';
    }

    // Color swatch click handler
    document.querySelectorAll('.color-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const colorHex = this.dataset.hex;
            colorizeMainImage(colorHex);
        });
    });

    // If you want to restore the original image when a storage option is clicked or on some other event:
    // document.querySelectorAll('.storage-option').forEach(function(btn) {
    //     btn.addEventListener('click', restoreMainImage);
    // });

    // Optionally, restore image on page load or when no color is selected
    // restoreMainImage();
});
</script>