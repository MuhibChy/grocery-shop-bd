<?php
/**
 * Main Configuration File
 * Professional Grocery Shop Management System
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Site Configuration
define('SITE_NAME', 'Fresh Grocery BD');
define('SITE_URL', 'http://localhost/Grocery%20shop/');
define('SITE_EMAIL', 'info@freshgrocerybd.com');
define('SITE_PHONE', '+8801700000000');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'grocery_shop_bd');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security Configuration
define('ENCRYPTION_KEY', 'your-secret-encryption-key-here');
define('JWT_SECRET', 'your-jwt-secret-key-here');

// File Upload Configuration
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Currency
define('CURRENCY', 'BDT');
define('CURRENCY_SYMBOL', '৳');

// Payment Gateway Configuration
define('BKASH_APP_KEY', 'your-bkash-app-key');
define('BKASH_APP_SECRET', 'your-bkash-app-secret');
define('NAGAD_MERCHANT_ID', 'your-nagad-merchant-id');
define('NAGAD_MERCHANT_KEY', 'your-nagad-merchant-key');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// Time Zone
date_default_timezone_set('Asia/Dhaka');

// Define base path
define('BASE_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', __DIR__ . '/');
define('INCLUDES_PATH', BASE_PATH . 'includes/');

// Include required files
require_once CONFIG_PATH . 'database.php';

// Check if includes files exist before requiring them
if (file_exists(INCLUDES_PATH . 'functions.php')) {
    require_once INCLUDES_PATH . 'functions.php';
}

if (file_exists(INCLUDES_PATH . 'security.php')) {
    require_once INCLUDES_PATH . 'security.php';
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    // If database connection fails, set $db to null
    $db = null;
    // Only show error in development
    if (defined('DEVELOPMENT') && DEVELOPMENT) {
        echo "Database connection failed: " . $e->getMessage();
    }
}

// Language settings
$current_language = $_SESSION['language'] ?? 'en';
define('CURRENT_LANGUAGE', $current_language);

// Site settings from database
function getSiteSetting($key, $default = '') {
    global $db;
    try {
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}
?>
