<?php
include '../../../config/config.php';
session_start();

function errorRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:../../../profile');
    exit();
}

function successRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:../../../signin');
    exit();
}

// Get and validate customer ID
$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
if (empty($customer_id)) {
    errorRedirectWithMessage("Please sign in to delete your account");
}

// Get and validate password
$password = $_POST['password'] ?? '';
if (empty($password)) {
    errorRedirectWithMessage("Password is required to delete your account");
}

// Verify password
$verify_query = mysqli_prepare(
    $conn,
    "SELECT password_hash FROM users WHERE user_id = ? AND is_admin != 1"
);
mysqli_stmt_bind_param($verify_query, "i", $customer_id);
mysqli_stmt_execute($verify_query);
$result = mysqli_stmt_get_result($verify_query);
$user = mysqli_fetch_assoc($result);

if (!$user || $user['password_hash'] !== $password) {
    errorRedirectWithMessage("Incorrect password");
}

// Delete user account
$delete_query = mysqli_prepare(
    $conn,
    "DELETE FROM users WHERE user_id = ? AND is_admin != 1"
);

mysqli_stmt_bind_param($delete_query, "i", $customer_id);

if (mysqli_stmt_execute($delete_query)) {
    // Clear session and cookies
    session_destroy();
    setcookie("customer_token", "", time() - 3600, "/");
    successRedirectWithMessage("Your account has been deleted successfully");
} else {
    errorRedirectWithMessage("Failed to delete account. Please try again.");
}

mysqli_stmt_close($delete_query); 