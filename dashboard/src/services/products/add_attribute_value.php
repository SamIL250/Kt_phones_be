<?php
include '../../../config/config.php';
session_start();



$attribute_type_id = intval($_POST['attribute_type_id'] ?? 0);
$values_raw = trim($_POST['values'] ?? '');

function setSuccessMessage($message)
{
    global $attribute_type_id;
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes-values?attribute_type_id='.$attribute_type_id);
    exit();
}

function setErrorMessage($message)
{
    global $attribute_type_id;
    $_SESSION['notification'] = $message;
    header('location: ../../../product-attributes-values?attribute_type_id='.$attribute_type_id);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

if ($attribute_type_id === 0 || $values_raw === '') {
    setErrorMessage('At least one value is required.');
}

// Split values by comma, semicolon, or new line
$values = preg_split('/[\r\n,;]+/', $values_raw);
$added = 0;
$skipped = [];
foreach ($values as $value) {
    $value = trim($value);
    if ($value === '') continue;
    // Check for duplicate
    $check = mysqli_prepare($conn, 'SELECT attribute_value_id FROM attribute_value WHERE attribute_type_id = ? AND value = ?');
    $check->bind_param('is', $attribute_type_id, $value);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $skipped[] = $value;
        continue;
    }
    // Insert new value
    $insert = mysqli_prepare($conn, 'INSERT INTO attribute_value (attribute_type_id, value) VALUES (?, ?)');
    $insert->bind_param('is', $attribute_type_id, $value);
    if ($insert->execute()) {
        $added++;
    }
}
$msg = "$added value(s) added.";
if ($skipped) {
    $msg .= " Skipped: " . implode(', ', $skipped);
}
setSuccessMessage($msg); 