<?php
include '../../../config/config.php';
session_start();

function redirectWithMessage($notification)
{
    $_SESSION['notification'] = $notification;
    header("Location: ../../../products");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithMessage("Invalid request method");
}

// Get form data
$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);

// Debug: Log the product ID being processed
error_log("Updating product with ID: " . $product_id);
error_log("Product ID type: " . gettype($product_id));
error_log("Product ID length: " . strlen($product_id));
error_log("Raw POST data: " . print_r($_POST, true));

if (empty($product_id)) {
    redirectWithMessage("Error: No product ID provided");
}

// Verify product exists
$check_product = mysqli_prepare($conn, "SELECT product_id FROM products WHERE product_id = ?");
$check_product->bind_param('s', $product_id);
$check_product->execute();
$result = $check_product->get_result();

if ($result->num_rows === 0) {
    redirectWithMessage("Error: Product not found with ID: " . $product_id);
}

error_log("Found " . $result->num_rows . " products with ID: " . $product_id);

// Count total products before update
$count_before = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$total_before = mysqli_fetch_assoc($count_before)['total'];
error_log("Total products before update: " . $total_before);

$product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
$description = mysqli_real_escape_string($conn, $_POST['product_description']);
$product_sku = mysqli_real_escape_string($conn, $_POST['product_sku']);
$stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);
$base_price = mysqli_real_escape_string($conn, $_POST['base_price']);
$discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
$product_category = mysqli_real_escape_string($conn, $_POST['product_category']);
$product_tag = mysqli_real_escape_string($conn, $_POST['product_tag']);
$product_brand = mysqli_real_escape_string($conn, $_POST['product_brand']);
$is_featured = mysqli_real_escape_string($conn, $_POST['is_featured']);

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Debug: Log all form data
    error_log("Form data received: " . print_r($_POST, true));

    // Update main product details
    $update_product = mysqli_prepare(
        $conn,
        "UPDATE products SET 
        name = ?,
        description = ?,
        sku = ?,
        stock_quantity = ?,
        base_price = ?,
        discount_price = ?,
        category_id = ?,
        brand_id = ?,
        is_featured = ?,
        updated_at = NOW()
        WHERE product_id = ?"
    );

    $update_product->bind_param(
        'sssiddiiis',
        $product_name,
        $description,
        $product_sku,
        $stock_quantity,
        $base_price,
        $discount_price,
        $product_category,
        $product_brand,
        $is_featured,
        $product_id
    );

    error_log("Executing update query for product ID: " . $product_id);
    error_log("Update parameters: name=$product_name, sku=$product_sku, stock=$stock_quantity, base_price=$base_price, discount_price=$discount_price, category=$product_category, brand=$product_brand, featured=$is_featured");
    error_log("SQL Query: UPDATE products SET name=?, description=?, sku=?, stock_quantity=?, base_price=?, discount_price=?, category_id=?, brand_id=?, is_featured=?, updated_at=NOW() WHERE product_id=?");
    error_log("Parameter types: sssiddiiis (string,string,string,integer,double,double,integer,integer,integer,string)");

    // Test: Check what products would be affected by this WHERE clause
    $test_query = mysqli_prepare($conn, "SELECT product_id, name FROM products WHERE product_id = ?");
    $test_query->bind_param('s', $product_id);
    $test_query->execute();
    $test_result = $test_query->get_result();
    error_log("Products that would be affected by WHERE clause:");
    while ($row = $test_result->fetch_assoc()) {
        error_log("  - Product ID: " . $row['product_id'] . ", Name: " . $row['name']);
    }

    if (!$update_product->execute()) {
        throw new Exception("Error updating product: " . mysqli_error($conn));
    }

    // Check if any rows were affected
    if ($update_product->affected_rows === 0) {
        throw new Exception("No product was updated. Product ID: " . $product_id);
    }

    error_log("Successfully updated product. Rows affected: " . $update_product->affected_rows);

    // Count total products after update
    $count_after = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $total_after = mysqli_fetch_assoc($count_after)['total'];
    error_log("Total products after update: " . $total_after);

    // Check if the specific product was updated
    $check_updated = mysqli_prepare($conn, "SELECT name FROM products WHERE product_id = ?");
    $check_updated->bind_param('s', $product_id);
    $check_updated->execute();
    $updated_result = $check_updated->get_result();
    $updated_product = $updated_result->fetch_assoc();
    error_log("Updated product name: " . ($updated_product ? $updated_product['name'] : 'NOT FOUND'));

    // Update product tag
    mysqli_query($conn, "DELETE FROM product_tag_map WHERE product_id = '$product_id'");
    $insert_tag = mysqli_prepare(
        $conn,
        "INSERT INTO product_tag_map (product_id, tag_id) VALUES (?, ?)"
    );

    $insert_tag->bind_param('si', $product_id, $product_tag);
    if (!$insert_tag->execute()) {
        throw new Exception("Error updating product tag: " . mysqli_error($conn));
    }

    // Update product images
    mysqli_query($conn, "DELETE FROM product_images WHERE product_id = '$product_id'");
    $insert_image = mysqli_prepare(
        $conn,
        "INSERT INTO product_images (
            product_id,
            image_url,
            alt_text,
            display_order,
            is_primary
        ) VALUES (?, ?, ?, ?, ?)"
    );

    for ($i = 1; $i <= 5; $i++) {
        if (isset($_POST["product_image_$i"]) && !empty($_POST["product_image_$i"])) {
            $image_url = mysqli_real_escape_string($conn, $_POST["product_image_$i"]);
            $alt_text = "Product Image $i";
            $is_primary = ($i === 1) ? 1 : 0;

            $insert_image->bind_param(
                'sssis',
                $product_id,
                $image_url,
                $alt_text,
                $i,
                $is_primary
            );

            if (!$insert_image->execute()) {
                throw new Exception("Error updating product image $i: " . mysqli_error($conn));
            }
        }
    }

    // Update product attributes
    mysqli_query($conn, "DELETE FROM product_attributes WHERE product_id = '$product_id'");
    if (isset($_POST['selected_attributes']) && isset($_POST['attribute_values'])) {
        $insert_attribute = mysqli_prepare(
            $conn,
            "INSERT INTO product_attributes (
                product_id,
                attribute_type_id,
                attribute_value_id
            ) VALUES (?, ?, ?)"
        );

        foreach ($_POST['selected_attributes'] as $attribute_type_id) {
            if (
                isset($_POST['attribute_values'][$attribute_type_id]) &&
                !empty($_POST['attribute_values'][$attribute_type_id])
            ) {

                $attribute_value_id = $_POST['attribute_values'][$attribute_type_id];

                $insert_attribute->bind_param(
                    'sii',
                    $product_id,
                    $attribute_type_id,
                    $attribute_value_id
                );

                if (!$insert_attribute->execute()) {
                    throw new Exception("Error updating product attribute: " . mysqli_error($conn));
                }
            }
        }
    }

    // Commit transaction
    mysqli_commit($conn);
    redirectWithMessage("Product updated successfully");
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    redirectWithMessage("Error: " . $e->getMessage());
}
