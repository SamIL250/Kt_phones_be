<?php
header('Content-Type: application/json');
session_start();
include '../../../config/config.php';

$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;
$is_logged_in = isset($_GET['is_logged_in']) ? filter_var($_GET['is_logged_in'], FILTER_VALIDATE_BOOLEAN) : false;

$count = 0;
if ($is_logged_in && $customer_id) {
    // Count unique products in cart
    $cart_query_str = "SELECT COUNT(DISTINCT ci.product_id) as total FROM cart c JOIN cart_item ci ON c.cart_id = ci.cart_id WHERE c.user_id = ?";
    $cart_query = mysqli_prepare($conn, $cart_query_str);
    if ($cart_query) {
        mysqli_stmt_bind_param($cart_query, "s", $customer_id);
        mysqli_stmt_execute($cart_query);
        $result = mysqli_stmt_get_result($cart_query);
        if ($row = mysqli_fetch_assoc($result)) {
            $count = (int)($row['total'] ?? 0);
        }
        mysqli_stmt_close($cart_query);
    }
} else {
    if (isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
        // Count unique product IDs in guest cart
        $count = count($_SESSION['guest_cart']);
    }
}

echo json_encode(['count' => $count]);
