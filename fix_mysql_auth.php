<?php

/**
 * MySQL Authentication Fix Script
 * This script will fix the caching_sha2_password authentication issue
 */

echo "<h2>MySQL Authentication Fix Script</h2>";

// Try to connect using the old method first
$conn = mysqli_connect('localhost', 'root', '', 'kt_phones');

if ($conn) {
    echo "<p style='color: green;'>✓ Successfully connected to MySQL!</p>";

    // Try to fix the authentication method
    $fix_query = "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''";
    $result = mysqli_query($conn, $fix_query);

    if ($result) {
        echo "<p style='color: green;'>✓ Successfully updated authentication method!</p>";

        // Flush privileges
        $flush_query = "FLUSH PRIVILEGES";
        $flush_result = mysqli_query($conn, $flush_query);

        if ($flush_result) {
            echo "<p style='color: green;'>✓ Privileges flushed successfully!</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Could not flush privileges: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Could not update authentication method: " . mysqli_error($conn) . "</p>";
    }

    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>✗ Could not connect to MySQL: " . mysqli_connect_error() . "</p>";

    // Alternative approach - try to connect without database first
    echo "<h3>Trying alternative connection method...</h3>";

    $conn_no_db = mysqli_connect('localhost', 'root', '');

    if ($conn_no_db) {
        echo "<p style='color: green;'>✓ Connected to MySQL server (without database)</p>";

        // Try to fix authentication
        $fix_query = "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''";
        $result = mysqli_query($conn_no_db, $fix_query);

        if ($result) {
            echo "<p style='color: green;'>✓ Successfully updated authentication method!</p>";

            // Flush privileges
            $flush_query = "FLUSH PRIVILEGES";
            $flush_result = mysqli_query($conn_no_db, $flush_query);

            if ($flush_result) {
                echo "<p style='color: green;'>✓ Privileges flushed successfully!</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Could not update authentication method: " . mysqli_error($conn_no_db) . "</p>";
        }

        mysqli_close($conn_no_db);
    } else {
        echo "<p style='color: red;'>✗ Could not connect to MySQL server at all</p>";
    }
}

echo "<h3>Manual Steps Required:</h3>";
echo "<ol>";
echo "<li>Open phpMyAdmin (usually at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>)</li>";
echo "<li>Go to the SQL tab</li>";
echo "<li>Run this command: <code>ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';</code></li>";
echo "<li>Then run: <code>FLUSH PRIVILEGES;</code></li>";
echo "<li>Restart XAMPP MySQL service</li>";
echo "</ol>";

echo "<h3>Alternative Solution:</h3>";
echo "<p>If the above doesn't work, try creating a new user:</p>";
echo "<pre>";
echo "CREATE USER 'ktphones_user'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password123';";
echo "GRANT ALL PRIVILEGES ON kt_phones.* TO 'ktphones_user'@'localhost';";
echo "FLUSH PRIVILEGES;";
echo "</pre>";
echo "<p>Then update your config files to use 'ktphones_user' instead of 'root'</p>";

echo "<h3>Test Connection:</h3>";
echo "<p><a href='test_connection.php'>Click here to test the connection after making changes</a></p>";
