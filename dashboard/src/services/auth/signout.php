<?php
session_start();
function errorRedirectWithMessage($message): void
{
    $_SESSION['notification'] = $message;
    header('location:../../../auth/signin');
    exit();
}
//clear the JWT token 
$logout = setcookie("admin_token", "", time() - 3600, "/", "", false, true);

if($logout) {
    errorRedirectWithMessage("You have been logged out!");
} 
