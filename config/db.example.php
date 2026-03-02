<?php
// ============================================
// Velvet Vogue - Database Configuration
// ============================================
// Copy this file to db.php and update with your credentials.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');            // Your MySQL password
define('DB_NAME', 'velvet_vogue');

// Site Configuration
define('SITE_NAME', 'Velvet Vogue');
define('SITE_URL', 'http://localhost/Velvet%20Vogue%20Online%20Fashion%20Store%20Website');
define('SITE_EMAIL', 'info@velvetvogue.com');
define('CURRENCY', 'PKR');
define('CURRENCY_SYMBOL', 'Rs.');

// Create Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
