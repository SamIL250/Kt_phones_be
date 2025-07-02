<?php
include '../../../config/config.php';
session_start();

function setSuccessMessage($message, $product_id) {
    $_SESSION['notification'] = $message;
    header('Location: ../../../product?product=' . urlencode($product_id));
    exit();
}

function setErrorMessage($message, $product_id) {
    $_SESSION['notification'] = $message;
    header('Location: ../../../product?product=' . urlencode($product_id));
    exit();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setErrorMessage('Method not allowed', '');
}

// Extract and sanitize data
$product_id = mysqli_real_escape_string($conn, $_POST['product_id'] ?? '');
$image_url = mysqli_real_escape_string($conn, $_POST['image_url'] ?? '');
$alt_text = mysqli_real_escape_string($conn, $_POST['alt_text'] ?? '');
$display_order = intval($_POST['display_order'] ?? 0);
$is_primary = isset($_POST['is_primary']) ? 1 : 0;

// Validate required fields
if (empty($product_id) || empty($image_url)) {
    setErrorMessage('Product ID and image URL are required', $product_id);
}

// Validate image URL format
if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
    setErrorMessage('Invalid image URL format', $product_id);
}

// Verify product exists
$product_check = mysqli_prepare($conn, "SELECT product_id FROM products WHERE product_id = ?");
$product_check->bind_param('s', $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    setErrorMessage('Product not found', $product_id);
}

// Check if image URL already exists for this product
$duplicate_check = mysqli_prepare($conn, "SELECT image_id FROM product_images WHERE product_id = ? AND image_url = ?");
$duplicate_check->bind_param('ss', $product_id, $image_url);
$duplicate_check->execute();
$duplicate_result = $duplicate_check->get_result();

if ($duplicate_result->num_rows > 0) {
    setErrorMessage('Image URL already exists for this product', $product_id);
}

// Get current display order if not provided
if ($display_order == 0) {
    $max_order_query = mysqli_prepare($conn, "SELECT MAX(display_order) as max_order FROM product_images WHERE product_id = ?");
    $max_order_query->bind_param('s', $product_id);
    $max_order_query->execute();
    $max_order_result = $max_order_query->get_result();
    $max_order_data = $max_order_result->fetch_assoc();
    $display_order = ($max_order_data['max_order'] ?? 0) + 1;
}

// Set default alt text if not provided
if (empty($alt_text)) {
    $alt_text = "Product Image " . $display_order;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // If this image is set as primary, unset all other primary images for this product
    if ($is_primary == 1) {
        $unset_primary = mysqli_prepare($conn, "UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
        $unset_primary->bind_param('s', $product_id);
        
        if (!$unset_primary->execute()) {
            throw new Exception('Failed to unset existing primary images');
        }
    }
    
    // Insert the new image
    $insert_image = mysqli_prepare($conn, "
        INSERT INTO product_images (
            product_id,
            image_url,
            alt_text,
            display_order,
            is_primary
        ) VALUES (?, ?, ?, ?, ?)
    ");
    
    $insert_image->bind_param('sssis', $product_id, $image_url, $alt_text, $display_order, $is_primary);
    
    if (!$insert_image->execute()) {
        throw new Exception('Failed to insert image');
    }
    
    // Update product's updated_at timestamp
    $update_product = mysqli_prepare($conn, "UPDATE products SET updated_at = CURRENT_TIMESTAMP WHERE product_id = ?");
    $update_product->bind_param('s', $product_id);
    
    if (!$update_product->execute()) {
        throw new Exception('Failed to update product timestamp');
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Get the inserted image ID
    $image_id = $insert_image->insert_id;
    
    setSuccessMessage('Image added successfully', $product_id);
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    setErrorMessage('Internal server error: ' . $e->getMessage(), $product_id);
}

// Close database connection
mysqli_close($conn);
?> 