<?php
// Commented out all business/order logic for verification
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php'; // Adjust path as needed

// Collect POST data
$post_data = $_POST;

// Collect cart_id from POST
$cart_id = $_POST['cart_id'] ?? null;

// Collect customer_id from POST or cookie
$customer_id = $_POST['customer_id'] ?? null;
if (!$customer_id && isset($_COOKIE['customer_token'])) {
    $token = $_COOKIE['customer_token'];
    $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
    $customer_id = $payload['user_id'] ?? null;
}

// Collect session data (if any)
$session_data = $_SESSION;
$checkout = $session_data['checkout_summary'] ?? null;

if (!$cart_id || !$customer_id || !$checkout) {
    echo json_encode(['status' => 'error', 'message' => 'Missing cart or customer or checkout data']);
    exit;
}

// Output all collected data for verification
// $data = [
//     'post' => $post_data,
//     'cart_id' => $cart_id,
//     'customer_id' => $customer_id,
//     'session' => $session_data
// ];
// echo json_encode($data, JSON_PRETTY_PRINT);

// 2. Prepare payment data
$amount = $checkout['total'];
$currency = 'RWF'; // Change if needed
$redirect_url = 'http://localhost/Kt_phones_be/src/services/order/flutterwave_callback.php'; // Set your callback URL

// 3. Get customer info (fetch from DB if needed)
$customer_email = ''; $customer_name = ''; $customer_phone = '';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kt_phones_v2', 'root', ''); // Update credentials
    $stmt = $pdo->prepare("SELECT email, CONCAT(first_name, ' ', last_name) as name, phone_number FROM users WHERE user_id = ?");
    $stmt->execute([$customer_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $customer_email = $user['email'];
        $customer_name = $user['name'];
        $customer_phone = $user['phone_number'] ?? '';
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $e->getMessage()]);
    exit;
}

// 4. Prepare Flutterwave payload
$tx_ref = uniqid('ktp_');
$data = [
    "tx_ref" => $tx_ref,
    "amount" => $amount,
    "currency" => $currency,
    "redirect_url" => $redirect_url,
    "customer" => [
        "email" => $customer_email,
        "phonenumber" => $customer_phone,
        "name" => $customer_name
    ],
    "customizations" => [
        "title" => "KT Phones Order",
        "description" => "Payment for order $tx_ref",
        "logo" => "https://yourdomain.com/logo.png"
    ]
];

// 5. Call Flutterwave API
$secret_key = 'FLWSECK_TEST-6d9a0dee8510d1c3c013bf88bd61a151-X'; // Replace with your secret key

// Use cURL instead of Guzzle
$ch = curl_init('https://api.flutterwave.com/v3/payments');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $secret_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['status' => 'error', 'message' => $curl_error]);
    exit;
}
$body = json_decode($response, true);
if (isset($body['status']) && $body['status'] === 'success') {
    // Save tx_ref, cart_id, customer_id in session for later verification
    $_SESSION['pending_payment'] = [
        'tx_ref' => $tx_ref,
        'cart_id' => $cart_id,
        'customer_id' => $customer_id,
        'checkout' => $checkout,
        'post' => $post_data
    ];
    // Return payment link
    echo json_encode(['status' => 'success', 'link' => $body['data']['link']]);
} else {
    echo json_encode(['status' => 'error', 'message' => $body['message'] ?? 'Payment initialization failed']);
}
exit; 