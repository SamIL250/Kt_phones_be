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
    errorRedirectWithMessage("Please sign in to update your profile");
}

// Sanitize and validate input
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
$phone_number = mysqli_real_escape_string($conn, $_POST['phone_number'] ?? '');
$date_of_birth = !empty($_POST['date_of_birth']) ? mysqli_real_escape_string($conn, $_POST['date_of_birth']) : null;

// Validate required fields
if (empty($first_name) || empty($last_name)) {
    errorRedirectWithMessage("First name and last name are required");
}

// Update user information
$update_query = mysqli_prepare(
    $conn,
    "UPDATE users SET 
        first_name = ?,
        last_name = ?,
        phone_number = ?,
        date_of_birth = ?
    WHERE user_id = ? AND is_admin != 1"
);

mysqli_stmt_bind_param(
    $update_query,
    "ssssi",
    $first_name,
    $last_name,
    $phone_number,
    $date_of_birth,
    $customer_id
);

if (mysqli_stmt_execute($update_query)) {
    successRedirectWithMessage("Profile updated successfully");
} else {
    errorRedirectWithMessage("Failed to update profile. Please try again.");
}

mysqli_stmt_close($update_query); 