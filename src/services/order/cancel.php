<?php
require_once '../../../config/config.php';

session_start();

function errorRedirectWithMessage($message, $path = '../../../profile'): void
{
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => $message
    ];
    header("location:$path");
    exit();
}

function successRedirectWithMessage($message, $path = '../../../profile'): void
{
    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => $message
    ];
    header("location:$path");
    exit();
}

// Get and validate customer ID
$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
if (empty($customer_id)) {
    errorRedirectWithMessage('Please sign in to cancel orders', '../../../signin');
}

// Get order ID from request
$order_id = $_POST['order_id'] ?? null;
if (!$order_id) {
    errorRedirectWithMessage('Order ID is required');
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Check if order exists and belongs to user
    $check_query = mysqli_prepare(
        $conn,
        "SELECT status FROM orders WHERE order_id = ? AND user_id = ?"
    );
    mysqli_stmt_bind_param($check_query, "si", $order_id, $customer_id);
    mysqli_stmt_execute($check_query);
    $check_result = mysqli_stmt_get_result($check_query);
    $order_data = mysqli_fetch_assoc($check_result);

    if (!$order_data) {
        throw new Exception('Order not found');
    }

    // Check if order can be cancelled
    if ($order_data['status'] !== 'pending') {
        throw new Exception('Only pending orders can be cancelled');
    }

    // Get order items to restore stock
    $items_query = mysqli_prepare(
        $conn,
        "SELECT product_id, quantity FROM order_items WHERE order_id = ?"
    );
    mysqli_stmt_bind_param($items_query, "s", $order_id);
    mysqli_stmt_execute($items_query);
    $items_result = mysqli_stmt_get_result($items_query);

    // Restore stock for each item
    while ($item = mysqli_fetch_assoc($items_result)) {
        $update_stock = mysqli_prepare(
            $conn,
            "UPDATE products 
             SET stock_quantity = stock_quantity + ? 
             WHERE product_id = ?"
        );
        mysqli_stmt_bind_param($update_stock, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($update_stock);
    }

    // Update order status to cancelled
    $update_order = mysqli_prepare(
        $conn,
        "UPDATE orders SET status = 'cancelled' WHERE order_id = ?"
    );
    mysqli_stmt_bind_param($update_order, "s", $order_id);
    mysqli_stmt_execute($update_order);

    // Commit transaction
    mysqli_commit($conn);

    successRedirectWithMessage('Order cancelled successfully');

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    errorRedirectWithMessage($e->getMessage());
} 