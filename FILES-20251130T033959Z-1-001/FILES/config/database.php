<?php
/**
 * Database Configuration
 * LMS System
 */

// Database credentials
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'bigzdemo_elern');
define('DB_PASS', 'o54$J14sj');
define('DB_NAME', 'bigzdemo_elern');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Error reporting for development (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>