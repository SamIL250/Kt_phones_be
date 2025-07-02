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
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate required fields
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    setErrorMessage('All password fields are required.');
}

// Verify current password
$stmt = $conn->prepare('SELECT password_hash FROM users WHERE user_id = ?');
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['password_hash'] !== $current_password) {
    setErrorMessage('Current password is incorrect.');
}

// Validate new password
if (strlen($new_password) < 6) {
    setErrorMessage('New password must be at least 6 characters long.');
}

if ($new_password === $current_password) {
    setErrorMessage('New password must be different from current password.');
}

if ($new_password !== $confirm_password) {
    setErrorMessage('New password and confirm password do not match.');
}

// Update password
$stmt = $conn->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE user_id = ?');
$stmt->bind_param('si', $new_password, $admin_id);

if (!$stmt->execute()) {
    $stmt->close();
    setErrorMessage('Failed to update password. Please try again.');
}
$stmt->close();

setSuccessMessage('Password changed successfully.');
?> 