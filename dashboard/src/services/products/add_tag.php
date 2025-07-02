<?php
require_once '../../../config/config.php';
session_start();

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../product-tags');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../product-tags');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

$tag_name = trim($_POST['tag_name'] ?? '');
if ($tag_name === '') {
    setErrorMessage('Tag name is required.');
}

// Check for duplicate
$stmt = $conn->prepare('SELECT tag_id FROM product_tags WHERE tag_name = ?');
$stmt->bind_param('s', $tag_name);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    setErrorMessage('Tag already exists.');
}
$stmt->close();

// Insert new tag
$stmt = $conn->prepare('INSERT INTO product_tags (tag_name) VALUES (?)');
$stmt->bind_param('s', $tag_name);
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to add tag. Please try again.');
}
$stmt->close();
setSuccessMessage('Tag added successfully.'); 