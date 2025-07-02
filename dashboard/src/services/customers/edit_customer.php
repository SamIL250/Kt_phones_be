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
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($user_id <= 0 || $first_name === '' || $last_name === '' || $email === '') {
    setErrorMessage('All fields except password are required.');
}

// Check for duplicate email (excluding self)
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ?');
$stmt->bind_param('si', $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    setErrorMessage('Another user with this email already exists.');
}
$stmt->close();

if ($password !== '') {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, password_hash = ? WHERE user_id = ?');
    $stmt->bind_param('ssssi', $first_name, $last_name, $email, $password_hash, $user_id);
} else {
    $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?');
    $stmt->bind_param('sssi', $first_name, $last_name, $email, $user_id);
}
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to update customer. Please try again.');
}
$stmt->close();
setSuccessMessage('Customer updated successfully.'); 