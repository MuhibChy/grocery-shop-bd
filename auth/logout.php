<?php
require_once '../config/config.php';

// Check if user is logged in
if (isLoggedIn()) {
    // Log the logout activity
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
    
    // Clear remember me cookie if it exists
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        // Remove token from database (you'd need to implement this)
    }
    
    // Destroy session
    session_destroy();
    
    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirect to login page
header('Location: login.php?message=logged_out');
exit();
?>
