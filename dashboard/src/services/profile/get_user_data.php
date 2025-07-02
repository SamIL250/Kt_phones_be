<?php
require_once '../../../config/config.php';
session_start();

header('Content-Type: application/json');

function errorResponse($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    errorResponse('User not authenticated.');
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT user_id, first_name, last_name, email, phone_number, date_of_birth, is_active, is_admin, created_at, updated_at FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    errorResponse('User not found.');
}

$user = $result->fetch_assoc();
$stmt->close();

// Success response
$response = [
    'success' => true,
    'user' => $user
];
echo json_encode($response);
?> 