<?php
// Commented out all business/order logic for verification
header('Content-Type: application/json');
session_start();

// Collect POST data
$post_data = $_POST;

// Collect guest cart from cookie (if present)
$guest_cart = isset($_COOKIE['guest_cart']) ? json_decode($_COOKIE['guest_cart'], true) : null;

// Collect session data (if any)
$session_data = $_SESSION;

// Output all collected data for verification
$data = [
    'post' => $post_data,
    'guest_cart' => $guest_cart,
    'session' => $session_data
];
echo json_encode($data, JSON_PRETTY_PRINT);
exit; 