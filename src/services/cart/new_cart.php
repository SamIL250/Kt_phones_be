<?php
header('Content-Type: application/json');

include '../../../config/config.php';
session_start();

$product = $_GET['product'] ?? null;
$user = $_GET['user'] ?? null;
$unit_price = $_GET['price'] ?? null;

if (!$product || !$user || !$unit_price) {
    echo json_encode([
        "success" => false,
        "message" => "Missing product, user ID, or unit price"
    ]);
    exit;
}

function generateUuid($length): string
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

$cart_id = generateUuid(20);
$quantity = 1;

$check_users_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user'");

if (mysqli_num_rows($check_users_cart) > 0) {
    $get_cart_id = mysqli_fetch_array($check_users_cart)['cart_id'];

    $check_cart_item_quantity = mysqli_query(
        $conn,
        "SELECT quantity FROM cart_item WHERE cart_id = '$get_cart_id' AND product_id = '$product'"
    );

    if (mysqli_num_rows($check_cart_item_quantity) > 0) {
        $get_current_quantity = mysqli_fetch_array($check_cart_item_quantity)['quantity'];
        $quantity += $get_current_quantity;

        $update_cart_item = mysqli_query(
            $conn,
            "UPDATE cart_item 
             SET quantity = '$quantity' 
             WHERE cart_id = '$get_cart_id' AND product_id = '$product'"
        );

        if (!$update_cart_item) {
            echo json_encode(["success" => false, "message" => "Failed to update cart item"]);
            exit;
        }
    } else {
        $create_cart_item = mysqli_query(
            $conn,
            "INSERT INTO cart_item(cart_id, product_id, quantity, unit_price)
            VALUES ('$get_cart_id', '$product', '$quantity', '$unit_price')"
        );

        if (!$create_cart_item) {
            echo json_encode(["success" => false, "message" => "Failed to add to cart"]);
            exit;
        }
    }

    echo json_encode(["success" => true, "message" => "Product added to cart"]);
    exit;
} else {
    // No cart exists, create one
    $create_user_cart = mysqli_query(
        $conn,
        "INSERT INTO `cart`(`cart_id`, `user_id`) VALUES ('$cart_id','$user')"
    );

    if (!$create_user_cart) {
        echo json_encode(["success" => false, "message" => "Failed to create user's cart"]);
        exit;
    }

    $create_cart_item = mysqli_query(
        $conn,
        "INSERT INTO cart_item(cart_id, product_id, quantity, unit_price)
        VALUES ('$cart_id', '$product', '$quantity', '$unit_price')"
    );

    if (!$create_cart_item) {
        echo json_encode(["success" => false, "message" => "Failed to create cart item"]);
        exit;
    }

    echo json_encode(["success" => true, "message" => "Product added to cart"]);
}
