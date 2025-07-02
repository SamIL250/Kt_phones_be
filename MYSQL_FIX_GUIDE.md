# MySQL Authentication Fix Guide

## Problem

You're experiencing MySQL authentication errors:

```
Warning: mysqli_connect(): The server requested authentication method unknown to the client [caching_sha2_password]
```

## Root Cause

Your MySQL server is using the newer `caching_sha2_password` authentication method, but your PHP MySQL client doesn't support it.

## Solutions

### Solution 1: Fix MySQL User Authentication (Recommended)

1. **Open MySQL Command Line or phpMyAdmin**

2. **Run the following SQL commands:**

```sql
-- Change root user authentication method
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
```

3. **Alternative: Create a new user with proper authentication**

```sql
-- Create a new user for your application
CREATE USER 'ktphones_user'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON kt_phones.* TO 'ktphones_user'@'localhost';
FLUSH PRIVILEGES;
```

### Solution 2: Update PHP Configuration

If Solution 1 doesn't work, you can also try:

1. **Update your config files** (already done)
2. **Check PHP MySQL extension**
3. **Update XAMPP** to the latest version

### Solution 3: Alternative Connection Method

If you continue having issues, you can use PDO instead of mysqli:

```php
<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kt_phones;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## Steps to Apply the Fix

1. **Run the SQL commands** in Solution 1
2. **Restart your XAMPP Apache and MySQL services**
3. **Test your application**

## Verification

After applying the fix, your application should:

- Connect to the database without authentication errors
- Display categories and other database content properly
- Show no mysqli warnings

## Additional Notes

- The updated config files now include better error handling
- The search.php file has been fixed to properly include the database connection
- All database queries now check for connection status before executing

## If Problems Persist

1. Check XAMPP error logs
2. Verify MySQL is running on port 3306
3. Ensure the `kt_phones` database exists
4. Try creating a new database user with explicit privileges
