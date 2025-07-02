<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../../config/config.php';
include __DIR__ . '/../services/auth/auth_state.php';
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KT Phones - Your trusted destination for premium smartphones and exceptional customer service. Shop the latest smartphones with unbeatable prices.">
    <meta name="keywords" content="smartphones, mobile phones, KT Phones, premium phones, Rwanda, Kigali">
    <meta name="author" content="KT Phones">
    <meta property="og:title" content="KT Phones - Premium Smartphones">
    <meta property="og:description" content="Your trusted destination for premium smartphones and exceptional customer service.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ktphones.com">
    <meta property="og:image" content="./src/assets/icons/kt_logo_2.png">
    <link rel="shortcut icon" href="./src/assets/icons/kt_logo_2.png" type="image/x-icon">
    <link rel="stylesheet" href="./src/assets/css/maserati-theme.css">
    <?php
    include __DIR__ . '/../components/cdns.php';
    ?>
    <title>KT Phones - Premium Smartphones</title>
</head>

<body class="overflow-x-hidden">
    <!-- Loader -->
    <div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <?php
    // include __DIR__ . '/../components/search.php';
    include __DIR__ . '/../components/navbar_new.php';
    include __DIR__ . '/../components/toasts.php';
    include __DIR__ . '/../components/cookies.php';
    ?>
    <!-- // Contents -->