<?php
/**
 * Authentication and Security Helper
 * Event Management System
 */

if (session_status() === PHP_SESSION_NONE) {
    // Basic session cookie security settings
    session_start();
}

/**
 * Check if an administrator is currently logged in
 *
 * @return bool
 */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require administrator authentication for protected routes
 */
function requireAdminAuth(): void {
    if (!isAdminLoggedIn()) {
        $loginUrl = 'login.php';
        header("Location: {$loginUrl}");
        exit;
    }
}

/**
 * Get current logged in admin username
 *
 * @return string
 */
function getAdminUsername(): string {
    return $_SESSION['admin_username'] ?? 'Admin';
}

/**
 * Generate or retrieve CSRF token for the session
 *
 * @return string
 */
function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from request
 *
 * @param string|null $token
 * @return bool
 */
function validateCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash message helper for session messages
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlashMessage(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
