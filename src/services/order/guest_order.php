<?php
// Commented out all business/order logic for verification
header('Content-Type: application/json');
session_start();

// 1. Collect guest info from POST
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// 2. Get checkout summary from session
$checkout = $_SESSION['checkout_summary'] ?? null;
if (!$first_name || !$last_name || !$email || !$phone || !$checkout) {
    echo json_encode(['status' => 'error', 'message' => 'Missing guest info or checkout summary']);
    exit;
}

// 3. Prepare payment data
$amount = $checkout['total'];
$currency = 'RWF';
$redirect_url = 'http://localhost/Kt_phones_be/src/services/order/flutterwave_callback.php'; // Set your callback URL
$customer_name = $first_name . ' ' . $last_name;

$tx_ref = uniqid('ktp_');
$data = [
    "tx_ref" => $tx_ref,
    "amount" => $amount,
    "currency" => $currency,
    "redirect_url" => $redirect_url,
    "customer" => [
        "email" => $email,
        "phonenumber" => $phone,
        "name" => $customer_name
    ],
    "customizations" => [
        "title" => "KT Phones Order",
        "description" => "Payment for order $tx_ref",
        "logo" => "https://gotallnews.com/lerony_client_f_x_y_b/kt_logo.png"
    ]
];

// 4. Call Flutterwave API
$secret_key = 'FLWSECK_TEST-6d9a0dee8510d1c3c013bf88bd61a151-X'; // Replace with your secret key
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
    // 5. Save tx_ref, guest info, and summary in session for callback
    $_SESSION['pending_payment'] = [
        'tx_ref' => $tx_ref,
        'cart_id' => null, // No cart_id for guest
        'customer_id' => null, // No customer_id for guest
        'checkout' => $checkout,
        'post' => $_POST,
        'is_guest' => true
    ];
    // 6. Redirect to payment link
    header('Location: ' . $body['data']['link']);
    // Fallback: If redirect fails, show HTML link
    echo '<!DOCTYPE html>
    <html>
    <head><title>Redirecting to Payment...</title></head>
    <body>
        <p>If you are not redirected automatically, <a href="' . htmlspecialchars($body['data']['link']) . '">click here to proceed to payment</a>.</p>
    </body>
    </html>';
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => $body['message'] ?? 'Payment initialization failed']);
    exit;
}

// Collect guest cart from cookie (if present)
$guest_cart = isset($_COOKIE['guest_cart']) ? json_decode($_COOKIE['guest_cart'], true) : null;

// Collect session data (if any)
$session_data = $_SESSION;

// Output all collected data for verification
$data = [
    'post' => $_POST,
    'guest_cart' => $guest_cart,
    'session' => $session_data
];
echo json_encode($data, JSON_PRETTY_PRINT);
exit; 