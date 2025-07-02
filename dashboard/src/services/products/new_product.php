<?php
include '../../../config/config.php';
session_start();

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
$is_active = 'true';

$is_published = "";
if(isset($_POST['publish'])) {
    $is_published = 'true';
} else {
    $is_published = 'false';
}

//product_images
$image_1 = $_POST['product_image_1'] ?? '';
$image_2 = $_POST['product_image_2'] ?? '';
$image_3 = $_POST['product_image_3'] ?? '';
$image_4 = $_POST['product_image_4'] ?? '';
$image_5 = $_POST['product_image_5'] ?? '';

$unfiltered_image_urls = [
    $image_1,
    $image_2,
    $image_3,
    $image_4,
    $image_5,
];

//filter images and remove empty fields
$image_urls = [];
foreach ($unfiltered_image_urls as $url) {
    $clean_url = trim($url);
    if (!empty($clean_url)) {
        $image_urls[] = mysqli_real_escape_string($conn, $clean_url);
    }
}

function generateUuid($length = 20): string
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function setSuccessMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../products-add');
}

function setErrorMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location: ../../../products-add');
    exit();
}

// Check if we need to create product variants (multi-select attributes)
$has_variants = false;
$multi_select_attributes = [];
$single_select_attributes = [];

if (isset($_POST['selected_attributes']) && is_array($_POST['selected_attributes'])) {
    foreach ($_POST['selected_attributes'] as $attribute_id) {
        $attribute_id = intval($attribute_id);
        
        // Check if this is a multi-select attribute (Storage or Color)
        $attribute_query = mysqli_query($conn, "SELECT name FROM attribute_type WHERE attribute_type_id = $attribute_id");
        $attribute_data = mysqli_fetch_assoc($attribute_query);
        $attribute_name = strtolower($attribute_data['name']);
        
        if (in_array($attribute_name, ['storage', 'color'])) {
            // Multi-select attribute
            if (isset($_POST['attribute_values'][$attribute_id]) && is_array($_POST['attribute_values'][$attribute_id])) {
                $values = array_map('intval', $_POST['attribute_values'][$attribute_id]);
                if (!empty($values)) {
                    $multi_select_attributes[$attribute_id] = $values;
                    $has_variants = true;
                }
            }
        } else {
            // Single-select attribute
            if (isset($_POST['attribute_values'][$attribute_id]) && !is_array($_POST['attribute_values'][$attribute_id])) {
                $value_id = intval($_POST['attribute_values'][$attribute_id]);
                if ($value_id > 0) {
                    $single_select_attributes[$attribute_id] = $value_id;
                }
            }
        }
    }
}

//create product
$product_id = generateUuid();

$new_product_query = mysqli_prepare(
    $conn,
    "INSERT INTO `products`
    (
    `product_id`,
     `name`, 
     `description`, 
     `sku`, 
     `base_price`, 
     `discount_price`, 
     `category_id`, 
     `brand_id`, 
     `stock_quantity`, 
     `is_featured`,
     `published`
    ) 
    VALUES (?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?)"
);

$new_product_query->bind_param(
    'ssssiiiiiis',
    $product_id,
    $product_name,
    $description,
    $product_sku,
    $base_price,
    $discount_price,
    $product_category,
    $product_brand,
    $stock_quantity,
    $is_featured,
    $is_published
);

if (!$new_product_query->execute()) {
    setErrorMessage('Internal server error: Failed to add new product');
}

// Handle product attributes and variants
if ($has_variants) {
    // Create product variants
    $variant_combinations = generateVariantCombinations($multi_select_attributes);
    
    // Create product_variants table if it doesn't exist
    createProductVariantsTable($conn);
    
    // Insert variants
    foreach ($variant_combinations as $index => $combination) {
        // Handle variant stock - use base stock if empty
        $variant_stock_input = isset($_POST['variant_stock'][$index]) ? trim($_POST['variant_stock'][$index]) : '';
        $variant_stock = ($variant_stock_input !== '') ? intval($variant_stock_input) : intval($stock_quantity);
        
        // Handle variant price - use base price if empty
        $variant_price_input = isset($_POST['variant_price'][$index]) ? trim($_POST['variant_price'][$index]) : '';
        $variant_price = ($variant_price_input !== '') ? floatval($variant_price_input) : floatval($base_price);
        
        // Get size and color IDs from combination
        $size_id = null;
        $color_id = null;
        
        foreach ($combination as $attr) {
            $attr_query = mysqli_query($conn, "SELECT name FROM attribute_type WHERE attribute_type_id = {$attr['attributeId']}");
            $attr_data = mysqli_fetch_assoc($attr_query);
            $attr_name = strtolower($attr_data['name']);
            
            if ($attr_name === 'storage') {
                $size_id = $attr['value']['id'];
            } elseif ($attr_name === 'color') {
                $color_id = $attr['value']['id'];
            }
        }
        
        // Insert variant
        $variant_sku = $product_sku . '-' . ($index + 1);
        $insert_variant_query = mysqli_prepare(
            $conn,
            "INSERT INTO product_variants (
                product_id, size_id, color_id, sku, price, stock_quantity, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        
        $insert_variant_query->bind_param(
            'siissi',
            $product_id,
            $size_id,
            $color_id,
            $variant_sku,
            $variant_price,
            $variant_stock
        );
        
        if (!$insert_variant_query->execute()) {
            setErrorMessage('Internal server error: Failed to create product variants');
        }
    }
    
    // After creating all variants, update the main product's stock
    update_product_stock_from_variants($conn, $product_id);

    // Insert single-select attributes for the main product
    insertSingleSelectAttributes($conn, $product_id, $single_select_attributes);
    
} else {
    // No variants - insert attributes normally
    insertProductAttributes($conn, $product_id, $_POST['selected_attributes'], $_POST['attribute_values']);
}

//create product tag
$insert_tag_query = mysqli_prepare(
    $conn,
    "INSERT INTO
            product_tag_map (
                product_id,
                tag_id
            )
            VALUES (?, ?)"
);

$insert_tag_query->bind_param(
    'si',
    $product_id,
    $product_tag
);

if (!$insert_tag_query->execute()) {
    setErrorMessage('Internal server error: Failed to add tags');
}

//insert product images
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

$display_order = 1;
foreach ($image_urls as $index => $url) {
    if (!empty($url)) {
        $image_url = htmlspecialchars(trim($url));
        $alt_text = "Product Image " . ($index + 1);
        $is_primary = ($display_order === 1) ? 1 : 0;

        $insert_image->bind_param(
            'sssis',
            $product_id,
            $image_url,
            $alt_text,
            $display_order,
            $is_primary
        );

        if (!$insert_image->execute()) {
            setErrorMessage('Internal server error: Failed to add images');
        }

        $display_order++;
    }
}

setSuccessMessage('New product created successfully.');

// Helper functions
function createProductVariantsTable($conn) {
    $create_table_query = "
        CREATE TABLE IF NOT EXISTS product_variants (
            variant_id INT AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(50) NOT NULL,
            size_id INT,
            color_id INT,
            sku VARCHAR(50),
            price DECIMAL(10,2),
            stock_quantity INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
            FOREIGN KEY (size_id) REFERENCES attribute_value(attribute_value_id),
            FOREIGN KEY (color_id) REFERENCES attribute_value(attribute_value_id)
        )
    ";
    
    if (!mysqli_query($conn, $create_table_query)) {
        setErrorMessage('Internal server error: Failed to create variants table');
    }
}

function generateVariantCombinations($multi_select_attributes) {
    if (empty($multi_select_attributes)) return [];
    
    $combinations = [];
    $attribute_ids = array_keys($multi_select_attributes);
    $attribute_values = array_values($multi_select_attributes);
    
    generateCombinationsRecursive($attribute_ids, $attribute_values, [], 0, $combinations);
    
    return $combinations;
}

function generateCombinationsRecursive($attribute_ids, $attribute_values, $current, $index, &$combinations) {
    if ($index === count($attribute_ids)) {
        $combinations[] = $current;
        return;
    }
    
    $attribute_id = $attribute_ids[$index];
    $values = $attribute_values[$index];
    
    foreach ($values as $value_id) {
        // Get the value text for the combination
        global $conn;
        $value_query = mysqli_query($conn, "SELECT value FROM attribute_value WHERE attribute_value_id = $value_id");
        $value_data = mysqli_fetch_assoc($value_query);
        
        $current[] = [
            'attributeId' => $attribute_id,
            'value' => [
                'id' => $value_id,
                'text' => $value_data['value']
            ]
        ];
        
        generateCombinationsRecursive($attribute_ids, $attribute_values, $current, $index + 1, $combinations);
        array_pop($current);
    }
}

function insertSingleSelectAttributes($conn, $product_id, $single_select_attributes) {
    $insert_attribute_query = mysqli_prepare(
        $conn,
        "INSERT INTO product_attributes (
            product_id, attribute_type_id, attribute_value_id
        ) VALUES (?, ?, ?)"
    );
    
    foreach ($single_select_attributes as $attribute_id => $value_id) {
        $insert_attribute_query->bind_param('sii', $product_id, $attribute_id, $value_id);
        
        if (!$insert_attribute_query->execute()) {
            setErrorMessage('Internal server error: Failed to add single-select attributes');
        }
    }
}

function insertProductAttributes($conn, $product_id, $selected_attributes, $attribute_values) {
    $insert_attribute_query = mysqli_prepare(
        $conn,
        "INSERT INTO product_attributes (
            product_id, attribute_type_id, attribute_value_id
        ) VALUES (?, ?, ?)"
    );
    
    foreach ($selected_attributes as $attribute_id) {
        $attribute_id = intval($attribute_id);
        $value_id = isset($attribute_values[$attribute_id])
            ? intval($attribute_values[$attribute_id])
            : null;

        if ($value_id) {
            $insert_attribute_query->bind_param('sii', $product_id, $attribute_id, $value_id);

            if (!$insert_attribute_query->execute()) {
                setErrorMessage('Internal server error: Failed to add attributes');
            }
        }
    }
}

function update_product_stock_from_variants($conn, $product_id) {
    if (!$product_id) return;
    
    // Calculate the total stock from all variants of the product
    $stock_query = mysqli_query($conn, "
        SELECT SUM(stock_quantity) as total_stock 
        FROM product_variants 
        WHERE product_id = '$product_id' AND is_active = 1
    ");
    
    $stock_data = mysqli_fetch_assoc($stock_query);
    $total_stock = $stock_data['total_stock'] ?? 0;
    
    // Update the stock_quantity in the parent products table
    $update_query = mysqli_prepare($conn, "
        UPDATE products SET stock_quantity = ? WHERE product_id = ?
    ");
    $update_query->bind_param('is', $total_stock, $product_id);
    $update_query->execute();
}

// echo "<br>";
// echo "name => ". $product_name;
// echo "<br>";
// echo "Description => ".$description;
// echo "<br>";
// echo "sku => ".$product_sku;
// echo "<br>";
// echo "stock quantity => ".$stock_quantity;
// echo "<br>";
// echo "base price => ".$base_price;
// echo "<br>";
// echo "discount => ".$discount_price;
// echo "<br>";
// echo "category => ".$product_category;
// echo "<br>";
// echo "tag => ".$product_tag;
// echo "<br>";
// echo "brand => ".$product_brand;
// echo "<br>";
// echo "is featured => ".$is_featured;
