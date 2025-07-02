<?php
include '../../../config/config.php';
session_start();

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === '') {
    setErrorMessage('Attribute name is required.');
}

// Check for duplicate name
$check = mysqli_prepare($conn, 'SELECT attribute_type_id FROM attribute_type WHERE name = ?');
$check->bind_param('s', $name);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    setErrorMessage('Attribute name already exists. Please use a unique name.');
}

// Insert new attribute type
$insert = mysqli_prepare($conn, 'INSERT INTO attribute_type (name, description) VALUES (?, ?)');
$insert->bind_param('ss', $name, $description);
if (!$insert->execute()) {
    setErrorMessage('Failed to add attribute. Please try again.');
}

setSuccessMessage('Attribute added successfully.'); 