<?php
session_start();
include '../../../config/config.php';

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

// Sanitize and validate input
$user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
if (empty($user_id)) {
    errorRedirectWithMessage("Please sign in to add an address");
}

$address_type = mysqli_real_escape_string($conn, $_POST['address_type']);
$address_line1 = mysqli_real_escape_string($conn, $_POST['address_line1']);
$address_line2 = mysqli_real_escape_string($conn, $_POST['address_line2'] ?? '');
$country = mysqli_real_escape_string($conn, $_POST['country']);
$district = mysqli_real_escape_string($conn, $_POST['district']);
$sector = mysqli_real_escape_string($conn, $_POST['sector'] ?? '');
$cell = mysqli_real_escape_string($conn, $_POST['cell'] ?? '');
$phone_number = mysqli_real_escape_string($conn, $_POST['phone_number'] ?? '');
$is_default = isset($_POST['is_default']) ? 1 : 0;
$is_guest = isset($_POST['is_guest']) ? (int)$_POST['is_guest'] : 0;

// Validate required fields
if (empty($address_type) || empty($address_line1) || empty($country) || empty($district)) {
    errorRedirectWithMessage("Please fill in all required fields");
}

// Generate unique address ID
$address_id = 'ADDR' . uniqid();

// If this is set as default, unset any existing default address
if ($is_default) {
    $update_default = mysqli_query(
        $conn,
        "UPDATE addresses SET is_default = 0 WHERE user_id = '$user_id' AND is_guest = 0"
    );
}

// Insert new address
$insert_query = mysqli_prepare(
    $conn,
    "INSERT INTO addresses (
        address_id, user_id, email, first_name, last_name, address_line1, address_line2, 
        country, district, sector, cell, phone_number, address_type, is_default, is_guest
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

// Get user's email and name
$user_query = mysqli_prepare($conn, "SELECT email, first_name, last_name FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($user_query, "i", $user_id);
mysqli_stmt_execute($user_query);
$user_result = mysqli_stmt_get_result($user_query);
$user_data = mysqli_fetch_assoc($user_result);

mysqli_stmt_bind_param(
    $insert_query,
    "sssssssssssssii",
    $address_id,
    $user_id,
    $user_data['email'],
    $user_data['first_name'],
    $user_data['last_name'],
    $address_line1,
    $address_line2,
    $country,
    $district,
    $sector,
    $cell,
    $phone_number,
    $address_type,
    $is_default,
    $is_guest
);

if (mysqli_stmt_execute($insert_query)) {
    successRedirectWithMessage("Address added successfully");
} else {
    errorRedirectWithMessage("Failed to add address. Please try again.");
}

mysqli_stmt_close($insert_query);
mysqli_stmt_close($user_query); 