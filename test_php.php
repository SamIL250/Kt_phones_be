<?php

/**
 * Simple PHP Test File
 * This will help verify that PHP is working and database connection is established
 */

echo "<h1>PHP Test Page</h1>";

// Test 1: PHP is working
echo "<h2>Test 1: PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p style='color: green;'>✓ PHP is working correctly!</p>";

// Test 2: Database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    include __DIR__ . '/config/config.php';

    if (isset($conn) && $conn) {
        echo "<p style='color: green;'>✓ Database connection successful!</p>";

        // Test 3: Simple query
        echo "<h2>Test 3: Database Query</h2>";
        $test_query = "SELECT 1 as test";
        $result = mysqli_query($conn, $test_query);

        if ($result) {
            echo "<p style='color: green;'>✓ Database query successful!</p>";

            // Test 4: Check if kt_phones database exists
            echo "<h2>Test 4: Database Check</h2>";
            $db_query = "SHOW DATABASES LIKE 'kt_phones'";
            $db_result = mysqli_query($conn, $db_query);

            if (mysqli_num_rows($db_result) > 0) {
                echo "<p style='color: green;'>✓ kt_phones database exists!</p>";

                // Test 5: Check categories table
                $table_query = "SHOW TABLES LIKE 'categories'";
                $table_result = mysqli_query($conn, $table_query);

                if (mysqli_num_rows($table_result) > 0) {
                    echo "<p style='color: green;'>✓ categories table exists!</p>";

                    // Test 6: Count categories
                    $count_query = "SELECT COUNT(*) as count FROM categories";
                    $count_result = mysqli_query($conn, $count_query);

                    if ($count_result) {
                        $row = mysqli_fetch_assoc($count_result);
                        echo "<p style='color: green;'>✓ Found " . $row['count'] . " categories in database!</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠ categories table does not exist</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ kt_phones database does not exist</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Database query failed: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

// Test 7: File includes
echo "<h2>Test 7: File Includes</h2>";
$files_to_test = [
    'config/config.php' => 'Database config',
    'src/layout/layout.php' => 'Layout file',
    'src/components/navbar.php' => 'Navbar component',
    'vendor/autoload.php' => 'Composer autoload'
];

foreach ($files_to_test as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $description exists ($file)</p>";
    } else {
        echo "<p style='color: red;'>✗ $description missing ($file)</p>";
    }
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='home.php'>Test Home Page</a></li>";
echo "<li><a href='test_connection.php'>Test Database Connection</a></li>";
echo "<li><a href='check_php_version.php'>Check PHP Version</a></li>";
echo "</ul>";
