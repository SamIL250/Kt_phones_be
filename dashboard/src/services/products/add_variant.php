<?php
include '../../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../products');
    exit();
}

$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
$price = floatval($_POST['price']);
$stock_quantity = intval($_POST['stock_quantity']);

// Handle nullable foreign keys: convert empty strings to NULL
$storage_id = !empty($_POST['storage_id']) ? intval($_POST['storage_id']) : NULL;
$color_id = !empty($_POST['color_id']) ? intval($_POST['color_id']) : NULL;

// Validation
if (empty($product_id)) {
    $_SESSION['notification'] = 'Product ID is required.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($price <= 0) {
    $_SESSION['notification'] = 'Price must be greater than 0.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($stock_quantity < 0) {
    $_SESSION['notification'] = 'Stock quantity cannot be negative.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Check if at least one attribute is selected
if (!$storage_id && !$color_id) {
    $_SESSION['notification'] = 'At least one attribute (Storage or Color) must be selected.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Check if this variant combination already exists
$check_query = mysqli_prepare($conn, "
    SELECT variant_id FROM product_variants 
    WHERE product_id = ? AND 
          (? IS NULL AND size_id IS NULL OR size_id = ?) AND
          (? IS NULL AND color_id IS NULL OR color_id = ?)
");
$check_query->bind_param('siiii', $product_id, $storage_id, $storage_id, $color_id, $color_id);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows > 0) {
    $_SESSION['notification'] = 'This variant combination already exists.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Get product SKU for variant SKU generation
$product_query = mysqli_query($conn, "SELECT sku FROM products WHERE product_id = '$product_id'");
$product_data = mysqli_fetch_assoc($product_query);
$base_sku = $product_data['sku'];

// Generate unique variant SKU
$sku_query = mysqli_query($conn, "
    SELECT COUNT(*) as count FROM product_variants WHERE product_id = '$product_id'
");
$sku_data = mysqli_fetch_assoc($sku_query);
$variant_number = $sku_data['count'] + 1;
$variant_sku = $base_sku . '-' . $variant_number;

// Function to update the parent product's stock quantity based on its variants
function update_product_stock_from_variants($conn, $product_id) {
    if (!$product_id) return;
    
    // Calculate the total stock from all variants of the product
    $stock_query = mysqli_query($conn, "
        SELECT SUM(stock_quantity) as total_stock 
        FROM product_variants 
        WHERE product_id = '$product_id' AND is_active = 1
    ");
    
    $stock_data = mysqli_fetch_assoc($stock_query);
    $total_stock = $stock_data['total_stock'] ?? 0;
    
    // Update the stock_quantity in the parent products table
    $update_query = mysqli_prepare($conn, "
        UPDATE products SET stock_quantity = ? WHERE product_id = ?
    ");
    $update_query->bind_param('is', $total_stock, $product_id);
    $update_query->execute();
}

// Insert the new variant
$insert_query = mysqli_prepare($conn, "
    INSERT INTO product_variants (
        product_id, size_id, color_id, sku, price, stock_quantity, is_active
    ) VALUES (?, ?, ?, ?, ?, ?, 1)
");

$insert_query->bind_param('siissi', $product_id, $storage_id, $color_id, $variant_sku, $price, $stock_quantity);

if ($insert_query->execute()) {
    // Update the parent product's total stock
    update_product_stock_from_variants($conn, $product_id);
    $_SESSION['notification'] = 'Variant added successfully.';
} else {
    $_SESSION['notification'] = 'Failed to add variant. Please try again.';
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?> 