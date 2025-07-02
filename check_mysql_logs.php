<?php

/**
 * MySQL Error Log Checker
 * This script helps diagnose MySQL startup issues
 */

echo "<h2>MySQL Error Log Checker</h2>";

// Common MySQL log file locations in XAMPP
$log_paths = [
    'C:/xampp/mysql/data/mysql_error.log',
    'C:/xampp/mysql/data/mysql.err',
    'C:/xampp/mysql/data/mysqld.log',
    'C:/xampp/mysql/data/mysql-bin.err'
];

echo "<h3>Checking MySQL Log Files:</h3>";

$found_logs = false;
foreach ($log_paths as $log_path) {
    if (file_exists($log_path)) {
        $found_logs = true;
        echo "<h4>Log File: $log_path</h4>";
        echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;'>";

        $lines = file($log_path);
        if ($lines) {
            // Show last 50 lines
            $recent_lines = array_slice($lines, -50);
            foreach ($recent_lines as $line) {
                echo htmlspecialchars($line) . "<br>";
            }
        } else {
            echo "Could not read log file.";
        }
        echo "</div><br>";
    }
}

if (!$found_logs) {
    echo "<p style='color: red;'>No MySQL log files found in common locations.</p>";
}

// Check if MySQL data directory exists
echo "<h3>MySQL Data Directory Check:</h3>";
$data_dir = 'C:/xampp/mysql/data';
if (is_dir($data_dir)) {
    echo "<p style='color: green;'>✓ MySQL data directory exists: $data_dir</p>";

    // Check for important files
    $important_files = ['ibdata1', 'ib_logfile0', 'ib_logfile1', 'mysql'];
    foreach ($important_files as $file) {
        $file_path = $data_dir . '/' . $file;
        if (file_exists($file_path) || is_dir($file_path)) {
            echo "<p style='color: green;'>✓ $file exists</p>";
        } else {
            echo "<p style='color: red;'>✗ $file missing</p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ MySQL data directory not found: $data_dir</p>";
}

// Check if port 3306 is in use
echo "<h3>Port 3306 Check:</h3>";
$connection = @fsockopen('localhost', 3306, $errno, $errstr, 5);
if ($connection) {
    echo "<p style='color: orange;'>⚠ Port 3306 is already in use by another process</p>";
    fclose($connection);
} else {
    echo "<p style='color: green;'>✓ Port 3306 is available</p>";
}

echo "<hr>";
echo "<h3>Common Solutions:</h3>";
echo "<ol>";
echo "<li><strong>Delete ib_logfile files:</strong> Delete ib_logfile0 and ib_logfile1 from C:/xampp/mysql/data/</li>";
echo "<li><strong>Check Windows Services:</strong> Make sure no other MySQL service is running</li>";
echo "<li><strong>Run as Administrator:</strong> Try running XAMPP as administrator</li>";
echo "<li><strong>Reset MySQL:</strong> Backup and reinstall MySQL if needed</li>";
echo "</ol>";

echo "<p><a href='fix_mysql_startup.php'>→ Try Automatic Fix</a></p>";
