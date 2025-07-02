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

// Get and validate input
$address_id = $_GET['address_id'];
$user_id = $_GET['user_id'];

if (empty($address_id) || empty($user_id)) {
    errorRedirectWithMessage("Invalid address or user ID");
}

// Check if address exists and belongs to user
$check_query = mysqli_prepare(
    $conn,
    "SELECT address_id FROM addresses WHERE address_id = ? AND user_id = ? AND is_guest = 0"
);
mysqli_stmt_bind_param($check_query, "si", $address_id, $user_id);
mysqli_stmt_execute($check_query);
$result = mysqli_stmt_get_result($check_query);

if (mysqli_num_rows($result) === 0) {
    errorRedirectWithMessage("Address not found or unauthorized");
}

// Delete address
$delete_query = mysqli_prepare(
    $conn,
    "DELETE FROM addresses WHERE address_id = ? AND user_id = ? AND is_guest = 0"
);
mysqli_stmt_bind_param($delete_query, "si", $address_id, $user_id);

if (mysqli_stmt_execute($delete_query)) {
    successRedirectWithMessage("Address deleted successfully");
} else {
    errorRedirectWithMessage("Failed to delete address. Please try again.");
}

mysqli_stmt_close($check_query);
mysqli_stmt_close($delete_query); 