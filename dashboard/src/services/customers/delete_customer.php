<?php
require_once '../../../config/config.php';
session_start();

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../customers');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../customers');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

$user_id = intval($_POST['user_id'] ?? 0);
if ($user_id <= 0) {
    setErrorMessage('Invalid customer.');
}
// Check for references in orders
$stmt = $conn->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($ref_count);
$stmt->fetch();
$stmt->close();
if ($ref_count > 0) {
    setErrorMessage('Cannot delete customer: they have placed orders.');
}
// Delete user
$stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to delete customer. Please try again.');
}
$stmt->close();
setSuccessMessage('Customer deleted successfully.'); 