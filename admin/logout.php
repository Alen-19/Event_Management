<?php
/**
 * Administrator Logout
 * Event Management System
 */

require_once __DIR__ . '/../includes/auth.php';

// Unset session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start new session just to flash the logout notice
session_start();
setFlashMessage('success', 'You have been logged out successfully.');

header("Location: login.php");
exit;
