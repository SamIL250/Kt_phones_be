<?php
// Database connection is already included in layout.php
// No need to include config.php again

require __DIR__ . '/../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

// Secret key
$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";

// Default values
$customer_id = "";
$customer_email = "";
$first_name = "";
$last_name = "";
$full_name = "";

$is_logged_in = false;

if (isset($_COOKIE['customer_token'])) {
    $token = $_COOKIE['customer_token'];

    try {
        // Decode token
        $decode = JWT::decode($token, new Key($key, 'HS256'));
        $customer_id = $decode->customer->customer_id ?? '';
        $customer_email = $decode->customer->customer_email ?? '';

        // validate decoded fields
        if ($customer_id && $customer_email && isset($conn) && $conn) {
            $is_logged_in = true;

            //getting customer data(names)
            $get_customer_data = mysqli_query(
                $conn,
                "SELECT first_name, last_name FROM users WHERE user_id = '$customer_id'"
            );

            if ($get_customer_data) {
                foreach ($get_customer_data as $data) {
                    $first_name = $data['first_name'];
                    $last_name = $data['last_name'];
                    $full_name = $first_name . " " . $last_name;
                }
            }
        }
    } catch (ExpiredException $e) {
        // Token is expired
        $is_logged_in = false;
    } catch (Exception $e) {
        // Invalid token or decoding issue
        $is_logged_in = false;
    }
}
