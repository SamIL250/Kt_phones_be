<?php

// Get order ID from URL
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    ?>
    <script>window.location.replace('store')</script>
    <?php
    exit();
}

// Check if user is logged in
$customer_id = null;
$guest_email = null;
$is_guest = true;

if (!empty($customer_id)) {
    $is_guest = false;
}

// Get order details
$order_query = mysqli_prepare(
    $conn,
    "SELECT o.*, a.email, a.first_name, a.last_name, a.address_line1, a.address_line2, 
            a.country, a.district, a.sector, a.cell, a.phone_number
     FROM orders o 
     LEFT JOIN addresses a ON o.shipping_address_id = a.address_id 
     WHERE o.order_id = ?"
);

if (!$order_query) {
    die("Query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($order_query, "s", $order_id);

if (!mysqli_stmt_execute($order_query)) {
    die("Query execution failed: " . mysqli_stmt_error($order_query));
}

$order_result = mysqli_stmt_get_result($order_query);
$order_data = mysqli_fetch_assoc($order_result);

// Debug information
if (!$order_data) {
    error_log("No order found for ID: " . $order_id);
    ?>
    <script>window.location.replace('store')</script>
    <?php
    exit();
}

// Get order items
$items_query = mysqli_prepare(
    $conn,
    "SELECT oi.*, p.name as product_name, pi.image_url 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.product_id 
     LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
     WHERE oi.order_id = ?"
);

if (!$items_query) {
    die("Items query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($items_query, "s", $order_id);

if (!mysqli_stmt_execute($items_query)) {
    die("Items query execution failed: " . mysqli_stmt_error($items_query));
}

$items_result = mysqli_stmt_get_result($items_query);

// Calculate totals
$subtotal = 0;
$items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $subtotal += $item['subtotal'];
    $items[] = $item;
}

$shipping_cost = $order_data['shipping_cost'] ?? 0;
$tax_amount = $order_data['tax_amount'] ?? 0;
$total_amount = $order_data['total_amount'] ?? ($subtotal + $shipping_cost + $tax_amount);

// Get address details
$address_query = mysqli_prepare(
    $conn,
    "SELECT * FROM addresses WHERE address_id = ?"
);

if (!$address_query) {
    die("Address query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($address_query, "s", $order_data['shipping_address_id']);

if (!mysqli_stmt_execute($address_query)) {
    die("Address query execution failed: " . mysqli_stmt_error($address_query));
}

$address_result = mysqli_stmt_get_result($address_query);
$address_data = mysqli_fetch_assoc($address_result);

?>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-8">
                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h1 class="mt-4 text-2xl font-bold text-gray-900">Order Placed Successfully!</h1>
                    <p class="mt-2 text-gray-600">Thank you for your purchase. Your order has been received.</p>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h2>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Order Number</p>
                                    <p class="font-medium"><?= htmlspecialchars($order_data['order_id'] ?? '') ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Order Date</p>
                                    <p class="font-medium"><?= !empty($order_data['order_date']) ? date('F j, Y', strtotime($order_data['order_date'])) : '' ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Method</p>
                                    <p class="font-medium"><?= ucfirst(htmlspecialchars($order_data['payment_method'] ?? '')) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Shipping Method</p>
                                    <p class="font-medium"><?= ucfirst(htmlspecialchars($order_data['shipping_method'] ?? '')) ?></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                            <div class="space-y-3">
                                <p class="font-medium">
                                    <?= htmlspecialchars($address_data['first_name'] ?? '') ?> 
                                    <?= htmlspecialchars($address_data['last_name'] ?? '') ?>
                                </p>
                                <p><?= htmlspecialchars($address_data['address_line1'] ?? '') ?></p>
                                <?php if (!empty($address_data['address_line2'])): ?>
                                    <p><?= htmlspecialchars($address_data['address_line2']) ?></p>
                                <?php endif; ?>
                                <p>
                                    <?= htmlspecialchars($address_data['country'] ?? '') ?>,
                                    <?= htmlspecialchars($address_data['district'] ?? '') ?>
                                </p>
                                <p>
                                    <?= htmlspecialchars($address_data['sector'] ?? '') ?>,
                                    <?= htmlspecialchars($address_data['cell'] ?? '') ?>
                                </p>
                                <p>Phone: <?= htmlspecialchars($address_data['phone_number'] ?? '') ?></p>
                                <p>Email: <?= htmlspecialchars($address_data['email'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <p class="text-gray-600">Subtotal</p>
                                <p class="font-medium"><?= number_format($subtotal, 2) ?> Frw</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-600">Shipping</p>
                                <p class="font-medium"><?= number_format($shipping_cost, 2) ?> Frw</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-600">Tax</p>
                                <p class="font-medium"><?= number_format($tax_amount, 2) ?> Frw</p>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-3">
                                <p class="font-semibold">Total</p>
                                <p class="font-semibold"><?= number_format($total_amount, 2) ?> Frw</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                        <div class="space-y-4">
                            <?php foreach ($items as $item): ?>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900"><?= htmlspecialchars($item['product_name']) ?></h3>
                                        <p class="text-sm text-gray-500">Quantity: <?= $item['quantity'] ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium"><?= number_format($item['subtotal'], 2) ?> Frw</p>
                                        <p class="text-sm text-gray-500"><?= number_format($item['unit_price'], 2) ?> Frw each</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>