<?php
session_start();
include '../../../config/config.php'; // Using an absolute path is more reliable

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_GET['user_id'];
$clear_all = isset($_GET['clear_all']) && $_GET['clear_all'] === 'true';

// Get the user's wishlist ID
$wishlist_id_query = mysqli_prepare($conn, "SELECT wishlist_id FROM wishlists WHERE user_id = ?");
mysqli_stmt_bind_param($wishlist_id_query, "s", $user_id);
mysqli_stmt_execute($wishlist_id_query);
$wishlist_result = mysqli_stmt_get_result($wishlist_id_query);

if (mysqli_num_rows($wishlist_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'No wishlist found for this user.']);
    exit;
}
$wishlist_id = mysqli_fetch_assoc($wishlist_result)['wishlist_id'];
mysqli_stmt_close($wishlist_id_query);

if ($clear_all) {
    // --- Clear the entire wishlist ---
    $delete_stmt = mysqli_prepare($conn, "DELETE FROM wishlist_items WHERE wishlist_id = ?");
    mysqli_stmt_bind_param($delete_stmt, "i", $wishlist_id);

    if (mysqli_stmt_execute($delete_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Wishlist cleared successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear wishlist.']);
    }
    mysqli_stmt_close($delete_stmt);
} else {
    // --- Remove a single item ---
    if (!isset($_GET['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID not provided.']);
        exit;
    }
    $product_id = $_GET['product_id'];

    $delete_stmt = mysqli_prepare($conn, "DELETE FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($delete_stmt, "is", $wishlist_id, $product_id);

    if (mysqli_stmt_execute($delete_stmt)) {
        if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
            echo json_encode(['success' => true, 'message' => 'Item removed from wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found in wishlist.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item from wishlist.']);
    }
    mysqli_stmt_close($delete_stmt);
}

mysqli_close($conn);
