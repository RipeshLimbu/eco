<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'waste_management');
define('DB_PORT', 3306);

// Application Configuration
define('BASE_URL', 'http://localhost/eco');  // Update this according to your setup

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time Zone
date_default_timezone_set('Asia/Kathmandu');  // Update this according to your timezone

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
