<?php
/**
 * Online/Production Configuration
 * Use this for live hosting environments
 */

// Production Environment Settings
define('DEVELOPMENT', false);
error_reporting(0);
ini_set('display_errors', 0);

// Site Configuration for Production
define('SITE_NAME', 'Fresh Grocery BD');
define('SITE_URL', 'https://your-username.github.io/grocery-shop-bd/'); // Update with your GitHub Pages URL
define('SITE_EMAIL', 'info@freshgrocerybd.com');
define('SITE_PHONE', '+8801700000000');

// Database Configuration for Production
// For GitHub Pages (static hosting), database won't work
// For full hosting, update these with your hosting provider's details
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'grocery_shop_bd');
define('DB_USER', $_ENV['DB_USER'] ?? 'username');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'password');

// Security Configuration
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'your-production-encryption-key-32-chars');
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'your-production-jwt-secret-key');

// File Upload Configuration
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Production Payment Gateway Configuration
define('BKASH_APP_KEY', $_ENV['BKASH_APP_KEY'] ?? '');
define('BKASH_APP_SECRET', $_ENV['BKASH_APP_SECRET'] ?? '');
define('NAGAD_MERCHANT_ID', $_ENV['NAGAD_MERCHANT_ID'] ?? '');
define('NAGAD_MERCHANT_KEY', $_ENV['NAGAD_MERCHANT_KEY'] ?? '');

// Email Configuration for Production
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? 'your-email@gmail.com');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? 'your-app-password');

// Time Zone
date_default_timezone_set('Asia/Dhaka');

// For GitHub Pages (static demo), we'll use mock data instead of database
if (strpos($_SERVER['HTTP_HOST'], 'github.io') !== false) {
    define('DEMO_MODE', true);
    define('USE_DATABASE', false);
} else {
    define('DEMO_MODE', false);
    define('USE_DATABASE', true);
}

// Mock data for demo mode
if (defined('DEMO_MODE') && DEMO_MODE) {
    // Simulate database connection
    $db = null;
    
    // Mock functions for demo
    function isLoggedIn() { return false; }
    function isAdmin() { return false; }
    function getCartCount($userId) { return 3; }
    function formatCurrency($amount) { return '৳ ' . number_format($amount, 2); }
    function getSiteSetting($key, $default = '') { return $default; }
}
?>
