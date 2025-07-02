<?php
include '../../../config/config.php';
session_start();

function setSuccessMessage($message, $product_id) {
    $_SESSION['notification'] = $message;
    header('Location: ../../../product?product=' . urlencode($product_id));
    exit();
}

function setErrorMessage($message, $product_id) {
    $_SESSION['notification'] = $message;
    header('Location: ../../../product?product=' . urlencode($product_id));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.', '');
}

$product_id = mysqli_real_escape_string($conn, $_POST['product_id'] ?? '');
$stock_change = intval($_POST['stock_change'] ?? 0);
$reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? '');
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

if (empty($product_id) || empty($reason)) {
    setErrorMessage('Product ID and reason are required.', $product_id);
}

if ($stock_change == 0) {
    setErrorMessage('Stock change cannot be zero.', $product_id);
}

$product_check = mysqli_prepare($conn, "SELECT product_id, stock_quantity FROM products WHERE product_id = ?");
$product_check->bind_param('s', $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    setErrorMessage('Product not found.', $product_id);
}

$product_data = $product_result->fetch_assoc();
$current_stock = $product_data['stock_quantity'];
$new_stock = $current_stock + $stock_change;

if ($new_stock < 0) {
    setErrorMessage('Stock cannot be negative. Current stock: ' . $current_stock . ', Change: ' . $stock_change, $product_id);
}

mysqli_begin_transaction($conn);

try {
    $update_stock = mysqli_prepare($conn, "UPDATE products SET stock_quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ?");
    $update_stock->bind_param('is', $new_stock, $product_id);
    if (!$update_stock->execute()) {
        throw new Exception('Failed to update stock');
    }
    // You can add a stock_history table insert here if needed
    mysqli_commit($conn);
    setSuccessMessage('Stock updated successfully. Old stock: ' . $current_stock . ', New stock: ' . $new_stock, $product_id);
} catch (Exception $e) {
    mysqli_rollback($conn);
    setErrorMessage('Internal server error: ' . $e->getMessage(), $product_id);
}

mysqli_close($conn);
?> 