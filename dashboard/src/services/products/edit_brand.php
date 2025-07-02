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
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$logo_url = trim($_POST['logo_url'] ?? '');

if ($brand_id === 0 || $name === '') {
    setErrorMessage('Brand name is required.');
}

// Check for duplicate name (exclude current brand)
$check_name = mysqli_prepare($conn, 'SELECT brand_id FROM brands WHERE name = ? AND brand_id != ?');
$check_name->bind_param('si', $name, $brand_id);
$check_name->execute();
$check_name->store_result();
if ($check_name->num_rows > 0) {
    setErrorMessage('Brand name already exists. Please use a unique name.');
}

// Update brand
$update = mysqli_prepare($conn, 'UPDATE brands SET name = ?, description = ?, logo_url = ? WHERE brand_id = ?');
$update->bind_param('sssi', $name, $description, $logo_url, $brand_id);
if (!$update->execute()) {
    setErrorMessage('Failed to update brand. Please try again.');
}

setSuccessMessage('Brand updated successfully.'); 