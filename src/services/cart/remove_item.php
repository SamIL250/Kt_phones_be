<?php
include '../../../config/config.php';
session_start();

$product = $_GET['product'] ?? '';
$user = $_GET['user'] ?? '';

function redirectWithMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../cart');
    exit();
}

// Validate inputs
if (empty($product) || empty($user)) {
    redirectWithMessage("Missing product or user ID.");
}

// 1. Get the cart_id for the user
$get_cart = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = '$user'");
if (!$get_cart || mysqli_num_rows($get_cart) === 0) {
    redirectWithMessage("Cart not found for the user.");
}

$cart_id = mysqli_fetch_assoc($get_cart)['cart_id'];

// 2. Delete the cart item
$delete_item = mysqli_query($conn, "
    DELETE FROM cart_item 
    WHERE cart_id = '$cart_id' AND product_id = '$product'
");

if (!$delete_item) {
    redirectWithMessage("Failed to remove item from cart.");
}

// 3. Optionally, delete the cart if it's now empty
$check_remaining = mysqli_query($conn, "SELECT * FROM cart_item WHERE cart_id = '$cart_id'");
if (mysqli_num_rows($check_remaining) === 0) {
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id = '$cart_id'");
}

redirectWithMessage("Item removed from your cart.");
