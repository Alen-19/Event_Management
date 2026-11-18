<?php
/**
 * Shared Public Header Component
 * Event Management System
 */

$pageTitle = $pageTitle ?? 'Event Management System';
$activePage = $activePage ?? '';
$basePath = $basePath ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - LevelX Events</title>
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
    <?php if (isset($extraCss)): ?>
        <link rel="stylesheet" href="<?= $basePath . $extraCss ?>">
    <?php endif; ?>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <nav class="navbar">
                <a href="<?= $basePath ?>index.php" class="brand-logo">
                    <div class="brand-icon">LX</div>
                    <span>LevelX Events</span>
                </a>
                <ul class="nav-links">
                    <li>
                        <a href="<?= $basePath ?>index.php" class="nav-link <?= ($activePage === 'home') ? 'active' : '' ?>">
                            Upcoming Events
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>register.php" class="nav-link <?= ($activePage === 'register') ? 'active' : '' ?>">
                            Register Now
                        </a>
                    </li>
                    <li>
                        <a href="<?= $basePath ?>admin/index.php" class="btn btn-secondary btn-sm">
                            <span>🔐</span> Admin Portal
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
