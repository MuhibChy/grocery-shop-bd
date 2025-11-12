<?php
/**
 * Security Functions
 * Professional Grocery Shop Management System
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if user is customer
 */
function isCustomer() {
    return isLoggedIn() && $_SESSION['user_role'] === 'customer';
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'auth/login.php');
        exit();
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . 'index.php');
        exit();
    }
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    return $errors;
}

/**
 * Rate limiting
 */
function checkRateLimit($action, $limit = 5, $window = 300) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = $action . '_' . $ip;
    
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $now = time();
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    $data = $_SESSION['rate_limit'][$key];
    
    if ($now - $data['start'] > $window) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    if ($data['count'] >= $limit) {
        return false;
    }
    
    $_SESSION['rate_limit'][$key]['count']++;
    return true;
}

/**
 * Validate phone number (Bangladesh)
 */
function validateBDPhone($phone) {
    // Remove spaces and special characters
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Check for valid BD phone patterns
    $patterns = [
        '/^(\+88)?01[3-9]\d{8}$/',  // Mobile numbers
        '/^(\+88)?02\d{7,8}$/',     // Dhaka landline
        '/^(\+88)?0[3-9]\d{7,9}$/'  // Other area codes
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $phone)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Sanitize file name
 */
function sanitizeFileName($filename) {
    // Remove path information and dots around the filename
    $filename = basename($filename);
    
    // Replace spaces with underscores
    $filename = str_replace(' ', '_', $filename);
    
    // Remove any character that isn't alphanumeric, underscore, dash, or dot
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    return $filename;
}

/**
 * Validate image file
 */
function validateImageFile($file) {
    $errors = [];
    
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $errors[] = 'No file uploaded';
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size too large. Maximum ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB allowed';
    }
    
    // Check file type
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, ALLOWED_IMAGE_TYPES)) {
        $errors[] = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_IMAGE_TYPES);
    }
    
    // Check if it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        $errors[] = 'File is not a valid image';
    }
    
    return $errors;
}

/**
 * Clean input for database
 */
function cleanInput($input) {
    if (is_array($input)) {
        return array_map('cleanInput', $input);
    }
    
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate required fields
 */
function validateRequired($fields, $data) {
    $errors = [];
    
    foreach ($fields as $field => $label) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = $label . ' is required';
        }
    }
    
    return $errors;
}

/**
 * Generate secure random token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Encrypt data
 */
function encryptData($data) {
    $key = ENCRYPTION_KEY;
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt data
 */
function decryptData($encryptedData) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

/**
 * Log security event
 */
function logSecurityEvent($event, $details = '', $severity = 'info') {
    $logFile = '../logs/security_' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $userId = $_SESSION['user_id'] ?? 'guest';
    
    $logEntry = "[{$timestamp}] [{$severity}] Event: {$event} | User: {$userId} | IP: {$ip} | Details: {$details} | User-Agent: {$userAgent}" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Check for suspicious activity
 */
function checkSuspiciousActivity() {
    // Check for common attack patterns
    $suspicious = false;
    $reason = '';
    
    // Check for SQL injection attempts
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    
    $sqlPatterns = [
        '/union.*select/i',
        '/select.*from/i',
        '/insert.*into/i',
        '/delete.*from/i',
        '/drop.*table/i',
        '/exec.*xp_/i'
    ];
    
    foreach ($sqlPatterns as $pattern) {
        if (preg_match($pattern, $queryString . $requestUri)) {
            $suspicious = true;
            $reason = 'SQL injection attempt detected';
            break;
        }
    }
    
    // Check for XSS attempts
    $xssPatterns = [
        '/<script/i',
        '/javascript:/i',
        '/onload=/i',
        '/onerror=/i'
    ];
    
    foreach ($xssPatterns as $pattern) {
        if (preg_match($pattern, $queryString . $requestUri)) {
            $suspicious = true;
            $reason = 'XSS attempt detected';
            break;
        }
    }
    
    if ($suspicious) {
        logSecurityEvent('suspicious_activity', $reason, 'warning');
        // You might want to block the IP or take other actions here
    }
    
    return $suspicious;
}

// Run security check on every request
checkSuspiciousActivity();
?>
