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
    header('location:../../../profile');
    exit();
}

// Get and validate customer ID
$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
if (empty($customer_id)) {
    errorRedirectWithMessage("Please sign in to change your password");
}

// Get and validate passwords
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    errorRedirectWithMessage("All password fields are required");
}

if ($new_password !== $confirm_password) {
    errorRedirectWithMessage("New passwords do not match");
}

// Verify current password
$verify_query = mysqli_prepare(
    $conn,
    "SELECT password_hash FROM users WHERE user_id = ? AND is_admin != 1"
);
mysqli_stmt_bind_param($verify_query, "i", $customer_id);
mysqli_stmt_execute($verify_query);
$result = mysqli_stmt_get_result($verify_query);
$user = mysqli_fetch_assoc($result);

if (!$user || $user['password_hash'] !== $current_password) {
    errorRedirectWithMessage("Current password is incorrect");
}

// Update password
$update_query = mysqli_prepare(
    $conn,
    "UPDATE users SET password_hash = ? WHERE user_id = ? AND is_admin != 1"
);

mysqli_stmt_bind_param(
    $update_query,
    "si",
    $new_password,
    $customer_id
);

if (mysqli_stmt_execute($update_query)) {
    successRedirectWithMessage("Password changed successfully");
} else {
    errorRedirectWithMessage("Failed to change password. Please try again.");
}

mysqli_stmt_close($update_query); 