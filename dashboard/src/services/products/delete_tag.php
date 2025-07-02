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
if ($tag_id <= 0) {
    setErrorMessage('Invalid tag.');
}
// Check for references in product_tag_map
$stmt = $conn->prepare('SELECT COUNT(*) FROM product_tag_map WHERE tag_id = ?');
$stmt->bind_param('i', $tag_id);
$stmt->execute();
$stmt->bind_result($ref_count);
$stmt->fetch();
$stmt->close();
if ($ref_count > 0) {
    setErrorMessage('Cannot delete tag: it is assigned to one or more products.');
}
// Delete tag
$stmt = $conn->prepare('DELETE FROM product_tags WHERE tag_id = ?');
$stmt->bind_param('i', $tag_id);
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to delete tag. Please try again.');
}
$stmt->close();
setSuccessMessage('Tag deleted successfully.'); 