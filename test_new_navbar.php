<?php

/**
 * Test New Navbar
 * This will test if the new navbar file works correctly
 */

// Include necessary files
include __DIR__ . '/config/config.php';
include __DIR__ . '/src/services/auth/auth_state.php';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>New Navbar Test</title>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "</head>";
echo "<body>";

echo "<h1>New Navbar Test</h1>";

// Include the new navbar
echo "<h2>Navbar Below:</h2>";
echo "<div style='border: 2px solid green; padding: 10px; margin: 10px;'>";
include __DIR__ . '/src/components/navbar.php';
echo "</div>";

echo "<h2>Test Complete</h2>";
echo "<p>If you see a proper navigation bar above, then the navbar is working correctly.</p>";

echo "</body>";
echo "</html>";
