<?php
include '../../../config/config.php';
session_start();



$attribute_value_id = intval($_POST['attribute_value_id'] ?? 0);
$attribute_id = intval($_POST['attribute_id'] ?? 0);

function setSuccessMessage($message)
{
    global $attribute_id;
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes-values?attribute_type_id='.$attribute_id);
    exit();
}

function setErrorMessage($message)
{
    global $attribute_id;
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes-values?attribute_type_id='.$attribute_id);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}


if ($attribute_value_id === 0) {
    setErrorMessage('Invalid attribute value ID.');
}

// Delete the value
$delete = mysqli_prepare($conn, 'DELETE FROM attribute_value WHERE attribute_value_id = ?');
$delete->bind_param('i', $attribute_value_id);
if (!$delete->execute()) {
    setErrorMessage('Failed to delete value. Please try again.');
}

setSuccessMessage('Value deleted successfully.'); 