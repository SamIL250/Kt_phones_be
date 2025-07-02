<?php 
include './config/config.php';
require './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function errorRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:./auth/signin');
    exit();
}

$key = "gfrewbfiugrwekjfiueg_fewfmy_secrete_key_34rgfrewbfiugrwekjfiueg";

$admin_id = "";
$admin_email = "";

//check if the admin is logged in
if(!isset($_COOKIE['admin_token'])) {
    errorRedirectWithMessage('You have no active access token!, Sign in again.');
}

$token = $_COOKIE['admin_token'];

try{
    //decode token to show user data
    $decode = JWT::decode($token, new Key($key, 'HS256'));
    $admin_id = $decode -> admin -> admin_id;
    $admin_email = $decode -> admin -> admin_email;

} catch (ExpiredException $e) {
    errorRedirectWithMessage("You have no active access token!, Sign in again.");
} catch (Exception $e) {
    errorRedirectWithMessage("You have no active access token!, Sign in again.");
}