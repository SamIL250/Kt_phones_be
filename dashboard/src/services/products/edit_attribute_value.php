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

$attribute_value_id = intval($_POST['attribute_value_id'] ?? 0);
$value = trim(urldecode($_POST['value'] ?? ''));

if ($attribute_value_id === 0 || $value === '') {
    setErrorMessage('Attribute value is required.');
}

// Get attribute_type_id for this value
$get_type = mysqli_prepare($conn, 'SELECT attribute_type_id FROM attribute_value WHERE attribute_value_id = ?');
$get_type->bind_param('i', $attribute_value_id);
$get_type->execute();
$get_type->bind_result($attribute_type_id);
if (!$get_type->fetch()) {
    setErrorMessage('Attribute value not found.');
}
$get_type->close();

// Check for duplicate value for this attribute type (exclude current value)
$check = mysqli_prepare($conn, 'SELECT attribute_value_id FROM attribute_value WHERE attribute_type_id = ? AND value = ? AND attribute_value_id != ?');
$check->bind_param('isi', $attribute_type_id, $value, $attribute_value_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    setErrorMessage('This value already exists for this attribute type.');
}

// Update value
$update = mysqli_prepare($conn, 'UPDATE attribute_value SET value = ? WHERE attribute_value_id = ?');
$update->bind_param('si', $value, $attribute_value_id);
if (!$update->execute()) {
    setErrorMessage('Failed to update value. Please try again.');
}

setSuccessMessage('Value updated successfully.'); 