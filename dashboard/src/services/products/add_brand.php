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

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$logo_url = trim($_POST['logo_url'] ?? '');

if ($name === '') {
    setErrorMessage('Brand name is required.');
}

// Check for duplicate name
$check_name = mysqli_prepare($conn, 'SELECT brand_id FROM brands WHERE name = ?');
$check_name->bind_param('s', $name);
$check_name->execute();
$check_name->store_result();
if ($check_name->num_rows > 0) {
    setErrorMessage('Brand name already exists. Please use a unique name.');
}

// Insert new brand
$insert = mysqli_prepare($conn, 'INSERT INTO brands (name, description, logo_url) VALUES (?, ?, ?)');
$insert->bind_param('sss', $name, $description, $logo_url);
if (!$insert->execute()) {
    setErrorMessage('Failed to add brand. Please try again.');
}

setSuccessMessage('Brand added successfully.'); 