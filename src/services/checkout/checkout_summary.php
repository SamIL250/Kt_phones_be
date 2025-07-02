<?php
session_start();
// Get and validate POST parameters
$products = $_POST['products'] ?? [];
$customer_id = $_POST['customer'] ?? null;
$subtotal = $_POST['subtotal'] ?? 0;
$discount = $_POST['discount'] ?? 0;
$tax = $_POST['tax'] ?? 0;
$shipping_cost = $_POST['shipping_cost'] ?? 0;
$total = $_POST['total'] ?? 0;

// $products is now an array of arrays, each with:
// [product_id, variant_id, name, color, storage, unit_price, quantity]
// No need to cast to int, keep as associative array for checkout display

$customer_id = (int) $customer_id;
$subtotal = (float) $subtotal;
$discount = (float) $discount;
$tax = (float) $tax;
$shipping_cost = (float) $shipping_cost;
$total = (float) $total;

// Save everything into session for use on the checkout page
$_SESSION['checkout_summary'] = [
    'products' => $products, // array of detailed product info
    'customer_id' => $customer_id,
    'subtotal' => $subtotal,
    'discount' => $discount,
    'tax' => $tax,
    'shipping_cost' => $shipping_cost,
    'total' => $total
];

// Optional: redirect or proceed
header('Location: ../../../checkout');
exit;
