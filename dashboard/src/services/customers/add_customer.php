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

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
    setErrorMessage('All fields are required.');
}

// Check for duplicate email
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    setErrorMessage('A user with this email already exists.');
}
$stmt->close();

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare('INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $first_name, $last_name, $email, $password_hash);
if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to add customer. Please try again.');
}
$stmt->close();
setSuccessMessage('Customer added successfully.'); 