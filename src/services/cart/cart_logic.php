<?php
// src/services/cart/cart_logic.php

// Always normalize guest cart to array of arrays
function normalizeGuestCart() {
    if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart']) && !isset($_SESSION['guest_cart'][0]['product_id'])) {
        $normalized = [];
        foreach ($_SESSION['guest_cart'] as $pid => $qty) {
            if (is_array($qty) && isset($qty['product_id'])) {
                $normalized[] = $qty;
            } else {
                $normalized[] = ['product_id' => $pid, 'quantity' => $qty];
            }
        }
        $_SESSION['guest_cart'] = $normalized;
    }
}

function getGuestCart() {
    normalizeGuestCart();
    return isset($_SESSION['guest_cart']) ? $_SESSION['guest_cart'] : [];
}

function saveGuestCart($cart) {
    $_SESSION['guest_cart'] = $cart;
}

function clearGuestCart() {
    $_SESSION['guest_cart'] = [];
}

function get_cart_contents($conn, $is_logged_in, $customer_id) {
    $cart = [];
    if ($is_logged_in) {
        $get_cart_items_query = "SELECT 
            p.product_id, p.name, p.base_price, p.discount_price, p.stock_quantity,
            ci.cart_item_id, ci.quantity as cart_quantity, ci.variant_id, ci.unit_price,
            size_val.value as storage_value,
            color_val.value as color_value,
            (SELECT GROUP_CONCAT(pi.image_url SEPARATOR ', ') FROM product_images pi WHERE pi.product_id = p.product_id AND pi.is_primary = 1) as image_urls
            FROM cart c
            JOIN cart_item ci ON ci.cart_id = c.cart_id
            JOIN products p ON p.product_id = ci.product_id
            LEFT JOIN product_variants pv ON ci.variant_id = pv.variant_id
            LEFT JOIN attribute_value size_val ON pv.size_id = size_val.attribute_value_id
            LEFT JOIN attribute_value color_val ON pv.color_id = color_val.attribute_value_id
            WHERE c.user_id = '$customer_id'
            ORDER BY ci.cart_item_id";
        $get_cart_items_result = mysqli_query($conn, $get_cart_items_query);
        if ($get_cart_items_result) {
            while ($row = mysqli_fetch_assoc($get_cart_items_result)) {
                $cart[] = $row;
            }
        }
    } else {
        $guest_cart = getGuestCart();
        if (!empty($guest_cart) && is_array($guest_cart)) {
            foreach ($guest_cart as $item) {
                if (isset($item['product_id'])) {
                    $product_id = mysqli_real_escape_string($conn, $item['product_id']);
                    $variant_id = isset($item['variant_id']) ? intval($item['variant_id']) : null;
                    
                    $product_query_sql = "SELECT 
                        p.product_id, p.name, p.base_price, p.discount_price, p.stock_quantity,
                        (SELECT GROUP_CONCAT(pi.image_url SEPARATOR ', ') FROM product_images pi WHERE pi.product_id = p.product_id AND pi.is_primary = 1) as image_urls";
                    
                    if ($variant_id) {
                        $product_query_sql .= ",
                        pv.price as variant_price,
                        size_val.value as storage_value,
                        color_val.value as color_value
                        FROM products p
                        LEFT JOIN product_variants pv ON p.product_id = pv.product_id AND pv.variant_id = $variant_id
                        LEFT JOIN attribute_value size_val ON pv.size_id = size_val.attribute_value_id
                        LEFT JOIN attribute_value color_val ON pv.color_id = color_val.attribute_value_id
                        WHERE p.product_id = '$product_id'";
                    } else {
                        $product_query_sql .= " FROM products p WHERE p.product_id = '$product_id'";
                    }
                    
                    $product_query = mysqli_query($conn, $product_query_sql);
                    if ($product = mysqli_fetch_assoc($product_query)) {
                        $product['cart_quantity'] = $item['quantity'];
                        $product['variant_id'] = $variant_id;
                        
                        // Use variant price if available
                        if ($variant_id && isset($product['variant_price'])) {
                            $product['unit_price'] = $product['variant_price'];
                        } else {
                            $product['unit_price'] = ($product['discount_price'] > 0) ? $product['discount_price'] : $product['base_price'];
                        }
                        
                        $cart[] = $product;
                    }
                }
            }
        }
    }
    return $cart;
}

function calculate_totals($cart_items) {
    $subtotal = 0;
    $discount = 0;
    $shipping = 30; // Flat rate shipping
    $tax_rate = 0.18; // 18% tax

    foreach ($cart_items as $item) {
        // Use variant price if available, otherwise use base product price
        $price = isset($item['unit_price']) ? $item['unit_price'] : 
                (($item['discount_price'] > 0) ? $item['discount_price'] : $item['base_price']);
        
        $line_total = $price * $item['cart_quantity'];
        $subtotal += $line_total;
        
        // Calculate discount only for base product (not variants)
        if (!$item['variant_id'] && $item['discount_price'] > 0) {
            $original_total = $item['base_price'] * $item['cart_quantity'];
            $discount += ($original_total - $line_total);
        }
    }
    $tax = round($subtotal * $tax_rate, 2);
    $total = $subtotal + $tax + $shipping;
    return [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'shipping' => $shipping,
        'tax' => $tax,
        'total' => $total,
        'item_count' => count($cart_items)
    ];
}
