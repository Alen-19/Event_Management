<?php
/**
 * Registration Success & Summary Page
 * Event Management System
 */

require_once __DIR__ . '/config/db.php';

$pdo = getActiveDB();
$pageTitle = 'Registration Confirmed';
$activePage = 'register';
$basePath = '';

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

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <?php if ($registration): ?>
        <div class="ticket-card">
            <!-- Ticket Header -->
            <div class="ticket-header">
                <div class="ticket-icon">✓</div>
                <h1>Registration Confirmed!</h1>
                <p>Thank you, <strong><?= htmlspecialchars($registration['user_name']) ?></strong>. Your registration has been recorded.</p>
            </div>

            <!-- Ticket Body -->
            <div class="ticket-body">
                <div class="ticket-grid">
                    <div class="ticket-item">
                        <span class="ticket-label">Registration ID</span>
                        <span class="ticket-value">#LX-<?= str_pad((string)$registration['reg_id'], 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="ticket-item">
                        <span class="ticket-label">Registration Date</span>
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
                        <span class="ticket-value"><?= htmlspecialchars($registration['user_email']) ?></span>
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
                        <span class="ticket-label">Event Details</span>
                        <p style="font-size: 0.9rem; color: #475569; margin-top: 4px; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($registration['event_description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="ticket-actions">
                    <button onclick="window.print()" class="btn btn-secondary" style="flex: 1;">
                        🖨️ Print Ticket / Summary
                    </button>
                    <a href="index.php" class="btn btn-primary" style="flex: 1;">
                        Browse More Events &rarr;
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="form-card" style="text-align: center; padding: 40px 20px;">
            <div style="font-size: 40px; margin-bottom: 12px;">⚠️</div>
            <h2>Registration Not Found</h2>
            <p style="color: var(--text-muted); margin: 10px 0 20px 0;">
                The registration summary you requested could not be located or invalid ID was provided.
            </p>
            <a href="index.php" class="btn btn-primary">Return to Homepage &rarr;</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
