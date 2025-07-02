<?php
session_start();
include '../../../config/config.php';

function redirectWithMessage($notification)
{
    $_SESSION['notification'] = $notification;
    header("Location: ../../../products");
    exit();
}

if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    redirectWithMessage("Error: Product ID is missing.");
}

$product_id = $_GET['product_id'];

// Log the ID to be deactivated for debugging
error_log("Attempting to deactivate product with ID: " . $product_id);

$sql = "UPDATE products SET is_active = 0 WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    // Log error if prepare fails
    error_log("MySQLi prepare failed: " . mysqli_error($conn));
    redirectWithMessage("An error occurred. Please try again.");
}

// Corrected bind_param type to 's' for string
mysqli_stmt_bind_param($stmt, "s", $product_id);

$result = mysqli_stmt_execute($stmt);

if ($result) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirectWithMessage("Product removed successfully.");
    } else {
        redirectWithMessage("Product not found or no changes were made.");
    }
} else {
    // Log error if execute fails
    error_log("MySQLi execute failed: " . mysqli_stmt_error($stmt));
    redirectWithMessage("Failed to remove product. Please check the logs.");
}
