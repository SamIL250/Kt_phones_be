<?php
include '../../../config/config.php';
session_start();

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-brands');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-brands');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

$brand_id = intval($_POST['brand_id'] ?? 0);
if ($brand_id === 0) {
    setErrorMessage('Invalid brand ID.');
}

// Check for products referencing this brand
$product_check = mysqli_prepare($conn, 'SELECT product_id FROM products WHERE brand_id = ?');
$product_check->bind_param('i', $brand_id);
$product_check->execute();
$product_check->store_result();
if ($product_check->num_rows > 0) {
    setErrorMessage('Cannot delete: This brand is assigned to one or more products.');
}

// Delete the brand
$delete = mysqli_prepare($conn, 'DELETE FROM brands WHERE brand_id = ?');
$delete->bind_param('i', $brand_id);
if (!$delete->execute()) {
    setErrorMessage('Failed to delete brand. Please try again.');
}

setSuccessMessage('Brand deleted successfully.'); 