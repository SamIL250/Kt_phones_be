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

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Get total count of orders
$count_query = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) as total FROM orders WHERE user_id = ?"
);
mysqli_stmt_bind_param($count_query, "i", $customer_id);
mysqli_stmt_execute($count_query);
$count_result = mysqli_stmt_get_result($count_query);
$total_count = mysqli_fetch_assoc($count_result)['total'];

// Get orders with pagination
$orders_query = mysqli_prepare(
    $conn,
    "SELECT o.*, 
     (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as total_items
     FROM orders o 
     WHERE o.user_id = ? 
     ORDER BY o.order_date DESC 
     LIMIT ? OFFSET ?"
);
mysqli_stmt_bind_param($orders_query, "iii", $customer_id, $limit, $offset);
mysqli_stmt_execute($orders_query);
$orders_result = mysqli_stmt_get_result($orders_query);

$orders = [];
while ($order = mysqli_fetch_assoc($orders_result)) {
    // Get first item image for order preview
    $image_query = mysqli_prepare(
        $conn,
        "SELECT pi.image_url 
         FROM order_items oi 
         JOIN product_images pi ON oi.product_id = pi.product_id 
         WHERE oi.order_id = ? AND pi.is_primary = 1 
         LIMIT 1"
    );
    mysqli_stmt_bind_param($image_query, "s", $order['order_id']);
    mysqli_stmt_execute($image_query);
    $image_result = mysqli_stmt_get_result($image_query);
    $image_data = mysqli_fetch_assoc($image_result);
    
    $orders[] = [
        'order_id' => $order['order_id'],
        'order_date' => $order['order_date'],
        'status' => $order['status'],
        'total_amount' => $order['total_amount'],
        'payment_status' => $order['payment_status'],
        'shipping_method' => $order['shipping_method'],
        'total_items' => $order['total_items'],
        'preview_image' => $image_data ? $image_data['image_url'] : null
    ];
}

// Store orders data in session for the view to use
$_SESSION['orders_list'] = [
    'orders' => $orders,
    'pagination' => [
        'total' => $total_count,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total_count / $limit)
    ]
];

successRedirectWithMessage('Orders retrieved successfully', '../../../orders'); 