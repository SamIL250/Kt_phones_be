<?php
// src/services/cart/cart_handler.php
header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../../config/config.php';
require_once './cart_logic.php';
require_once '../../../vendor/autoload.php'; // If not already included
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jsonResponse($success, $message, $data = null) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

function redirectWithMessage($message, $location = '../../../cart') {
    $_SESSION['notification'] = $message;
    header('Location: ' . $location);
    exit();
}

$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";

$is_logged_in = false;
$customer_id = null;

// Check session first
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] && isset($_SESSION['customer_id'])) {
    $is_logged_in = true;
    $customer_id = $_SESSION['customer_id'];
} elseif (isset($_COOKIE['customer_token'])) {
    // Fallback to JWT cookie
    try {
        $token = $_COOKIE['customer_token'];
        $decode = JWT::decode($token, new Key($key, 'HS256'));
        $customer_id = $decode->customer->customer_id ?? null;
        if ($customer_id) {
            $is_logged_in = true;
        }
    } catch (Exception $e) {
        $is_logged_in = false;
        $customer_id = null;
    }
}

$input = json_decode(file_get_contents('php://input'), true);
$is_ajax = !is_null($input);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($is_ajax) {
        jsonResponse(false, 'Invalid request method.');
    }
    header('Location: ../../../cart');
    exit();
}

// Determine action and data source
$action = null;
$post_data = [];

if ($is_ajax) {
    $action = $input['action'] ?? 'add_item';
    $post_data = $input;
} else {
    $action = $_POST['action'] ?? 'add_item';
    $post_data = $_POST;
}

$product_id = $post_data['product_id'] ?? '';
$variant_id = $post_data['variant_id'] ?? null;
$quantity = isset($post_data['quantity']) ? (int)$post_data['quantity'] : 1;
$user_id = $post_data['user_id'] ?? $customer_id;
$cart_item_id = $post_data['cart_item_id'] ?? null;

// If variant_id is not provided, select the first available variant for the product (by variant_id ASC)
// Reference: product_variants table (see dashboard/src/services/products/new_product.php and .sql schema)
if (!$variant_id) {
    $variant_query = mysqli_query($conn, "SELECT variant_id FROM product_variants WHERE product_id = '" . mysqli_real_escape_string($conn, $product_id) . "' AND is_active = 1 ORDER BY variant_id ASC LIMIT 1");
    if ($variant_query && $variant_row = mysqli_fetch_assoc($variant_query)) {
        $variant_id = $variant_row['variant_id'];
    }
}

// Only validate required fields for each action
if ($action === 'remove_item') {
    if ($user_id) { // Logged-in user: require cart_item_id
        if (!$cart_item_id) {
            if ($is_ajax) {
                jsonResponse(false, 'Invalid cart item.');
            }
            redirectWithMessage('Invalid cart item.', '../../../store');
        }
    } else { // Guest user: require product_id
        if (!$product_id) {
            if ($is_ajax) {
                jsonResponse(false, 'Invalid product.');
            }
            redirectWithMessage('Invalid product.', '../../../store');
        }
    }
} else {
    // For all other actions, require product_id or cart_item_id
    if (!$product_id && !$cart_item_id) {
        if ($is_ajax) {
            jsonResponse(false, 'Invalid product.');
        }
        redirectWithMessage('Invalid product.', '../../../store');
    }
}

// Get or create a cart ID for logged-in users (skip for guest remove_item)
$cart_id = null;
if ($user_id) {
    $cart_query = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = '$user_id' AND status = 'active' LIMIT 1");
    if ($cart_query && $row = mysqli_fetch_assoc($cart_query)) {
        $cart_id = $row['cart_id'];
    } else {
        $cart_id = uniqid();
        mysqli_query($conn, "INSERT INTO cart (cart_id, user_id, status) VALUES ('$cart_id', '$user_id', 'active')");
    }
}

// Only build $existing_item_query for actions that need it (not remove_item)
if ($action !== 'remove_item') {
    $existing_item_query = "SELECT cart_item_id, quantity FROM cart_item WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    if ($variant_id) {
        $existing_item_query .= " AND variant_id = " . intval($variant_id);
    } else {
        $existing_item_query .= " AND variant_id IS NULL";
    }
    $existing_item_result = mysqli_query($conn, $existing_item_query);
    $existing_item = $existing_item_result ? mysqli_fetch_assoc($existing_item_result) : null;
}

error_log('user_id: ' . $user_id . ', cart_item_id: ' . $cart_item_id . ', product_id: ' . $product_id . ', is_logged_in: ' . ($is_logged_in ? 'true' : 'false'));

switch ($action) {
    case 'remove_item':
        if ($user_id) { // Logged-in user
            if ($cart_item_id) {
                $delete_query = mysqli_prepare($conn, "DELETE FROM cart_item WHERE cart_item_id = ?");
                $delete_query->bind_param('i', $cart_item_id);
                $delete_query->execute();
                if ($is_ajax && $delete_query->affected_rows === 0) {
                    jsonResponse(false, 'Failed to delete item from cart. (No matching row)');
                }
            }
        } else { // Guest user
            $guest_cart = getGuestCart();
            $guest_cart = array_filter($guest_cart, function($item) use ($product_id, $variant_id) {
                $is_match = $item['product_id'] === $product_id;
                $current_variant_id = $item['variant_id'] ?? null;
                return !($is_match && $current_variant_id == $variant_id);
            });
            saveGuestCart(array_values($guest_cart));
        }
        break;

    case 'update_quantity':
        if ($quantity < 1) $quantity = 1;
        if ($user_id) { // Logged-in user
            if ($existing_item) {
                mysqli_query($conn, "UPDATE cart_item SET quantity = $quantity WHERE cart_item_id = " . $existing_item['cart_item_id']);
            }
        } else { // Guest user
            $guest_cart = getGuestCart();
            foreach ($guest_cart as &$item) {
                $current_variant_id = $item['variant_id'] ?? null;
                if ($item['product_id'] === $product_id && $current_variant_id == $variant_id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            saveGuestCart($guest_cart);
        }
        break;

    case 'add_item':
    default:
        if ($user_id) { // Logged-in user
            if ($existing_item) {
                $new_quantity = $existing_item['quantity'] + $quantity;
                mysqli_query($conn, "UPDATE cart_item SET quantity = $new_quantity WHERE cart_item_id = " . $existing_item['cart_item_id']);
            } else {
                $unit_price = 0;
                if ($variant_id) {
                    $price_query = mysqli_query($conn, "SELECT price FROM product_variants WHERE variant_id = " . intval($variant_id));
                    $unit_price = $price_query ? mysqli_fetch_assoc($price_query)['price'] : 0;
                } else {
                    $price_query = mysqli_query($conn, "SELECT base_price, discount_price FROM products WHERE product_id = '$product_id'");
                    $price_data = $price_query ? mysqli_fetch_assoc($price_query) : ['base_price' => 0, 'discount_price' => 0];
                    $unit_price = $price_data['discount_price'] > 0 ? $price_data['discount_price'] : $price_data['base_price'];
                }
                $variant_sql = $variant_id ? intval($variant_id) : "NULL";
                mysqli_query($conn, "INSERT INTO cart_item (cart_id, product_id, quantity, unit_price, variant_id) VALUES ('$cart_id', '$product_id', $quantity, $unit_price, $variant_sql)");
            }
        } else { // Guest user
            $guest_cart = getGuestCart();
            $found = false;
            foreach ($guest_cart as &$item) {
                $current_variant_id = $item['variant_id'] ?? null;
                if ($item['product_id'] === $product_id && $current_variant_id == $variant_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $guest_cart[] = ['product_id' => $product_id, 'quantity' => $quantity, 'variant_id' => $variant_id];
            }
            saveGuestCart($guest_cart);
        }
        break;
}

if ($is_ajax) {
    $cart_items = get_cart_contents($conn, $is_logged_in, $user_id);
    $totals = calculate_totals($cart_items);
    jsonResponse(true, 'Cart updated successfully.', $totals);
} else {
    redirectWithMessage('Product added to your cart.', '../../../cart');
}
