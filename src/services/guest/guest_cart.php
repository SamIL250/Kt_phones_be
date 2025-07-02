<?php
header('Content-Type: application/json');

// Cookie settings
define('COOKIE_NAME', 'guest_cart');
define('COOKIE_EXPIRY', 30 * 24 * 60 * 60); // 30 days
define('COOKIE_PATH', '/');
define('COOKIE_SECURE', true); // Set to true in production
define('COOKIE_HTTPONLY', true);

// Function to get guest cart items
function getGuestCart() {
    if (isset($_COOKIE[COOKIE_NAME])) {
        try {
            $cartData = json_decode($_COOKIE[COOKIE_NAME], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $cartData;
            }
        } catch (Exception $e) {
            error_log('Error decoding guest cart: ' . $e->getMessage());
        }
    }
    return [];
}

// Function to save guest cart items
function saveGuestCart($cartItems) {
    try {
        $cookieValue = json_encode($cartItems);
        if (json_last_error() === JSON_ERROR_NONE) {
            setcookie(
                COOKIE_NAME,
                $cookieValue,
                [
                    'expires' => time() + COOKIE_EXPIRY,
                    'path' => COOKIE_PATH,
                    'secure' => COOKIE_SECURE,
                    'httponly' => COOKIE_HTTPONLY,
                    'samesite' => 'Lax'
                ]
            );
            $_COOKIE[COOKIE_NAME] = $cookieValue; // Set for current request
            return true;
        }
    } catch (Exception $e) {
        error_log('Error saving guest cart: ' . $e->getMessage());
    }
    return false;
}

// Function to add item to guest cart
function addToGuestCart($productId, $quantity = 1, $price, $name, $image) {
    $cart = getGuestCart();
    
    // Validate input
    if (!$productId || !is_numeric($quantity) || $quantity <= 0 || !is_numeric($price) || $price < 0) {
        return false;
    }
    
    // Check if product already exists
    $productExists = false;
    foreach ($cart as &$item) {
        if ($item['product_id'] === $productId) {
            // Update quantity for existing product
            $item['quantity'] = $quantity; // Replace quantity instead of adding
            $item['price'] = $price; // Update price in case it changed
            $item['name'] = $name; // Update name in case it changed
            $item['image'] = $image; // Update image in case it changed
            $item['updated_at'] = time(); // Add update timestamp
            $productExists = true;
            break;
        }
    }
    
    // If product doesn't exist, add it
    if (!$productExists) {
        $cart[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'name' => $name,
            'image' => $image,
            'added_at' => time()
        ];
    }
    
    return saveGuestCart($cart) ? $cart : false;
}

// Function to remove item from guest cart
function removeFromGuestCart($productId) {
    $cart = getGuestCart();
    $cart = array_filter($cart, function($item) use ($productId) {
        return $item['product_id'] !== $productId;
    });
    saveGuestCart($cart);
    return $cart;
}

// Function to update guest cart item quantity
function updateGuestCartQuantity($productId, $quantity) {
    $cart = getGuestCart();
    foreach ($cart as &$item) {
        if ($item['product_id'] === $productId) {
            $item['quantity'] = $quantity;
            break;
        }
    }
    saveGuestCart($cart);
    return $cart;
}

// Handle different operations based on action parameter
$action = $_GET['action'] ?? '';
error_log('Guest cart action: ' . $action);

switch ($action) {
    case 'add':
        $productId = $_GET['product_id'] ?? '';
        $quantity = intval($_GET['quantity'] ?? 1);
        $price = floatval($_GET['price'] ?? 0);
        $name = $_GET['name'] ?? '';
        $image = $_GET['image'] ?? '';
        
        if ($productId && $price > 0) {
            $cart = addToGuestCart($productId, $quantity, $price, $name, $image);
            if ($cart !== false) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product added to cart',
                    'cart' => $cart
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to add product to cart'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product data'
            ]);
        }
        break;
        
    case 'remove':
        $productId = $_GET['product_id'] ?? '';
        if ($productId) {
            $cart = removeFromGuestCart($productId);
            // Set success message in session
            session_start();
            $_SESSION['notification'] = 'Product removed from cart';
            // Redirect back to cart page
            header('Location: ../../../cart');
            exit();
        } else {
            session_start();
            $_SESSION['notification'] = 'Missing product ID';
            header('Location: ../../../cart');
            exit();
        }
        break;
        
    case 'update':
        $productId = $_GET['product_id'] ?? '';
        $quantity = intval($_GET['quantity'] ?? 1);
        if ($productId && $quantity > 0) {
            $cart = updateGuestCartQuantity($productId, $quantity);
            session_start();
            $_SESSION['notification'] = 'Cart updated successfully';
            header('Location: ../../../cart');
            exit();
        } else {
            session_start();
            $_SESSION['notification'] = 'Invalid quantity';
            header('Location: ../../../cart');
            exit();
        }
        break;
        
    case 'clear':
        if (saveGuestCart([])) {
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to clear cart'
            ]);
        }
        break;
        
    case 'get':
        $cart = getGuestCart();
        error_log('Getting cart: ' . json_encode($cart));
        echo json_encode([
            'success' => true,
            'cart' => $cart
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?> 