<?php
header('Content-Type: application/json');
include '../../../config/config.php';
session_start();

// Check if user is logged in
if (!isset($_COOKIE['customer_token'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Get user ID from token
$token = $_COOKIE['customer_token'];
$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$decoded = JWT::decode($token, new Key($key, 'HS256'));
$user_id = $decoded->customer->customer_id;

// Validate input
$required_fields = ['current_password', 'new_password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'success' => false,
            'message' => "Missing required field: $field"
        ]);
        exit;
    }
}

if ($_POST['new_password'] !== $_POST['confirm_password']) {
    echo json_encode([
        'success' => false,
        'message' => 'New passwords do not match'
    ]);
    exit;
}

if (strlen($_POST['new_password']) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'New password must be at least 8 characters long'
    ]);
    exit;
}

// Verify current password
$current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
$check_query = "SELECT password_hash FROM users WHERE user_id = ? AND password_hash = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "is", $user_id, $current_password);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Current password is incorrect'
    ]);
    exit;
}

// Update password
$new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
$query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $new_password, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update password'
    ]);
} 