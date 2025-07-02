<?php
require_once '../../../config/config.php';
require_once '../../../vendor/autoload.php';
session_start();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../auth/signin.php');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../profile');
    exit();
}

// Check if admin is logged in via JWT token
if (!isset($_COOKIE['admin_token'])) {
    setErrorMessage('Admin not authenticated.');
}

$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$token = $_COOKIE['admin_token'];

try {
    $decode = JWT::decode($token, new Key($key, 'HS256'));
    $admin_id = $decode->admin->admin_id;
} catch (ExpiredException $e) {
    setErrorMessage('Session expired. Please sign in again.');
} catch (Exception $e) {
    setErrorMessage('Invalid session. Please sign in again.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

// Get confirmation password
$password = $_POST['password'] ?? '';

if (empty($password)) {
    setErrorMessage('Password is required to delete account.');
}

// Verify password
$stmt = $conn->prepare('SELECT password_hash, is_admin FROM users WHERE user_id = ?');
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['password_hash'] !== $password) {
    setErrorMessage('Incorrect password.');
}

// Prevent admin account deletion
if ($user['is_admin']) {
    setErrorMessage('Admin accounts cannot be deleted.');
}

// Check if user has active orders
$stmt = $conn->prepare('SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status IN (?, ?, ?)');
$pending = 'pending';
$processing = 'processing';
$shipped = 'shipped';
$stmt->bind_param('isss', $admin_id, $pending, $processing, $shipped);
$stmt->execute();
$result = $stmt->get_result();
$active_orders = $result->fetch_assoc();
$stmt->close();

if ($active_orders['count'] > 0) {
    setErrorMessage('Cannot delete account with active orders. Please cancel or complete all orders first.');
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete user's wishlists and wishlist items
    $stmt = $conn->prepare('SELECT wishlist_id FROM wishlists WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($wishlist = $result->fetch_assoc()) {
        $delete_items = $conn->prepare('DELETE FROM wishlist_items WHERE wishlist_id = ?');
        $delete_items->bind_param('s', $wishlist['wishlist_id']);
        $delete_items->execute();
        $delete_items->close();
    }
    $stmt->close();
    
    $stmt = $conn->prepare('DELETE FROM wishlists WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Delete user's reviews
    $stmt = $conn->prepare('DELETE FROM product_reviews WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Delete user's cart and cart items
    $stmt = $conn->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($cart = $result->fetch_assoc()) {
        $delete_items = $conn->prepare('DELETE FROM cart_item WHERE cart_id = ?');
        $delete_items->bind_param('s', $cart['cart_id']);
        $delete_items->execute();
        $delete_items->close();
    }
    $stmt->close();
    
    $stmt = $conn->prepare('DELETE FROM cart WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Delete user's addresses
    $stmt = $conn->prepare('DELETE FROM addresses WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Delete user's orders (only if no active orders)
    $stmt = $conn->prepare('DELETE FROM order_items WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?)');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $conn->prepare('DELETE FROM orders WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Finally delete the user
    $stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // Clear the JWT token
    setcookie("admin_token", "", time() - 3600, "/", "", false, true);
    
    setSuccessMessage('Account deleted successfully.');
    
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    setErrorMessage('Failed to delete account: ' . $e->getMessage());
}
?> 