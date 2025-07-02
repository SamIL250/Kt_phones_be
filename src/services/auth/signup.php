<?php
session_start();
include '../../../config/config.php';
require '../../../vendor/autoload.php';

use Firebase\JWT\JWT;

function errorRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:../../../signup');
    exit();
}

function redirectWithMessage($message)
{
    $_SESSION['notification'] = $message;
    header('location:../../../store');
    exit();
}

// Sanitize inputs
$first_name = mysqli_real_escape_string($conn, $_POST['fname']);
$last_name = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

// Validate inputs
if (empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
    errorRedirectWithMessage("All fields are required!");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorRedirectWithMessage("Invalid email format");
}

if ($password !== $confirm_password) {
    errorRedirectWithMessage("Passwords do not match!");
}

// Check if user already exists
$user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND is_active = 1 AND is_admin != 1");
if (mysqli_num_rows($user) > 0) {
    errorRedirectWithMessage("User with that email is already registered!");
}


// Insert user into database
$insert = mysqli_query($conn, "
    INSERT INTO users (first_name, last_name, email, password_hash, is_active, is_admin)
    VALUES ('$first_name', '$last_name', '$email', '$password', 1, 0)
");

if (!$insert) {
    errorRedirectWithMessage("Registration failed. Try again later.");
}

// Get the newly created user ID
$user_id = mysqli_insert_id($conn);

// JWT generation
$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";
$token = JWT::encode(
    [
        'iat' => time(),
        'nbf' => time(),
        'exp' => time() + 3600, // 1 hour
        'customer' => [
            'customer_id' => $user_id,
            'customer_email' => $email
        ]
    ],
    $key,
    'HS256'
);

// Store JWT in cookie
$set_cookie = setcookie("customer_token", $token, time() + 3600, "/", "", false, true);

if ($set_cookie) {
    redirectWithMessage("Welcome $first_name! Your account has been created.");
} else {
    errorRedirectWithMessage("Registration successful, but failed to sign you in automatically.");
}
