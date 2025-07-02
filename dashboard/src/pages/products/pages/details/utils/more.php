<?php
// Admin-focused product details tabs
?>

<section class="py-0 my-5">
    <div class="container-small">
        <ul class="nav nav-underline fs-9 mb-4" id="productTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="inventory-tab" data-bs-toggle="tab" href="#tab-inventory" role="tab">
                    <span class="fas fa-boxes me-2"></span>Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#tab-analytics" role="tab">
                    <span class="fas fa-chart-line me-2"></span>Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="attributes-tab" data-bs-toggle="tab" href="#tab-attributes" role="tab">
                    <span class="fas fa-tags me-2"></span>Attributes
                </a>
            </li>
        </ul>

                <div class="tab-content" id="productTabContent">
            <!-- Inventory Tab -->
            <div class="tab-pane fade show active" id="tab-inventory" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Inventory Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary mb-2"><?= number_format($product_data['stock_quantity']) ?></h3>
                                        <p class="text-muted mb-0">Current Stock</p>
                                    </div>
                                </div>
                                                </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-success mb-2"><?= number_format($order_stats['total_quantity_sold'] ?? 0) ?></h3>
                                        <p class="text-muted mb-0">Total Sold</p>
                                    </div>
                                </div>
                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>

            <!-- Analytics Tab -->
            <div class="tab-pane fade" id="tab-analytics" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sales Analytics</h5>
                                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-primary"><?= number_format($order_stats['total_orders'] ?? 0) ?></h4>
                                    <p class="text-muted mb-0">Total Orders</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-success"><?= number_format($order_stats['total_quantity_sold'] ?? 0) ?></h4>
                                    <p class="text-muted mb-0">Units Sold</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-warning"><?= number_format($order_stats['total_revenue'] ?? 0) ?></h4>
                                    <p class="text-muted mb-0">Total Revenue</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-info"><?= $order_stats['total_orders'] > 0 ? number_format($order_stats['total_revenue'] / $order_stats['total_orders'], 2) : 0 ?></h4>
                                    <p class="text-muted mb-0">Avg Order Value</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attributes Tab -->
            <div class="tab-pane fade" id="tab-attributes" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Attributes</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Fetch product attributes from database
                        $product_id = $product_data['product_id'];
                        
                        // Check if product has variants
                        $variants_query = mysqli_query($conn, "
                            SELECT pv.*, 
                                   size_val.value as size_value,
                                   color_val.value as color_value
                            FROM product_variants pv
                            LEFT JOIN attribute_value size_val ON pv.size_id = size_val.attribute_value_id
                            LEFT JOIN attribute_value color_val ON pv.color_id = color_val.attribute_value_id
                            WHERE pv.product_id = '$product_id' AND pv.is_active = 1
                            ORDER BY pv.variant_id
                        ");
                        
                        $has_variants = mysqli_num_rows($variants_query) > 0;
                        
                        // Fetch single-value attributes
                        $attributes_query = mysqli_query($conn, "
                            SELECT at.name as attribute_name, av.value as attribute_value
                            FROM product_attributes pa
                            JOIN attribute_type at ON pa.attribute_type_id = at.attribute_type_id
                            JOIN attribute_value av ON pa.attribute_value_id = av.attribute_value_id
                            WHERE pa.product_id = '$product_id'
                            ORDER BY at.name
                        ");
                        
                        $single_attributes = [];
                        while ($attr = mysqli_fetch_assoc($attributes_query)) {
                            $single_attributes[] = $attr;
                        }
                        ?>
                        
                        <!-- Single Value Attributes -->
                        <?php if (!empty($single_attributes)): ?>
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">Product Specifications</h6>
                        <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                            <th width="30%">Attribute</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                        <?php foreach ($single_attributes as $attribute): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($attribute['attribute_name']) ?></td>
                                            <td><?= htmlspecialchars($attribute['attribute_value']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Product Variants -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-success mb-0">Product Variants</h6>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                                    <i class="fas fa-plus me-1"></i>Add Variant
                                </button>
                            </div>
                            <?php
                            // Reset the query result pointer and fetch data
                            mysqli_data_seek($variants_query, 0);
                            
                            $variants_data = [];
                            while ($variant = mysqli_fetch_assoc($variants_query)) {
                                $variants_data[] = $variant;
                            }
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variant</th>
                                            <th>Storage</th>
                                            <th>Color</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($has_variants): ?>
                                        <?php
                                            $variant_count = 1;
                                            foreach ($variants_data as $variant): 
                                        ?>
                                        <tr>
                                            <td class="fw-semibold">Variant <?= $variant_count ?></td>
                                            <td>
                                                <?php if ($variant['size_value']): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($variant['size_value']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($variant['color_value']): ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($variant['color_value']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-success">$<?= number_format($variant['price']) ?></td>
                                            <td>
                                                <span class="badge <?= $variant['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= number_format($variant['stock_quantity']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($variant['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-danger btn-sm" 
                                                            onclick="deleteVariant(<?= $variant['variant_id'] ?>, '<?= htmlspecialchars($product_data['name']) ?>')"
                                                            title="Delete Variant">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                            $variant_count++;
                                            endforeach; 
                                        ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">No variants found. Add one to get started.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        
                        <!-- Add Variant Modal -->
                        <div class="modal fade" id="addVariantModal" tabindex="-1" aria-labelledby="addVariantModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addVariantModalLabel">Add New Variant</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="./src/services/products/add_variant.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="product_id" value="<?= $product_data['product_id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label for="storage" class="form-label">Storage</label>
                                                <select class="form-select" name="storage_id" id="storage">
                                                    <option value="">Select Storage</option>
                                                    <?php
                                                    $storage_query = mysqli_query($conn, "
                                                        SELECT av.attribute_value_id, av.value 
                                                        FROM attribute_value av 
                                                        JOIN attribute_type at ON av.attribute_type_id = at.attribute_type_id 
                                                        WHERE at.name = 'Storage' 
                                                        ORDER BY av.value
                                                    ");
                                                    while ($storage = mysqli_fetch_assoc($storage_query)):
                                                    ?>
                                                    <option value="<?= $storage['attribute_value_id'] ?>"><?= htmlspecialchars($storage['value']) ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="color" class="form-label">Color</label>
                                                <select class="form-select" name="color_id" id="color">
                                                    <option value="">Select Color</option>
                                                    <?php
                                                    $color_query = mysqli_query($conn, "
                                                        SELECT av.attribute_value_id, av.value 
                                                        FROM attribute_value av 
                                                        JOIN attribute_type at ON av.attribute_type_id = at.attribute_type_id 
                                                        WHERE at.name = 'Color' 
                                                        ORDER BY av.value
                                                    ");
                                                    while ($color = mysqli_fetch_assoc($color_query)):
                                                    ?>
                                                    <option value="<?= $color['attribute_value_id'] ?>"><?= htmlspecialchars($color['value']) ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="variant_price" class="form-label">Price</label>
                                                <input type="number" class="form-control" name="price" id="variant_price" step="0.01" min="0" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="variant_stock" class="form-label">Stock Quantity</label>
                                                <input type="number" class="form-control" name="stock_quantity" id="variant_stock" min="0" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Add Variant</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        function deleteVariant(variantId, productName) {
                            if (confirm(`Are you sure you want to delete this variant from "${productName}"?`)) {
                                // Create a form to submit the delete request
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = './src/services/products/delete_variant.php';
                                
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'variant_id';
                                input.value = variantId;
                                
                                form.appendChild(input);
                                document.body.appendChild(form);
                                form.submit();
                            }
                        }
                        </script>
                        
                        <!-- No Attributes Found -->
                        <?php if (empty($single_attributes) && !$has_variants): ?>
                        <div class="text-center py-4">
                            <div class="text-muted mb-3">
                                <i class="fas fa-tags fa-3x"></i>
                            </div>
                            <p class="text-muted mb-0">This product has no variants or specifications.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>