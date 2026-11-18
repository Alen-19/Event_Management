<?php
/**
 * Administrator Login
 * Event Management System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header("Location: index.php");
    exit;
}

$pdo = getActiveDB();
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($submittedToken)) {
        $error = "Session expired or invalid security token. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Please enter both username and password.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Password matches -> regenerate session ID for security
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];

                    setFlashMessage('success', "Welcome back, " . htmlspecialchars($admin['username']) . "!");
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            } catch (PDOException $e) {
                $error = "Database authentication error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LevelX Events</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .login-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 440px;
            padding: 36px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-logo .icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-md);
            color: #ffffff;
            font-size: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .login-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
        }
        .login-logo p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }
        .credentials-hint {
            background: #f1f5f9;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-top: 24px;
            font-size: 0.85rem;
            color: #475569;
            border: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <div class="icon">🔐</div>
                <h1>Admin Portal Login</h1>
                <p>Sign in to manage attendee registrations</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php 
                $flash = getFlashMessage();
                if ($flash): 
            ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom: 20px;">
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Enter admin username" 
                        value="<?= htmlspecialchars($username) ?>"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter password" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                    Sign In to Dashboard &rarr;
                </button>
            </form>

            <div class="credentials-hint">
                <strong>Default Credentials:</strong><br>
                Username: <code>admin</code> &bull; Password: <code>admin123</code>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="../index.php" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">
                    &larr; Back to Public Events Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>
