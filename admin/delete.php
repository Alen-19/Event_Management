<?php
/**
 * Administrator Delete Registration Action
 * Event Management System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Auth Guard
requireAdminAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($submittedToken)) {
        setFlashMessage('danger', 'Security validation failed (Invalid CSRF token). Please try again.');
        header("Location: index.php");
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        setFlashMessage('danger', 'Invalid registration ID specified for deletion.');
        header("Location: index.php");
        exit;
    }

    $pdo = getActiveDB();

    try {
        // Retrieve registration info before deletion to show friendly name in flash message
        $infoStmt = $pdo->prepare("SELECT user_name FROM registrations WHERE id = :id LIMIT 1");
        $infoStmt->execute([':id' => $id]);
        $reg = $infoStmt->fetch();

        if ($reg) {
            $deleteStmt = $pdo->prepare("DELETE FROM registrations WHERE id = :id");
            $deleteStmt->execute([':id' => $id]);

            setFlashMessage('success', "Registration #{$id} for \"" . htmlspecialchars($reg['user_name']) . "\" has been permanently deleted.");
        } else {
            setFlashMessage('warning', "Registration #{$id} could not be found or was already deleted.");
        }
    } catch (PDOException $e) {
        setFlashMessage('danger', "Database error during deletion: " . $e->getMessage());
    }
} else {
    // If accessed directly via GET without POST
    setFlashMessage('warning', 'Direct deletion via GET is disallowed. Please use the dashboard actions.');
}

header("Location: index.php");
exit;
