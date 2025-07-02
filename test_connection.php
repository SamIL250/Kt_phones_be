<?php

/**
 * Test MySQL Connection Script
 * This script tests if the MySQL connection is working properly
 */

echo "<h2>MySQL Connection Test</h2>";

// Test the current config
echo "<h3>Testing Current Configuration:</h3>";

try {
    // Include the config file
    require_once 'config/config.php';

    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful!</p>";

        // Test a simple query
        $test_query = "SELECT 1 as test";
        $result = mysqli_query($conn, $test_query);

        if ($result) {
            echo "<p style='color: green;'>✓ Query execution successful!</p>";

            // Test if kt_phones database exists
            $db_query = "SHOW DATABASES LIKE 'kt_phones'";
            $db_result = mysqli_query($conn, $db_query);

            if (mysqli_num_rows($db_result) > 0) {
                echo "<p style='color: green;'>✓ kt_phones database exists!</p>";

                // Test categories table
                $table_query = "SHOW TABLES LIKE 'categories'";
                $table_result = mysqli_query($conn, $table_query);

                if (mysqli_num_rows($table_result) > 0) {
                    echo "<p style='color: green;'>✓ categories table exists!</p>";

                    // Test categories query
                    $categories_query = "SELECT COUNT(*) as count FROM categories";
                    $categories_result = mysqli_query($conn, $categories_query);

                    if ($categories_result) {
                        $row = mysqli_fetch_assoc($categories_result);
                        echo "<p style='color: green;'>✓ Categories query successful! Found " . $row['count'] . " categories.</p>";
                    } else {
                        echo "<p style='color: red;'>✗ Categories query failed: " . mysqli_error($conn) . "</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠ categories table does not exist</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ kt_phones database does not exist</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Query execution failed: " . mysqli_error($conn) . "</p>";
        }

        mysqli_close($conn);
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test alternative connection method
echo "<h3>Testing Alternative Connection Method:</h3>";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kt_phones';

$conn_alt = mysqli_connect($host, $username, $password, $database);

if ($conn_alt) {
    echo "<p style='color: green;'>✓ Alternative connection successful!</p>";
    mysqli_close($conn_alt);
} else {
    echo "<p style='color: red;'>✗ Alternative connection failed: " . mysqli_connect_error() . "</p>";
}

echo "<hr>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>If all tests pass, your application should work properly</li>";
echo "<li>If tests fail, run the <a href='fix_mysql_auth.php'>fix script</a> again</li>";
echo "<li>Make sure to restart XAMPP MySQL service after making changes</li>";
echo "</ul>";

echo "<p><a href='index.php'>← Back to Application</a></p>";
