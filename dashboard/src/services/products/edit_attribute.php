<?php
session_start();
include '../../../config/config.php';

function redirectWithMessage($message, $is_error = false)
{
    $_SESSION['notification'] = $message;
    // Redirect back to the attributes page
    header("Location: ../../../product-attributes");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithMessage("Invalid request method.", true);
}

// Validate and sanitize inputs
$attribute_type_id = filter_input(INPUT_POST, 'attribute_type_id', FILTER_VALIDATE_INT);
$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
$description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));

if (!$attribute_type_id || empty($name)) {
    redirectWithMessage("Attribute ID and Name are required.", true);
}

// Prepare the update statement
$sql = "UPDATE attribute_type SET name = ?, description = ? WHERE attribute_type_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("MySQLi prepare failed: " . mysqli_error($conn));
    redirectWithMessage("An error occurred while preparing the update.", true);
}

mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $attribute_type_id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirectWithMessage("Attribute updated successfully.");
    } else {
        redirectWithMessage("No changes were made to the attribute.");
    }
} else {
    error_log("MySQLi execute failed: " . mysqli_stmt_error($stmt));
    redirectWithMessage("Failed to update attribute. Please check the logs.", true);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
