<?php
include '../../../config/config.php';
session_start();

// Check if request is POST and contains JSON data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Extract and sanitize data
$product_id = mysqli_real_escape_string($conn, $input['product_id'] ?? '');
$image_url = mysqli_real_escape_string($conn, $input['image_url'] ?? '');

// Validate required fields
if (empty($product_id) || empty($image_url)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID and image URL are required']);
    exit();
}

// Verify product exists
$product_check = mysqli_prepare($conn, "SELECT product_id FROM products WHERE product_id = ?");
$product_check->bind_param('s', $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

// Check if image exists for this product
$image_check = mysqli_prepare($conn, "SELECT image_id FROM product_images WHERE product_id = ? AND image_url = ?");
$image_check->bind_param('ss', $product_id, $image_url);
$image_check->execute();
$image_result = $image_check->get_result();

if ($image_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Image not found for this product']);
    exit();
}

// Get image details to check if it's primary
$image_details = mysqli_prepare($conn, "SELECT image_id, is_primary FROM product_images WHERE product_id = ? AND image_url = ?");
$image_details->bind_param('ss', $product_id, $image_url);
$image_details->execute();
$image_data = $image_details->get_result()->fetch_assoc();

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete the image
    $delete_image = mysqli_prepare($conn, "DELETE FROM product_images WHERE product_id = ? AND image_url = ?");
    $delete_image->bind_param('ss', $product_id, $image_url);
    
    if (!$delete_image->execute()) {
        throw new Exception('Failed to delete image');
    }
    
    // If deleted image was primary, set the next image as primary
    if ($image_data['is_primary'] == 1) {
        $update_primary = mysqli_prepare($conn, "
            UPDATE product_images 
            SET is_primary = 1 
            WHERE product_id = ? 
            ORDER BY display_order 
            LIMIT 1
        ");
        $update_primary->bind_param('s', $product_id);
        
        if (!$update_primary->execute()) {
            throw new Exception('Failed to update primary image');
        }
    }
    
    // Update product's updated_at timestamp
    $update_product = mysqli_prepare($conn, "UPDATE products SET updated_at = CURRENT_TIMESTAMP WHERE product_id = ?");
    $update_product->bind_param('s', $product_id);
    
    if (!$update_product->execute()) {
        throw new Exception('Failed to update product timestamp');
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Image deleted successfully',
        'was_primary' => $image_data['is_primary'] == 1
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}

// Close database connection
mysqli_close($conn);
?> 