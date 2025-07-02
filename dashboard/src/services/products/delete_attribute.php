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

$attribute_type_id = intval($_POST['attribute_type_id'] ?? 0);
if ($attribute_type_id === 0) {
    setErrorMessage('Invalid attribute type ID.');
}

// Check for attribute values
$value_check = mysqli_prepare($conn, 'SELECT attribute_value_id FROM attribute_value WHERE attribute_type_id = ?');
$value_check->bind_param('i', $attribute_type_id);
$value_check->execute();
$value_check->store_result();
if ($value_check->num_rows > 0) {
    setErrorMessage('Cannot delete: This attribute type has values.');
}

// Check for product references
$product_check = mysqli_prepare($conn, 'SELECT product_attribute_id FROM product_attributes WHERE attribute_type_id = ?');
$product_check->bind_param('i', $attribute_type_id);
$product_check->execute();
$product_check->store_result();
if ($product_check->num_rows > 0) {
    setErrorMessage('Cannot delete: This attribute type is assigned to one or more products.');
}

// Delete the attribute type
$delete = mysqli_prepare($conn, 'DELETE FROM attribute_type WHERE attribute_type_id = ?');
$delete->bind_param('i', $attribute_type_id);
if (!$delete->execute()) {
    setErrorMessage('Failed to delete attribute type. Please try again.');
}

setSuccessMessage('Attribute type deleted successfully.'); 