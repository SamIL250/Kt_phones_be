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

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$parent_category_id = isset($_POST['parent_category_id']) && $_POST['parent_category_id'] !== '' ? intval($_POST['parent_category_id']) : null;
$is_active = isset($_POST['is_active']) ? 1 : 0;

if ($name === '' || $slug === '') {
    setErrorMessage('Category name and slug are required.');
}

// Check for duplicate slug
$check_slug = mysqli_prepare($conn, 'SELECT category_id FROM categories WHERE slug = ?');
$check_slug->bind_param('s', $slug);
$check_slug->execute();
$check_slug->store_result();
if ($check_slug->num_rows > 0) {
    setErrorMessage('Slug already exists. Please use a unique slug.');
}

// Insert new category
$insert = mysqli_prepare($conn, 'INSERT INTO categories (name, description, parent_category_id, slug, is_active) VALUES (?, ?, ?, ?, ?)');
$insert->bind_param('ssisi', $name, $description, $parent_category_id, $slug, $is_active);
if (!$insert->execute()) {
    setErrorMessage('Failed to add category. Please try again.');
}

setSuccessMessage('Category added successfully.'); 