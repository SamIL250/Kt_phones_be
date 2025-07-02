<?php
// Commented out all business/order logic for verification
header('Content-Type: application/json');
session_start();

// Collect POST data
$post_data = $_POST;

// Collect cart_id from POST
$cart_id = $_POST['cart_id'] ?? null;

// Collect customer_id from POST or cookie
$customer_id = $_POST['customer_id'] ?? null;
if (!$customer_id && isset($_COOKIE['customer_token'])) {
    $token = $_COOKIE['customer_token'];
    $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
    $customer_id = $payload['user_id'] ?? null;
}

// Collect session data (if any)
$session_data = $_SESSION;

// Output all collected data for verification
$data = [
    'post' => $post_data,
    'cart_id' => $cart_id,
    'customer_id' => $customer_id,
    'session' => $session_data
];
echo json_encode($data, JSON_PRETTY_PRINT);
exit; 