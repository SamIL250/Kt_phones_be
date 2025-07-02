<?php
header('Content-Type: application/json');
include '../../../config/config.php';
session_start();

// Check if user is logged in
if (!isset($_COOKIE['customer_token'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Get user ID from token
$token = $_COOKIE['customer_token'];
$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$decoded = JWT::decode($token, new Key($key, 'HS256'));
$user_id = $decoded->customer->customer_id;

// Validate input
if (empty($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Password is required'
    ]);
    exit;
}

// Verify password
$password = mysqli_real_escape_string($conn, $_POST['password']);
$check_query = "SELECT password_hash FROM users WHERE user_id = ? AND password_hash = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "is", $user_id, $password);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Password is incorrect'
    ]);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete user's addresses
    $delete_addresses = "DELETE FROM user_addresses WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_addresses);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Delete user's wishlists and items
    $delete_wishlist_items = "DELETE wi FROM wishlist_items wi 
                             INNER JOIN wishlists w ON wi.wishlist_id = w.wishlist_id 
                             WHERE w.user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_wishlist_items);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $delete_wishlists = "DELETE FROM wishlists WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_wishlists);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Delete user's cart and items
    $delete_cart_items = "DELETE ci FROM cart_item ci 
                         INNER JOIN cart c ON ci.cart_id = c.cart_id 
                         WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_cart_items);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $delete_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_cart);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Finally, delete the user
    $delete_user = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_user);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Commit transaction
    mysqli_commit($conn);

    // Clear session and cookies
    session_destroy();
    setcookie('customer_token', '', time() - 3600, '/');

    echo json_encode([
        'success' => true,
        'message' => 'Account deleted successfully'
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete account'
    ]);
} 