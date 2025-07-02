<?php
require_once '../../../config/config.php';
require_once '../../../vendor/autoload.php';
session_start();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../profile');
    exit();
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('Location: ../../../profile');
    exit();
}

// Check if admin is logged in via JWT token
if (!isset($_COOKIE['admin_token'])) {
    setErrorMessage('Admin not authenticated.');
}

$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$token = $_COOKIE['admin_token'];

try {
    $decode = JWT::decode($token, new Key($key, 'HS256'));
    $admin_id = $decode->admin->admin_id;
} catch (ExpiredException $e) {
    setErrorMessage('Session expired. Please sign in again.');
} catch (Exception $e) {
    setErrorMessage('Invalid session. Please sign in again.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Invalid request method.');
}

// Get form data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$date_of_birth = $_POST['date_of_birth'] ?? null;

// Validate required fields
if ($first_name === '' || $last_name === '' || $email === '') {
    setErrorMessage('First name, last name, and email are required.');
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setErrorMessage('Invalid email format.');
}

// Check if email is already taken by another user
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ?');
$stmt->bind_param('si', $email, $admin_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    setErrorMessage('Email is already taken by another user.');
}
$stmt->close();

// Update profile information
$stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ?, date_of_birth = ?, updated_at = NOW() WHERE user_id = ?');
$stmt->bind_param('sssssi', $first_name, $last_name, $email, $phone_number, $date_of_birth, $admin_id);

if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to update profile. Please try again.');
}
$stmt->close();

setSuccessMessage('Profile updated successfully.');
?> 