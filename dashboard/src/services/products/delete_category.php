<?php
include '../../../config/config.php';
session_start();

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-categories');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-categories');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

$category_id = intval($_POST['category_id'] ?? 0);
if ($category_id === 0) {
    setErrorMessage('Invalid category ID.');
}

// Check for child categories
$child_check = mysqli_prepare($conn, 'SELECT category_id FROM categories WHERE parent_category_id = ?');
$child_check->bind_param('i', $category_id);
$child_check->execute();
$child_check->store_result();
if ($child_check->num_rows > 0) {
    setErrorMessage('Cannot delete: This category has subcategories.');
}

// Check for products referencing this category
$product_check = mysqli_prepare($conn, 'SELECT product_id FROM products WHERE category_id = ?');
$product_check->bind_param('i', $category_id);
$product_check->execute();
$product_check->store_result();
if ($product_check->num_rows > 0) {
    setErrorMessage('Cannot delete: This category is assigned to one or more products.');
}

// Delete the category
$delete = mysqli_prepare($conn, 'DELETE FROM categories WHERE category_id = ?');
$delete->bind_param('i', $category_id);
if (!$delete->execute()) {
    setErrorMessage('Failed to delete category. Please try again.');
}

setSuccessMessage('Category deleted successfully.'); 