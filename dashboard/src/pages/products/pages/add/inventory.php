<div class="row g-0 border-top border-bottom">
    <div class="col-sm-4">
        <div class="nav flex-sm-column border-bottom border-bottom-sm-0 border-end-sm fs-9 vertical-tab h-100 justify-content-between" role="tablist" aria-orientation="vertical">
            <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center active" id="pricingTab" data-bs-toggle="tab" data-bs-target="#pricingTabContent" role="tab" aria-controls="pricingTabContent" aria-selected="true">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="tag"></span>
                <span class="d-none d-sm-inline">Pricing</span>
            </a>
            <!-- <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center" id="restockTab" data-bs-toggle="tab" data-bs-target="#restockTabContent" role="tab" aria-controls="restockTabContent" aria-selected="false">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="package"></span>
                <span class="d-none d-sm-inline">Restock</span>
            </a> -->
            <!-- <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center" id="shippingTab" data-bs-toggle="tab" data-bs-target="#shippingTabContent" role="tab" aria-controls="shippingTabContent" aria-selected="false">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="truck"></span>
                <span class="d-none d-sm-inline">Shipping</span>
            </a> -->
            <!-- <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center" id="productsTab" data-bs-toggle="tab" data-bs-target="#productsTabContent" role="tab" aria-controls="productsTabContent" aria-selected="false">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="globe"></span>
                <span class="d-none d-sm-inline">Global Delivery</span>
            </a> -->
            <a class="nav-link border-end border-end-sm-0 border-bottom-sm text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center" id="attributesTab" data-bs-toggle="tab" data-bs-target="#attributesTabContent" role="tab" aria-controls="attributesTabContent" aria-selected="false">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="sliders"></span>
                <span class="d-none d-sm-inline">Attributes</span>
            </a>
            <a class="nav-link text-center text-sm-start cursor-pointer outline-none d-sm-flex align-items-sm-center" id="advancedTab" data-bs-toggle="tab" data-bs-target="#advancedTabContent" role="tab" aria-controls="advancedTabContent" aria-selected="false">
                <span class="me-sm-2 fs-4 nav-icons" data-feather="lock"></span>
                <span class="d-none d-sm-inline">Product Images</span>
            </a>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="tab-content py-3 ps-sm-4 h-100">
            <div class="tab-pane fade show active" id="pricingTabContent" role="tabpanel">
                <h4 class="mb-3 d-sm-none">Pricing</h4>
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Base price</h5><input class="form-control" type="number" step="0.01" min="0" placeholder="" name="base_price" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Discount price</h5><input class="form-control" type="number" step="0.01" min="0" placeholder="" name="discount_price" />
                    </div>
                </div>
            </div>
            <!-- <div class="tab-pane fade h-100" id="restockTabContent" role="tabpanel" aria-labelledby="restockTab">
                <div class="d-flex flex-column h-100">
                    <h5 class="mb-3 text-body-highlight">Add to Stock</h5>
                    <div class="row g-3 flex-1 mb-4">
                        <div class="col-sm-7"><input class="form-control" type="number" placeholder="Quantity" /></div>
                        <div class="col-sm"><button class="btn btn-primary" type="button"><span class="fa-solid fa-check me-1 fs-10"></span>Confirm</button></div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 200px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-body-highlight fw-bold py-1">Product in stock now:</td>
                                <td class="text-body-tertiary fw-semibold py-1">$1,090<button class="btn p-0" type="button"><span class="fa-solid fa-rotate text-body ms-1" style="--phoenix-text-opacity: .6;"></span></button></td>
                            </tr>
                            <tr>
                                <td class="text-body-highlight fw-bold py-1">Product in transit:</td>
                                <td class="text-body-tertiary fw-semibold py-1">5000</td>
                            </tr>
                            <tr>
                                <td class="text-body-highlight fw-bold py-1">Last time restocked:</td>
                                <td class="text-body-tertiary fw-semibold py-1">30th June, 2021</td>
                            </tr>
                            <tr>
                                <td class="text-body-highlight fw-bold py-1">Total stock over lifetime:</td>
                                <td class="text-body-tertiary fw-semibold py-1">20,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->
            <!-- <div class="tab-pane fade h-100" id="shippingTabContent" role="tabpanel" aria-labelledby="shippingTab">
                <div class="d-flex flex-column h-100">
                    <h5 class="mb-3 text-body-highlight">Shipping Type</h5>
                    <div class="flex-1">
                        <div class="mb-4">
                            <div class="form-check mb-1"><input class="form-check-input" type="radio" name="shippingRadio" id="fullfilledBySeller" /><label class="form-check-label fs-8 text-body" for="fullfilledBySeller">Fullfilled by Seller</label></div>
                            <div class="ps-4">
                                <p class="text-body-secondary fs-9 mb-0">You'll be responsible for product delivery. <br />Any damage or delay during shipping may cost you a Damage fee.</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="form-check mb-1"><input class="form-check-input" type="radio" name="shippingRadio" id="fullfilledByPhoenix" checked="checked" /><label class="form-check-label fs-8 text-body d-flex align-items-center" for="fullfilledByPhoenix">Fullfilled by Phoenix <span class="badge badge-phoenix badge-phoenix-warning fs-10 ms-2">Recommended</span></label></div>
                            <div class="ps-4">
                                <p class="text-body-secondary fs-9 mb-0">Your product, Our responsibility.<br />For a measly fee, we will handle the delivery process for you.</p>
                            </div>
                        </div>
                    </div>
                    <p class="fs-9 fw-semibold mb-0">See our <a class="fw-bold" href="#!">Delivery terms and conditions </a>for details.</p>
                </div>
            </div> -->
            <!-- <div class="tab-pane fade" id="productsTabContent" role="tabpanel" aria-labelledby="productsTab">
                <h5 class="mb-3 text-body-highlight">Global Delivery</h5>
                <div class="mb-3">
                    <div class="form-check"><input class="form-check-input" type="radio" name="deliveryRadio" id="worldwideDelivery" /><label class="form-check-label fs-8 text-body" for="worldwideDelivery">Worldwide delivery</label></div>
                    <div class="ps-4">
                        <p class="fs-9 mb-0 text-body-secondary">Only available with Shipping method: <a href="#!">Fullfilled by Phoenix</a></p>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check"><input class="form-check-input" type="radio" name="deliveryRadio" checked="checked" id="selectedCountry" /><label class="form-check-label fs-8 text-body" for="selectedCountry">Selected Countries</label></div>
                    <div class="ps-4" style="max-width: 350px;"><select class="form-select ps-4" id="organizerMultiple" data-choices="data-choices" multiple="multiple" data-options='{"removeItemButton":true,"placeholder":true}'>
                            <option value="">Type Country name</option>
                            <option>United States of America</option>
                            <option>United Kingdom</option>
                            <option>Canada</option>
                            <option>Mexico</option>
                        </select></div>
                </div>
                <div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="deliveryRadio" id="localDelivery" /><label class="form-check-label fs-8 text-body" for="localDelivery">Local delivery</label></div>
                    <p class="fs-9 ms-4 mb-0 text-body-secondary">Deliver to your country of residence <a href="#!">Change profile address </a></p>
                </div>
            </div> -->
            <div class="tab-pane fade" id="attributesTabContent" role="tabpanel" aria-labelledby="attributesTab">
                <h5 class="mb-3 text-body-highlight">Attributes</h5>

                <?php foreach ($attribute_types as $attribute_type):
                    $attribute_id = $attribute_type['attribute_type_id'];
                    $attribute_name_safe = strtolower(preg_replace('/\s+/', '-', $attribute_type['name']));
                    $is_multi_select = in_array(strtolower($attribute_type['name']), ['storage', 'color']);
                ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <!-- Checkbox to enable attribute -->
                            <input class="form-check-input"
                                id="attribute-checkbox-<?= $attribute_name_safe ?>"
                                type="checkbox"
                                name="selected_attributes[]"
                                value="<?= $attribute_id ?>"  />

                            <label class="form-check-label text-body fs-8" for="attribute-checkbox-<?= $attribute_name_safe ?>">
                                <?= htmlspecialchars($attribute_type['name']) ?>
                                <?php if ($is_multi_select): ?>
                                    <span class="badge bg-info ms-2">Multi-select</span>
                                <?php endif; ?>
                            </label>

                            <!-- Select dropdown for this attribute -->
                            <div class="product-variant-select-menu mt-2">
                                <?php if ($is_multi_select): ?>
                                    <!-- Multi-select for Storage and Color -->
                                    <select class="form-select mb-3"
                                        name="attribute_values[<?= $attribute_id ?>][]"
                                        data-choices
                                        data-options='{"placeholder":true, "removeItemButton":true, "maxItemCount":5}' 
                                        multiple>
                                        <option value="">-- Select <?= htmlspecialchars($attribute_type['name']) ?> options --</option>
                                        <?php
                                        $get_attribute_by_values = mysqli_query(
                                            $conn,
                                            "SELECT * FROM attribute_value WHERE attribute_type_id = '{$attribute_id}' ORDER BY value"
                                        );

                                        foreach ($get_attribute_by_values as $value): ?>
                                            <option value="<?= $value['attribute_value_id'] ?>">
                                                <?= htmlspecialchars($value['value']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <!-- Single-select for other attributes -->
                                    <select class="form-select mb-3"
                                        name="attribute_values[<?= $attribute_id ?>]"
                                        data-choices
                                        data-options='{"placeholder":true}' >
                                        <option value="">-- Select <?= htmlspecialchars($attribute_type['name']) ?> --</option>
                                        <?php
                                        $get_attribute_by_values = mysqli_query(
                                            $conn,
                                            "SELECT * FROM attribute_value WHERE attribute_type_id = '{$attribute_id}' ORDER BY value"
                                        );

                                        foreach ($get_attribute_by_values as $value): ?>
                                            <option value="<?= $value['attribute_value_id'] ?>">
                                                <?= htmlspecialchars($value['value']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Variant Preview Section -->
                <div class="mt-4 p-3 bg-light rounded" id="variantPreview" style="display: none;">
                    <h6 class="mb-3 text-body-highlight">Product Variants Preview</h6>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Each variant can have its own price and stock quantity. If left empty, the base product price and stock will be used.
                    </div>
                    <div id="variantList" class="row g-3">
                        <!-- Variants will be generated here dynamically -->
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="testVariantData()">
                            <i class="fas fa-eye me-1"></i>Test Variant Data
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="advancedTabContent" role="tabpanel" aria-labelledby="advancedTab">
                <h5 class="mb-3 text-body-highlight">Product images (min 3, max 5)</h5>
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Product Image 1</h5>
                        <input class="form-control" name="product_image_1" type="url" placeholder="https://product-image-url.com" required />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Product Image 2</h5>
                        <input class="form-control" name="product_image_2" type="url" placeholder="https://product-image-url.com" required />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Product Image 3</h5>
                        <input class="form-control" name="product_image_3" type="url" placeholder="https://product-image-url.com" required />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Product Image 4</h5>
                        <input class="form-control" name="product_image_4" type="url" placeholder="https://product-image-url.com" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-2 text-body-highlight">Product Image 5</h5>
                        <input class="form-control" name="product_image_5" type="url" placeholder="https://product-image-url.com" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all attribute checkboxes and select elements
    const attributeCheckboxes = document.querySelectorAll('input[name="selected_attributes[]"]');
    const attributeSelects = document.querySelectorAll('select[name^="attribute_values"]');
    const variantPreview = document.getElementById('variantPreview');
    const variantList = document.getElementById('variantList');

    // Function to generate variant preview
    function generateVariantPreview() {
        const selectedAttributes = {};
        let hasMultiSelect = false;

        // Collect selected attributes
        attributeCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const attributeId = checkbox.value;
                const selectElement = document.querySelector(`select[name="attribute_values[${attributeId}][]"], select[name="attribute_values[${attributeId}]"]`);
                
                if (selectElement) {
                    if (selectElement.multiple) {
                        // Multi-select (Storage/Color)
                        const selectedValues = Array.from(selectElement.selectedOptions).map(option => ({
                            id: option.value,
                            text: option.textContent
                        }));
                        if (selectedValues.length > 0) {
                            selectedAttributes[attributeId] = selectedValues;
                            hasMultiSelect = true;
                        }
                    } else {
                        // Single-select
                        if (selectElement.value) {
                            selectedAttributes[attributeId] = [{
                                id: selectElement.value,
                                text: selectElement.options[selectElement.selectedIndex].textContent
                            }];
                        }
                    }
                }
            }
        });

        // Show/hide variant preview
        if (hasMultiSelect && Object.keys(selectedAttributes).length > 0) {
            variantPreview.style.display = 'block';
            generateVariantCards(selectedAttributes);
        } else {
            variantPreview.style.display = 'none';
        }
    }

    // Function to generate variant cards
    function generateVariantCards(attributes) {
        const multiSelectAttributes = [];
        const singleSelectAttributes = [];

        // Separate multi-select and single-select attributes
        Object.entries(attributes).forEach(([attributeId, values]) => {
            if (values.length > 1) {
                multiSelectAttributes.push({ attributeId, values });
            } else {
                singleSelectAttributes.push({ attributeId, values: values[0] });
            }
        });

        // Generate all combinations
        const combinations = generateCombinations(multiSelectAttributes);
        
        // Get base price from the form
        const basePriceInput = document.querySelector('input[name="base_price"]');
        const basePrice = basePriceInput ? parseFloat(basePriceInput.value) || 0 : 0;
        
        // Create variant cards
        let variantCardsHTML = '';
        combinations.forEach((combination, index) => {
            const variantName = generateVariantName(combination, singleSelectAttributes);
            variantCardsHTML += `
                <div class="col-md-6">
                    <div class="card border h-100">
                        <div class="card-header bg-light py-2">
                            <h6 class="card-title mb-0 text-primary">Variant ${index + 1}</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="card-text small mb-3 text-muted">${variantName}</p>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">Stock Quantity</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           placeholder="0" name="variant_stock[${index}]" min="0" 
                                           title="Leave empty to use base product stock">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">Price</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           placeholder="${basePrice}" name="variant_price[${index}]" 
                                           step="0.01" min="0" 
                                           title="Leave empty to use base product price">
                                </div>
                            </div>
                            <input type="hidden" name="variant_combination[${index}]" value="${JSON.stringify(combination)}">
                        </div>
                    </div>
                </div>
            `;
        });

        variantList.innerHTML = variantCardsHTML;
        
        // Add event listeners to the new price inputs to update placeholders when base price changes
        const priceInputs = variantList.querySelectorAll('input[name^="variant_price"]');
        priceInputs.forEach(input => {
            if (!input.value) {
                input.placeholder = basePrice;
            }
        });
    }

    // Function to generate combinations
    function generateCombinations(multiSelectAttributes) {
        if (multiSelectAttributes.length === 0) return [];

        const combinations = [];
        const generateCombos = (current, index) => {
            if (index === multiSelectAttributes.length) {
                combinations.push([...current]);
                return;
            }

            const attribute = multiSelectAttributes[index];
            attribute.values.forEach(value => {
                current.push({ attributeId: attribute.attributeId, value });
                generateCombos(current, index + 1);
                current.pop();
            });
        };

        generateCombos([], 0);
        return combinations;
    }

    // Function to generate variant name
    function generateVariantName(combination, singleSelectAttributes) {
        const parts = [];
        
        // Add multi-select attributes
        combination.forEach(item => {
            parts.push(item.value.text);
        });
        
        // Add single-select attributes
        singleSelectAttributes.forEach(item => {
            parts.push(item.values.text);
        });
        
        return parts.join(' - ');
    }

    // Add event listeners
    attributeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', generateVariantPreview);
    });

    attributeSelects.forEach(select => {
        select.addEventListener('change', generateVariantPreview);
    });
    
    // Add event listener for base price changes
    const basePriceInput = document.querySelector('input[name="base_price"]');
    if (basePriceInput) {
        basePriceInput.addEventListener('input', function() {
            const basePrice = parseFloat(this.value) || 0;
            const priceInputs = document.querySelectorAll('input[name^="variant_price"]');
            priceInputs.forEach(input => {
                if (!input.value) {
                    input.placeholder = basePrice;
                }
            });
        });
    }
    
    // Add form validation before submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const variantPreview = document.getElementById('variantPreview');
            if (variantPreview.style.display !== 'none') {
                // Check if all variant fields are filled
                const stockInputs = variantPreview.querySelectorAll('input[name^="variant_stock"]');
                const priceInputs = variantPreview.querySelectorAll('input[name^="variant_price"]');
                
                console.log('Form submission - Variant fields found:', {
                    stockInputs: stockInputs.length,
                    priceInputs: priceInputs.length
                });
                
                let hasEmptyFields = false;
                stockInputs.forEach((input, index) => {
                    const stockValue = input.value.trim();
                    const priceValue = priceInputs[index] ? priceInputs[index].value.trim() : '';
                    
                    console.log(`Variant ${index + 1}:`, {
                        stock: stockValue,
                        price: priceValue
                    });
                    
                    if (stockValue === '' || priceValue === '') {
                        hasEmptyFields = true;
                    }
                });
                
                if (hasEmptyFields) {
                    if (!confirm('Some variant fields are empty. Variants with empty fields will use the base product price and stock. Continue anyway?')) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                // Ensure all variant fields are properly included in the form
                stockInputs.forEach((input, index) => {
                    if (!input.name) {
                        input.name = `variant_stock[${index}]`;
                    }
                });
                
                priceInputs.forEach((input, index) => {
                    if (!input.name) {
                        input.name = `variant_price[${index}]`;
                    }
                });
            }
        });
    }

    // Test function to verify variant data
    window.testVariantData = function() {
        const variantPreview = document.getElementById('variantPreview');
        if (variantPreview.style.display === 'none') {
            alert('No variants to test. Please select Storage and/or Color attributes first.');
            return;
        }
        
        const stockInputs = variantPreview.querySelectorAll('input[name^="variant_stock"]');
        const priceInputs = variantPreview.querySelectorAll('input[name^="variant_price"]');
        const combinationInputs = variantPreview.querySelectorAll('input[name^="variant_combination"]');
        
        let testData = {
            totalVariants: stockInputs.length,
            variants: []
        };
        
        stockInputs.forEach((input, index) => {
            const stockValue = input.value.trim();
            const priceValue = priceInputs[index] ? priceInputs[index].value.trim() : '';
            const combinationValue = combinationInputs[index] ? combinationInputs[index].value : '';
            
            testData.variants.push({
                variant: index + 1,
                stock: stockValue || 'Empty (will use base stock)',
                price: priceValue || 'Empty (will use base price)',
                combination: combinationValue ? JSON.parse(combinationValue) : 'No combination data'
            });
        });
        
        console.log('Variant Test Data:', testData);
        alert(`Variant test completed!\n\nTotal variants: ${testData.totalVariants}\n\nCheck browser console for detailed data.`);
    };
});
</script>