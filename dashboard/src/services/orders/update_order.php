<?php
include '../../../config/config.php';
session_start();

if (!isset($_POST['order_id'])) {
    $_SESSION['notification'] = "Invalid order update request";
    header('Location: ../../../orders.php');
    exit();
}

$order_id = $_POST['order_id'];
$new_status = $_POST['status'];
$payment_status = $_POST['payment_status'];
$tracking_number = $_POST['tracking_number'];
$shipping_method = $_POST['shipping_method'];
$shipping_cost = $_POST['shipping_cost'];
$tax_amount = $_POST['tax_amount'];

// Get current order status
$status_query = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = '$order_id'");
$current_order = mysqli_fetch_assoc($status_query);

if (!$current_order) {
    $_SESSION['notification'] = "Order not found";
    header('Location: ../../../orders.php');
    exit();
}

// Validate status changes
$current_status = $current_order['status'];

// Prevent changing status of delivered orders
if ($current_status == 'delivered' && $new_status != 'delivered') {
    $_SESSION['notification'] = "Cannot change status of delivered orders. Please contact support for assistance.";
    header('Location: ../../../order-edit?order=' . $order_id);
    exit();
}

// Prevent changing shipped orders to pending/processing
if ($current_status == 'shipped' && ($new_status == 'pending' || $new_status == 'processing')) {
    $_SESSION['notification'] = "Cannot change shipped orders back to pending/processing status. Please contact support for assistance.";
    header('Location: ../../../order-edit?order=' . $order_id);
    exit();
}

// Get shipping and billing address IDs for this order
$order_info = mysqli_query($conn, "SELECT shipping_address_id, billing_address_id FROM orders WHERE order_id = '$order_id'");
$order_info_row = mysqli_fetch_assoc($order_info);

$shipping_address_id = $order_info_row['shipping_address_id'];
$billing_address_id = $order_info_row['billing_address_id'];

// Update shipping address if fields are provided
if (isset($_POST['shipping_address_line1'])) {
    $shipping_address_line1 = mysqli_real_escape_string($conn, $_POST['shipping_address_line1']);
    $shipping_district = mysqli_real_escape_string($conn, $_POST['shipping_district']);
    $shipping_sector = mysqli_real_escape_string($conn, $_POST['shipping_sector']);
    $shipping_phone = mysqli_real_escape_string($conn, $_POST['shipping_phone']);
    mysqli_query(
        $conn,
        "UPDATE addresses SET 
            address_line1 = '$shipping_address_line1',
            district = '$shipping_district',
            sector = '$shipping_sector',
            phone_number = '$shipping_phone'
        WHERE address_id = '$shipping_address_id'"
    );
}

// Update billing address if fields are provided
if (isset($_POST['billing_address_line1'])) {
    $billing_address_line1 = mysqli_real_escape_string($conn, $_POST['billing_address_line1']);
    $billing_district = mysqli_real_escape_string($conn, $_POST['billing_district']);
    $billing_sector = mysqli_real_escape_string($conn, $_POST['billing_sector']);
    $billing_phone = mysqli_real_escape_string($conn, $_POST['billing_phone']);
    mysqli_query(
        $conn,
        "UPDATE addresses SET 
            address_line1 = '$billing_address_line1',
            district = '$billing_district',
            sector = '$billing_sector',
            phone_number = '$billing_phone'
        WHERE address_id = '$billing_address_id'"
    );
}

// Update order
$update = mysqli_query(
    $conn,
    "UPDATE orders 
    SET 
        status = '$new_status',
        payment_status = '$payment_status',
        tracking_number = '$tracking_number',
        shipping_method = '$shipping_method',
        shipping_cost = $shipping_cost,
        tax_amount = $tax_amount
    WHERE order_id = '$order_id'"
);

if ($update) {
    // If order is cancelled, restore stock
    if ($new_status == 'cancelled' && $current_status != 'cancelled') {
        $items_query = mysqli_query(
            $conn,
            "SELECT product_id, quantity FROM order_items WHERE order_id = '$order_id'"
        );

        while ($item = mysqli_fetch_assoc($items_query)) {
            mysqli_query(
                $conn,
                "UPDATE products 
                SET stock_quantity = stock_quantity + {$item['quantity']} 
                WHERE product_id = '{$item['product_id']}'"
            );
        }
    }

    $_SESSION['notification'] = "Order updated successfully";
    header('Location: ../../../order-details?order=' . $order_id);
} else {
    $_SESSION['notification'] = "Failed to update order: " . mysqli_error($conn);
    header('Location: ../../../order-edit?order=' . $order_id);
}
