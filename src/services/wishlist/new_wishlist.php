<?php
session_start();
include '../../../config/config.php';

function redirectWithMessage($message) {
    $_SESSION['notification'] = $message;
    header('Location: ../../../store');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    if (!$user_id) {
        redirectWithMessage('Please sign in to add products to your wishlist.');
    }
    if (!$product_id) {
        redirectWithMessage('Invalid product.');
    }
    $wishlist_query = mysqli_query($conn, "SELECT wishlist_id FROM wishlists WHERE user_id = '$user_id' LIMIT 1");
    if ($wishlist_query && $row = mysqli_fetch_assoc($wishlist_query)) {
        $wishlist_id = $row['wishlist_id'];
    } else {
        $wishlist_id = uniqid('WL');
        mysqli_query($conn, "INSERT INTO wishlists (wishlist_id, user_id) VALUES ('$wishlist_id', '$user_id')");
    }
    $exists = mysqli_query($conn, "SELECT 1 FROM wishlist_items WHERE wishlist_id = '$wishlist_id' AND product_id = '$product_id'");
    if ($exists && mysqli_num_rows($exists) > 0) {
        // Product exists, remove it
        if (mysqli_query($conn, "DELETE FROM wishlist_items WHERE wishlist_id = '$wishlist_id' AND product_id = '$product_id'")) {
            redirectWithMessage('Product removed from your wishlist.');
        } else {
            redirectWithMessage('Failed to remove product from wishlist.');
        }
    } else {
        // Product not in wishlist, add it
        if (mysqli_query($conn, "INSERT INTO wishlist_items (wishlist_id, product_id) VALUES ('$wishlist_id', '$product_id')")) {
            redirectWithMessage('Product added to your wishlist.');
        } else {
            redirectWithMessage('Failed to add product to wishlist.');
        }
    }
}
redirectWithMessage('Invalid request.');
