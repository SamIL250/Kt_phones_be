<?php
session_start();
include '../../../config/config.php';

header('Content-Type: application/json');

// Get address ID and user ID from request
$address_id = $_GET['id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$address_id || !$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit();
}

// Get address details
$address_query = mysqli_prepare(
    $conn,
    "SELECT * FROM addresses WHERE address_id = ? AND user_id = ? AND is_guest = 0"
);
mysqli_stmt_bind_param($address_query, "si", $address_id, $user_id);
mysqli_stmt_execute($address_query);
$result = mysqli_stmt_get_result($address_query);
$address = mysqli_fetch_assoc($result);

if ($address) {
    echo json_encode([
        'success' => true,
        'address_id' => $address['address_id'],
        'user_id' => $address['user_id'],
        'address_type' => $address['address_type'],
        'address_line1' => $address['address_line1'],
        'address_line2' => $address['address_line2'],
        'country' => $address['country'],
        'district' => $address['district'],
        'sector' => $address['sector'],
        'cell' => $address['cell'],
        'phone_number' => $address['phone_number'],
        'is_default' => $address['is_default']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Address not found'
    ]);
}

mysqli_stmt_close($address_query); 