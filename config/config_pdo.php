<?php
// Alternative PDO Database Configuration
// Use this if mysqli continues to have authentication issues

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kt_phones_v2';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);

    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // For backward compatibility, create mysqli connection object
    $conn = $pdo;

    echo "<!-- PDO connection established successfully -->";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper function to convert PDO to mysqli-like functions
function mysqli_query($pdo, $query)
{
    try {
        return $pdo->query($query);
    } catch (PDOException $e) {
        return false;
    }
}

function mysqli_fetch_assoc($result)
{
    if ($result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

function mysqli_num_rows($result)
{
    if ($result instanceof PDOStatement) {
        return $result->rowCount();
    }
    return 0;
}

function mysqli_error($pdo)
{
    if ($pdo instanceof PDO) {
        $error = $pdo->errorInfo();
        return $error[2] ?? 'Unknown error';
    }
    return 'Invalid connection object';
}

function mysqli_close($pdo)
{
    // PDO connections are automatically closed when the script ends
    // This function is for compatibility only
    return true;
}
