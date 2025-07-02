<?php
session_start();

// Flutterwave secret key
$secret_key = 'FLWSECK_TEST-6d9a0dee8510d1c3c013bf88bd61a151-X'; // Replace with your secret key

if (!isset($_GET['transaction_id'])) {
    die('No transaction ID');
}
$transaction_id = $_GET['transaction_id'];

// 1. Verify payment with Flutterwave using cURL
$ch = curl_init("https://api.flutterwave.com/v3/transactions/$transaction_id/verify");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $secret_key
]);
$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);
if ($curl_error) {
    die('Curl error: ' . $curl_error);
}
$body = json_decode($response, true);
if (!($body['status'] === 'success' && $body['data']['status'] === 'successful')) {
    die('Payment not successful');
}

// 2. Retrieve pending payment info from session
$pending = $_SESSION['pending_payment'] ?? null;
if (!$pending) die('No pending payment found in session');
$checkout = $pending['checkout'];
$post = $pending['post'];

// 3. Save order and order items in DB
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kt_phones_v2', 'root', ''); // Update credentials
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    $order_id = uniqid('ORD');
    $customer_id = $pending['customer_id'];
    $total_amount = $checkout['total'];
    $shipping_cost = $checkout['shipping_cost'] ?? 0;
    $tax_amount = $checkout['tax'] ?? 0;
    $shipping_method = $post['delivery_type'] ?? 'standard';
    $payment_method = 'flutterwave';
    $payment_status = 'paid';
    $order_date = date('Y-m-d H:i:s');
    $session_id = session_id();
    // For demo, use '0' for address fields (should be replaced with real address IDs)
    $shipping_address_id = '0';
    $billing_address_id = '0';
    $is_guest_order = 0;
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, total_amount, status, order_date, shipping_address_id, billing_address_id, is_guest_order, payment_method, payment_status, shipping_method, shipping_cost, tax_amount, session_id) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $order_id,
        $customer_id,
        $total_amount,
        $order_date,
        $shipping_address_id,
        $billing_address_id,
        $is_guest_order,
        $payment_method,
        $payment_status,
        $shipping_method,
        $shipping_cost,
        $tax_amount,
        $session_id
    ]);
    // Insert order items
    foreach ($checkout['products'] as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal, variant_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['unit_price'],
            $item['unit_price'] * $item['quantity'],
            $item['variant_id'] ?? null
        ]);
    }
    $pdo->commit();
    // 4. Clear session/cart as needed
    unset($_SESSION['pending_payment']);
    // 5. Redirect to receipt page
    header('Location: /src/views/checkout/success/success.php?order_id=' . $order_id);
    exit;
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    die('DB error: ' . $e->getMessage());
} 