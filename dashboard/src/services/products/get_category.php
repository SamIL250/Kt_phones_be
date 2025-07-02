<?php
include '../../../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$category_id = intval($_GET['category_id'] ?? 0);

if ($category_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit();
}

// Fetch category data
$query = mysqli_prepare($conn, 'SELECT category_id, name, description, slug, parent_category_id, is_active FROM categories WHERE category_id = ?');
$query->bind_param('i', $category_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Category not found']);
    exit();
}

$category = $result->fetch_assoc();
echo json_encode(['success' => true, 'category' => $category]);
