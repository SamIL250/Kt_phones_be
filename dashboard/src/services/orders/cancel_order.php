<?php
include '../../../config/config.php';
session_start();

if (!isset($_GET['order'])) {
    $_SESSION['notification'] = "Invalid order request";
    header('Location: ../../../orders.php');
    exit();
}

$order_id = $_GET['order'];

// Get current order status
$status_query = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = '$order_id'");
$order = mysqli_fetch_assoc($status_query);

if (!$order) {
    $_SESSION['notification'] = "Order not found";
    header('Location: ../../../orders.php');
    exit();
}

// Check if order can be cancelled based on status
if ($order['status'] == 'shipped' || $order['status'] == 'delivered') {
    $_SESSION['notification'] = "Orders that have been shipped or delivered cannot be cancelled. Please contact support for assistance.";
    header('Location: ../../../orders.php');
    exit();
}

// Only allow cancellation if order is pending or processing
if ($order['status'] == 'pending' || $order['status'] == 'processing') {
    // Update order status to cancelled
    $update = mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE order_id = '$order_id'");

    if ($update) {
        // Get order items to restore stock
        $items_query = mysqli_query(
            $conn,
            "SELECT product_id, quantity FROM order_items WHERE order_id = '$order_id'"
        );

        // Restore stock quantities
        while ($item = mysqli_fetch_assoc($items_query)) {
            mysqli_query(
                $conn,
                "UPDATE products 
                SET stock_quantity = stock_quantity + {$item['quantity']} 
                WHERE product_id = '{$item['product_id']}'"
            );
        }

        $_SESSION['notification'] = "Order cancelled successfully. Stock quantities have been restored.";
        header('Location: ../../../orders.php');
    } else {
        $_SESSION['notification'] = "Failed to cancel order: " . mysqli_error($conn);
        header('Location: ../../../orders.php');
    }
} else {
    $_SESSION['notification'] = "Order cannot be cancelled in its current status: " . ucfirst($order['status']);
    header('Location: ../../../orders.php');
}
