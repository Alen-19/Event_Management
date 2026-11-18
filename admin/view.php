<?php
/**
 * Administrator View Registration Details
 * Event Management System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Auth Guard
requireAdminAuth();

$pdo = getActiveDB();
$pageTitle = 'View Registration Details';

$regId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$registration = null;

if ($regId > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                r.id AS reg_id,
                r.user_name,
                r.user_email,
                r.created_at AS reg_date,
                e.id AS event_id,
                e.title AS event_title,
                e.description AS event_description,
                e.event_date,
                e.location AS event_location
            FROM registrations r
            INNER JOIN events e ON r.event_id = e.id
            WHERE r.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $regId]);
        $registration = $stmt->fetch();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}

$csrfToken = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - LevelX Events</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-light">
    <!-- Admin Top Navbar -->
    <header class="site-header">
        <div class="container">
            <nav class="navbar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="index.php" class="brand-logo">
                        <div class="brand-icon">LX</div>
                        <span>LevelX Events</span>
                    </a>
                    <span class="admin-badge">Admin Portal</span>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <a href="index.php" class="nav-link">
                        &larr; Back to Dashboard
                    </a>
                    <div style="font-size: 0.9rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                        <span>👤</span>
                        <strong><?= htmlspecialchars(getAdminUsername()) ?></strong>
                    </div>
                    <a href="logout.php" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to log out?');">
                        Sign Out
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if ($registration): ?>
                <div class="ticket-card" style="box-shadow: var(--shadow-md);">
                    <div class="ticket-header" style="background: linear-gradient(135deg, #1e293b, #334155);">
                        <div class="ticket-icon">📋</div>
                        <h1>Registration Details</h1>
                        <p>Detailed overview of attendee record #LX-<?= str_pad((string)$registration['reg_id'], 5, '0', STR_PAD_LEFT) ?></p>
                    </div>

                    <div class="ticket-body">
                        <div class="ticket-grid">
                            <div class="ticket-item">
                                <span class="ticket-label">Registration ID</span>
                                <span class="ticket-value">#<?= (int)$registration['reg_id'] ?></span>
                            </div>
                            <div class="ticket-item">
                                <span class="ticket-label">Date & Time Registered</span>
                                <span class="ticket-value"><?= date('M d, Y • h:i A', strtotime($registration['reg_date'])) ?></span>
                            </div>
                        </div>

                        <hr class="ticket-divider">

                        <div class="ticket-grid">
                            <div class="ticket-item">
                                <span class="ticket-label">Attendee Name</span>
                                <span class="ticket-value"><?= htmlspecialchars($registration['user_name']) ?></span>
                            </div>
                            <div class="ticket-item">
                                <span class="ticket-label">Attendee Email</span>
                                <span class="ticket-value">
                                    <a href="mailto:<?= htmlspecialchars($registration['user_email']) ?>" style="color: var(--primary); text-decoration: none;">
                                        <?= htmlspecialchars($registration['user_email']) ?>
                                    </a>
                                </span>
                            </div>
                        </div>

                        <hr class="ticket-divider">

                        <div style="margin-bottom: 20px;">
                            <span class="ticket-label">Registered Event</span>
                            <h3 style="color: var(--primary); font-size: 1.35rem; margin: 4px 0 10px 0;">
                                <?= htmlspecialchars($registration['event_title']) ?>
                            </h3>
                        </div>

                        <div class="ticket-grid">
                            <div class="ticket-item">
                                <span class="ticket-label">Event Date & Time</span>
                                <span class="ticket-value">
                                    📅 <?= date('l, F j, Y', strtotime($registration['event_date'])) ?><br>
                                    ⏰ <?= date('h:i A', strtotime($registration['event_date'])) ?>
                                </span>
                            </div>
                            <div class="ticket-item">
                                <span class="ticket-label">Location / Venue</span>
                                <span class="ticket-value">
                                    📍 <?= htmlspecialchars($registration['event_location']) ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($registration['event_description'])): ?>
                            <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid var(--border-color); margin-top: 15px;">
                                <span class="ticket-label">Event Description</span>
                                <p style="font-size: 0.9rem; color: #475569; margin-top: 4px; line-height: 1.5;">
                                    <?= nl2br(htmlspecialchars($registration['event_description'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="ticket-actions" style="margin-top: 30px;">
                            <a href="index.php" class="btn btn-secondary" style="flex: 1;">
                                &larr; Back to Registrations Table
                            </a>
                            <form method="POST" action="delete.php" style="flex: 1;" onsubmit="return confirm('Are you sure you want to permanently delete this registration?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="id" value="<?= (int)$registration['reg_id'] ?>">
                                <button type="submit" class="btn btn-danger btn-block">
                                    🗑️ Delete Registration
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="form-card" style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 40px; margin-bottom: 12px;">⚠️</div>
                    <h2>Registration Not Found</h2>
                    <p style="color: var(--text-muted); margin: 10px 0 20px 0;">
                        The registration you are trying to view does not exist or was deleted.
                    </p>
                    <a href="index.php" class="btn btn-primary">&larr; Return to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> LevelX Event Management &bull; Admin Portal</p>
        </div>
    </footer>
</body>
</html>
