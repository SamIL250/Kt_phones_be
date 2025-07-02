-- Fix MySQL Authentication Issue
-- Run this script in MySQL to change authentication method

-- Change root user authentication method
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';

-- If you have a specific user for the application, use this instead:
-- ALTER USER 'your_username'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';

-- Flush privileges to apply changes
FLUSH PRIVILEGES; 