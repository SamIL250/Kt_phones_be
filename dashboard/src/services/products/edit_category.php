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
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$parent_category_id = isset($_POST['parent_category_id']) && $_POST['parent_category_id'] !== '' ? intval($_POST['parent_category_id']) : null;
$is_active = isset($_POST['is_active']) ? 1 : 0;

if ($category_id === 0 || $name === '' || $slug === '') {
    setErrorMessage('Category name and slug are required.');
}

// Check for duplicate slug (exclude current category)
$check_slug = mysqli_prepare($conn, 'SELECT category_id FROM categories WHERE slug = ? AND category_id != ?');
$check_slug->bind_param('si', $slug, $category_id);
$check_slug->execute();
$check_slug->store_result();
if ($check_slug->num_rows > 0) {
    setErrorMessage('Slug already exists. Please use a unique slug.');
}

// Update category
if ($parent_category_id === null) {
    $update = mysqli_prepare($conn, 'UPDATE categories SET name = ?, description = ?, parent_category_id = NULL, slug = ?, is_active = ? WHERE category_id = ?');
    $update->bind_param('sssii', $name, $description, $slug, $is_active, $category_id);
} else {
    $update = mysqli_prepare($conn, 'UPDATE categories SET name = ?, description = ?, parent_category_id = ?, slug = ?, is_active = ? WHERE category_id = ?');
    $update->bind_param('ssisii', $name, $description, $parent_category_id, $slug, $is_active, $category_id);
}

if (!$update->execute()) {
    setErrorMessage('Failed to update category. Please try again.');
}

setSuccessMessage('Category updated successfully.');
