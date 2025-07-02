<?php

/**
 * PHP Version and MySQL Support Check
 */

echo "<h2>PHP Version and MySQL Support Check</h2>";

// Check PHP version
echo "<h3>Current PHP Version:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check if mysqli extension is loaded
echo "<h3>MySQL Extensions:</h3>";
if (extension_loaded('mysqli')) {
    echo "<p style='color: green;'>✓ mysqli extension is loaded</p>";
    echo "<p><strong>mysqli version:</strong> " . mysqli_get_client_info() . "</p>";
} else {
    echo "<p style='color: red;'>✗ mysqli extension is NOT loaded</p>";
}

if (extension_loaded('pdo_mysql')) {
    echo "<p style='color: green;'>✓ PDO MySQL extension is loaded</p>";
} else {
    echo "<p style='color: red;'>✗ PDO MySQL extension is NOT loaded</p>";
}

// Check MySQL client version
echo "<h3>MySQL Client Information:</h3>";
if (function_exists('mysqli_get_client_info')) {
    echo "<p><strong>MySQL Client Version:</strong> " . mysqli_get_client_info() . "</p>";
}

// Check if caching_sha2_password is supported
echo "<h3>Authentication Method Support:</h3>";
echo "<p><strong>Note:</strong> caching_sha2_password support was added in PHP 7.4+</p>";

if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p style='color: green;'>✓ Your PHP version should support caching_sha2_password</p>";
} else {
    echo "<p style='color: orange;'>⚠ Your PHP version may not fully support caching_sha2_password</p>";
    echo "<p><strong>Recommended:</strong> Upgrade to PHP 7.4+ or PHP 8.x</p>";
}

// Check XAMPP version
echo "<h3>XAMPP Information:</h3>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// Check for XAMPP version file
$xampp_version_file = 'C:/xampp/xampp-control.exe';
if (file_exists($xampp_version_file)) {
    $xampp_version = filemtime($xampp_version_file);
    echo "<p><strong>XAMPP Control Panel Date:</strong> " . date('Y-m-d', $xampp_version) . "</p>";
}

echo "<hr>";
echo "<h3>Recommendations:</h3>";
echo "<ul>";
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo "<li style='color: orange;'>⚠ <strong>Upgrade PHP:</strong> Your current version may not support caching_sha2_password</li>";
} else {
    echo "<li style='color: green;'>✓ <strong>PHP Version OK:</strong> Your PHP version should support the authentication method</li>";
}
echo "<li><strong>Alternative:</strong> Change MySQL authentication method to mysql_native_password</li>";
echo "<li><strong>Best Practice:</strong> Keep both PHP and MySQL updated for security</li>";
echo "</ul>";

echo "<p><a href='fix_mysql_auth.php'>← Back to Fix Script</a></p>";
