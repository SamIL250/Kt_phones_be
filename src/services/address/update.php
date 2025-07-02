<?php
header('Content-Type: application/json');
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

// Sanitize and validate input
$address_id = mysqli_real_escape_string($conn, $_POST['address_id']);
$user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

if (empty($address_id) || empty($user_id)) {
    errorRedirectWithMessage("Invalid address or user ID");
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

// Validate required fields
if (empty($address_type) || empty($address_line1) || empty($country) || empty($district)) {
    errorRedirectWithMessage("Please fill in all required fields");
}

// If this is set as default, unset any existing default address
if ($is_default) {
    $update_default = mysqli_query(
        $conn,
        "UPDATE addresses SET is_default = 0 WHERE user_id = '$user_id' AND is_guest = 0 AND address_id != '$address_id'"
    );
}

// Update address
$update_query = mysqli_prepare(
    $conn,
    "UPDATE addresses SET 
        address_type = ?,
        address_line1 = ?,
        address_line2 = ?,
        country = ?,
        district = ?,
        sector = ?,
        cell = ?,
        phone_number = ?,
        is_default = ?
    WHERE address_id = ? AND user_id = ? AND is_guest = 0"
);

mysqli_stmt_bind_param(
    $update_query,
    "ssssssssiss",
    $address_type,
    $address_line1,
    $address_line2,
    $country,
    $district,
    $sector,
    $cell,
    $phone_number,
    $is_default,
    $address_id,
    $user_id
);

if (mysqli_stmt_execute($update_query)) {
    successRedirectWithMessage("Address updated successfully");
} else {
    errorRedirectWithMessage("Failed to update address. Please try again.");
}

mysqli_stmt_close($update_query); 