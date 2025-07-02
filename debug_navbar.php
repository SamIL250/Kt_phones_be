<?php

/**
 * Debug Navbar Test
 * This will help us understand why the navbar is showing raw PHP code
 */

echo "<h1>Debug Navbar Test</h1>";

// Test 1: Check if PHP is working
echo "<h2>Test 1: PHP Processing</h2>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 2: Check if database connection exists
echo "<h2>Test 2: Database Connection</h2>";
include __DIR__ . '/config/config.php';

if (isset($conn) && $conn) {
    echo "<p style='color: green;'>✓ Database connection exists</p>";

    // Test categories query
    $test_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM categories");
    if ($test_query) {
        $row = mysqli_fetch_assoc($test_query);
        echo "<p style='color: green;'>✓ Categories query works: " . $row['count'] . " categories found</p>";
    } else {
        echo "<p style='color: red;'>✗ Categories query failed</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection not available</p>";
}

// Test 3: Check if auth variables exist
echo "<h2>Test 3: Auth Variables</h2>";
include __DIR__ . '/src/services/auth/auth_state.php';

echo "<p>is_logged_in: " . ($is_logged_in ? 'true' : 'false') . "</p>";
echo "<p>customer_id: " . ($customer_id ?? 'not set') . "</p>";
echo "<p>customer_name: " . ($customer_name ?? 'not set') . "</p>";

// Test 4: Try to include navbar directly
echo "<h2>Test 4: Direct Navbar Include</h2>";
echo "<div style='border: 2px solid red; padding: 10px;'>";
echo "<h3>Navbar Output:</h3>";
ob_start();
include __DIR__ . '/src/components/navbar.php';
$navbar_output = ob_get_clean();
echo htmlspecialchars($navbar_output);
echo "</div>";

// Test 5: Check file permissions
echo "<h2>Test 5: File Permissions</h2>";
$navbar_file = __DIR__ . '/src/components/navbar.php';
if (file_exists($navbar_file)) {
    echo "<p style='color: green;'>✓ Navbar file exists</p>";
    echo "<p>File size: " . filesize($navbar_file) . " bytes</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($navbar_file)), -4) . "</p>";
} else {
    echo "<p style='color: red;'>✗ Navbar file not found</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='home.php'>Test Home Page</a></li>";
echo "<li><a href='test_php.php'>Test PHP</a></li>";
echo "</ul>";
