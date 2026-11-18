<?php
/**
 * Automated Database Setup & Seed Runner
 * Event Management System
 */

require_once __DIR__ . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');
$messages = [];
$errors = [];

try {
    // 1. Connect to MySQL Server (without selecting specific DB)
    $serverPdo = getDBConnection(false);
    $messages[] = "Connected to MySQL server successfully.";

    // 2. Read schema file
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found at: {$schemaFile}");
    }
    $sqlContent = file_get_contents($schemaFile);

    // 3. Execute multi-query script
    $serverPdo->exec($sqlContent);
    $messages[] = "Database `event_management` created and configured.";
    $messages[] = "Tables `events`, `registrations`, and `admins` initialized.";
    $messages[] = "Sample upcoming events pre-seeded.";
    $messages[] = "Default administrator account created (Username: <strong>admin</strong> | Password: <strong>admin123</strong>).";

    $success = true;
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
    $success = false;
}

if ($isCli) {
    if ($success) {
        echo "=== Setup Successful! ===\n";
        foreach ($messages as $msg) {
            echo " - " . strip_tags($msg) . "\n";
        }
        echo "Admin Credentials: admin / admin123\n";
    } else {
        echo "=== Setup Failed ===\n";
        foreach ($errors as $err) {
            echo " [ERROR] {$err}\n";
        }
    }
    exit($success ? 0 : 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Event Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .setup-container {
            max-width: 650px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 35px;
            border: 1px solid #e2e8f0;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .setup-header h1 {
            font-size: 1.8rem;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-badge.success {
            background: #dcfce7;
            color: #15803d;
        }
        .status-badge.error {
            background: #fee2e2;
            color: #b91c1c;
        }
        .msg-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .msg-list li {
            padding: 10px 14px;
            margin-bottom: 8px;
            border-radius: 8px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .msg-list li.success-item {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        .msg-list li.error-item {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .info-card h4 {
            margin: 0 0 8px 0;
            color: #334155;
            font-size: 0.95rem;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="setup-container">
        <div class="setup-header">
            <div style="font-size: 40px; margin-bottom: 10px;">⚡</div>
            <h1>System Initialization</h1>
            <?php if ($success): ?>
                <span class="status-badge success">&#10003; Installation Complete</span>
            <?php else: ?>
                <span class="status-badge error">&#10007; Installation Failed</span>
            <?php endif; ?>
        </div>

        <ul class="msg-list">
            <?php foreach ($messages as $msg): ?>
                <li class="success-item">
                    <span>&#10003;</span>
                    <div><?= $msg ?></div>
                </li>
            <?php endforeach; ?>
            <?php foreach ($errors as $err): ?>
                <li class="error-item">
                    <span>&#9888;</span>
                    <div><?= htmlspecialchars($err) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($success): ?>
            <div class="info-card">
                <h4>Default Administrator Credentials:</h4>
                <p style="margin: 4px 0;"><strong>Username:</strong> <code>admin</code></p>
                <p style="margin: 4px 0;"><strong>Password:</strong> <code>admin123</code></p>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary" style="flex: 1; text-align: center;">View Events Homepage &rarr;</a>
                <a href="admin/login.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Go to Admin Login &rarr;</a>
            </div>
        <?php else: ?>
            <p style="color: #64748b; font-size: 0.9rem; text-align: center;">
                Please ensure MySQL is running in your XAMPP Control Panel, then refresh this page.
            </p>
            <div class="action-buttons">
                <a href="setup.php" class="btn btn-primary" style="flex: 1; text-align: center;">Retry Setup</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
