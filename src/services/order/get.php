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
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
if (empty($customer_id)) {
    errorRedirectWithMessage('Please sign in to view orders', '../../../signin');
}

// Get order ID from request
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    errorRedirectWithMessage('Order ID is required');
}

// Get order details
$order_query = mysqli_prepare(
    $conn,
    "SELECT o.*, ua.* 
     FROM orders o 
     JOIN user_addresses ua ON o.shipping_address_id = ua.address_id 
     WHERE o.order_id = ? AND o.user_id = ?"
);
mysqli_stmt_bind_param($order_query, "si", $order_id, $customer_id);
mysqli_stmt_execute($order_query);
$order_result = mysqli_stmt_get_result($order_query);
$order_data = mysqli_fetch_assoc($order_result);

if (!$order_data) {
    errorRedirectWithMessage('Order not found');
}

// Get order items
$items_query = mysqli_prepare(
    $conn,
    "SELECT oi.*, p.name, 
     (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.product_id 
     WHERE oi.order_id = ?"
);
mysqli_stmt_bind_param($items_query, "s", $order_id);
mysqli_stmt_execute($items_query);
$items_result = mysqli_stmt_get_result($items_query);
$order_items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $order_items[] = $item;
}

// Format order data
$formatted_order = [
    'order_id' => $order_data['order_id'],
    'order_date' => $order_data['order_date'],
    'status' => $order_data['status'],
    'total_amount' => $order_data['total_amount'],
    'shipping_cost' => $order_data['shipping_cost'],
    'tax_amount' => $order_data['tax_amount'],
    'payment_method' => $order_data['payment_method'],
    'payment_status' => $order_data['payment_status'],
    'shipping_method' => $order_data['shipping_method'],
    'shipping_address' => [
        'address_line1' => $order_data['address_line1'],
        'address_line2' => $order_data['address_line2'],
        'district' => $order_data['district'],
        'sector' => $order_data['sector'],
        'cell' => $order_data['cell'],
        'country' => $order_data['country'],
        'phone_number' => $order_data['phone_number']
    ],
    'items' => array_map(function($item) {
        return [
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'subtotal' => $item['subtotal'],
            'image_url' => $item['image_url']
        ];
    }, $order_items)
];

// Store order data in session for the view to use
$_SESSION['order_details'] = $formatted_order;
successRedirectWithMessage('Order details retrieved successfully', "../../../order/details?order_id=$order_id"); 