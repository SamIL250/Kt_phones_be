<?php
session_start();
include '../../../config/config.php';
require '../../../vendor/autoload.php';

use Firebase\JWT\JWT;

function errorRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:../../../signin');
    exit();
}
function redirectWithMessage($message) {
    $_SESSION['notification'] = $message;
    header('location:../../../store');
    exit();
}

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (empty($email) || empty($password)) {
    errorRedirectWithMessage("All fields are required!");
}

// validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorRedirectWithMessage("Invalid email format");
}

//check user email
$user = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email = '$email' AND password_hash = '$password' AND is_active = 1 AND is_admin != 1"
);

if (mysqli_num_rows($user) == 0) {
    errorRedirectWithMessage("Incorrect email or password, Try again!");
}

$user_id = "";
$user_email = "";

foreach ($user as $data) {
    $user_email = $data['email'];
    $user_id = $data['user_id'];
}

//admin JWT token
$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$token = JWT::encode(
    array(
        'iat' => time(),
        'nbf' => time(),
        'exp' => time() + 3600, // 1 hr
        'customer' => array(
            'customer_id' => $user_id,
            'customer_email' => $user_email
        )
    ),
    $key,
    'HS256'
);

//store jwt oin cookies
$set_cookie = setcookie("customer_token", $token, time() + 3600, "/", "", false, true);

if($set_cookie) {
    redirectWithMessage("Welcome back!, Enjoy your shopping.");
} else {
    errorRedirectWithMessage("Failed to sign in, Try again later");
}