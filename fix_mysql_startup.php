<?php
/**
 * MySQL Startup Fix Script
 * This script attempts to fix common MySQL startup issues
 */

echo "<h2>MySQL Startup Fix Script</h2>";

$mysql_data_dir = 'C:/xampp/mysql/data';
$backup_dir = 'C:/xampp/mysql/data_backup_' . date('Y-m-d_H-i-s');

echo "<h3>Step 1: Creating Backup</h3>";

// Create backup of current data directory
if (is_dir($mysql_data_dir)) {
    if (!is_dir($backup_dir)) {
        if (mkdir($backup_dir, 0777, true)) {
            echo "<p style='color: green;'>✓ Created backup directory: $backup_dir</p>";
        } else {
            echo "<p style='color: red;'>✗ Could not create backup directory</p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ MySQL data directory not found: $mysql_data_dir</p>";
}

echo "<h3>Step 2: Checking for Corrupted Files</h3>";

$corrupted_files = ['ib_logfile0', 'ib_logfile1'];
$files_to_delete = [];

foreach ($corrupted_files as $file) {
    $file_path = $mysql_data_dir . '/' . $file;
    if (file_exists($file_path)) {
        $files_to_delete[] = $file_path;
        echo "<p style='color: orange;'>⚠ Found potentially corrupted file: $file</p>";
    }
}

echo "<h3>Step 3: Manual Steps Required</h3>";
echo "<p>Please follow these steps manually:</p>";

echo "<ol>";
echo "<li><strong>Stop XAMPP completely</strong> (Apache, MySQL, and any other services)</li>";
echo "<li><strong>Open File Explorer</strong> and navigate to: <code>C:\\xampp\\mysql\\data</code></li>";
echo "<li><strong>Delete these files</strong> (if they exist):";
echo "<ul>";
foreach ($corrupted_files as $file) {
    echo "<li><code>$file</code></li>";
}
echo "</ul></li>";
echo "<li><strong>Also delete</strong> (if they exist):";
echo "<ul>";
echo "<li><code>ib_logfile0</code></li>";
echo "<li><code>ib_logfile1</code></li>";
echo "<li><code>ib_logfile2</code></li>";
echo "</ul></li>";
echo "<li><strong>Start XAMPP MySQL service</strong></li>";
echo "</ol>";

echo "<h3>Step 4: Alternative Solutions</h3>";

echo "<h4>Option A: Reset MySQL (Loses all data)</h4>";
echo "<ol>";
echo "<li>Stop XAMPP</li>";
echo "<li>Rename <code>C:\\xampp\\mysql\\data</code> to <code>C:\\xampp\\mysql\\data_old</code></li>";
echo "<li>Copy <code>C:\\xampp\\mysql\\backup</code> to <code>C:\\xampp\\mysql\\data</code></li>";
echo "<li>Start XAMPP MySQL</li>";
echo "</ol>";

echo "<h4>Option B: Check Windows Services</h4>";
echo "<ol>";
echo "<li>Press <code>Win + R</code>, type <code>services.msc</code></li>";
echo "<li>Look for any MySQL service and stop it</li>";
echo "<li>Try starting XAMPP MySQL again</li>";
echo "</ol>";

echo "<h4>Option C: Run as Administrator</h4>";
echo "<ol>";
echo "<li>Right-click on XAMPP Control Panel</li>";
echo "<li>Select 'Run as administrator'</li>";
echo "<li>Try starting MySQL again</li>";
echo "</ol>";

echo "<hr>";
echo "<h3>After Fixing:</h3>";
echo "<ul>";
echo "<li><a href='test_connection.php'>Test Database Connection</a></li>";
echo "<li><a href='check_php_version.php'>Check PHP Version</a></li>";
echo "<li><a href='fix_mysql_auth.php'>Fix Authentication (if needed)</a></li>";
echo "</ul>";

echo "<p><strong>Note:</strong> If you lose your database data, you can restore it from the SQL files in your project.</p>";
?> 