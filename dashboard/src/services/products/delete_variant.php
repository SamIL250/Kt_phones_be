<?php
include '../../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../products');
    exit();
}

$variant_id = intval($_POST['variant_id']);

// Validation
if ($variant_id <= 0) {
    $_SESSION['notification'] = 'Invalid variant ID.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Check if variant exists and get product info
$check_query = mysqli_prepare($conn, "
    SELECT pv.variant_id, p.name as product_name 
    FROM product_variants pv 
    JOIN products p ON pv.product_id = p.product_id 
    WHERE pv.variant_id = ?
");
$check_query->bind_param('i', $variant_id);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows === 0) {
    $_SESSION['notification'] = 'Variant not found.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$variant_data = $result->fetch_assoc();

// Check if variant is in any active orders
$order_check_query = mysqli_prepare($conn, "
    SELECT COUNT(*) as count FROM order_items oi
    JOIN product_variants pv ON oi.product_id = pv.product_id
    WHERE pv.variant_id = ? AND oi.order_id IN (
        SELECT order_id FROM orders WHERE status IN ('pending', 'processing', 'shipped')
    )
");
$order_check_query->bind_param('i', $variant_id);
$order_check_query->execute();
$order_result = $order_check_query->get_result();
$order_data = $order_result->fetch_assoc();

if ($order_data['count'] > 0) {
    $_SESSION['notification'] = 'Cannot delete variant: It is associated with active orders.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

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

// Store product_id before deleting the variant
$product_id = $variant_data['product_id'];

// Delete the variant
$delete_query = mysqli_prepare($conn, "DELETE FROM product_variants WHERE variant_id = ?");
$delete_query->bind_param('i', $variant_id);

if ($delete_query->execute()) {
    // Update the parent product's total stock
    update_product_stock_from_variants($conn, $product_id);
    $_SESSION['notification'] = 'Variant deleted successfully.';
} else {
    $_SESSION['notification'] = 'Failed to delete variant. Please try again.';
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?> 