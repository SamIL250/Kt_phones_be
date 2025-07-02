<?php
session_start();
include '../../../config/config.php';
function redirectWithMessage($notification) {
    $_SESSION['notification'] = $notification;
    header("Location: ../../../products");
    exit();
}

$product_id = $_GET['product_id'];

$sql = "UPDATE products SET is_active = 0 WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);

$result = mysqli_stmt_execute($stmt);

if ($result) {
    redirectWithMessage("Product removed successfully");
} else {
    redirectWithMessage("Failed to remove product");
}


