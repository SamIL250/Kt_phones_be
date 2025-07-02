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

$tag_id = intval($_POST['tag_id'] ?? 0);
$tag_name = trim($_POST['tag_name'] ?? '');
if ($tag_id <= 0 || $tag_name === '') {
    setErrorMessage('Invalid tag or name.');
}
// Check for duplicate (excluding self)
$stmt = $conn->prepare('SELECT tag_id FROM product_tags WHERE tag_name = ? AND tag_id != ?');
$stmt->bind_param('si', $tag_name, $tag_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    setErrorMessage('Another tag with this name already exists.');
}
$stmt->close();
// Update tag
$stmt = $conn->prepare('UPDATE product_tags SET tag_name = ?, tag_updated_at = NOW() WHERE tag_id = ?');
$stmt->bind_param('si', $tag_name, $tag_id);
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to update tag. Please try again.');
}
$stmt->close();
setSuccessMessage('Tag updated successfully.'); 