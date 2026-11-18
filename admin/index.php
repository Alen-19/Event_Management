<?php
/**
 * Administrator Dashboard - Registrations Management
 * Event Management System
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Auth Guard
requireAdminAuth();

$pdo = getActiveDB();
$pageTitle = 'Admin Dashboard - Registrations';

// Fetch all registrations joined with event details
try {
    $stmt = $pdo->query("
        SELECT 
            r.id,
            r.user_name,
            r.user_email,
            r.created_at AS reg_date,
            e.id AS event_id,
            e.title AS event_title,
            e.event_date,
            e.location AS event_location
        FROM registrations r
        INNER JOIN events e ON r.event_id = e.id
        ORDER BY r.id DESC
    ");
    $registrations = $stmt->fetchAll();

    // Stats
    $totalRegistrations = count($registrations);
    $eventsCount = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
} catch (PDOException $e) {
    $registrations = [];
    $totalRegistrations = 0;
    $eventsCount = 0;
    $dbError = $e->getMessage();
}

$flash = getFlashMessage();
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
                    <a href="../index.php" class="nav-link" target="_blank" title="Preview Public Site">
                        🌐 View Live Website &nearr;
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
            <!-- Flash Message Banner -->
            <?php if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom: 24px;">
                    <span><?= ($flash['type'] === 'success') ? '✓' : '⚠️' ?></span>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($dbError)): ?>
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <span>Database error: <?= htmlspecialchars($dbError) ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats KPI Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">📝</div>
                    <div class="stat-info">
                        <h3><?= $totalRegistrations ?></h3>
                        <p>Total Registrations</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">📅</div>
                    <div class="stat-info">
                        <h3><?= $eventsCount ?></h3>
                        <p>Active Events</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">🛡️</div>
                    <div class="stat-info">
                        <h3 style="font-size: 1.15rem; color: #334155; padding-top: 4px;">System Active</h3>
                        <p>Session Authenticated</p>
                    </div>
                </div>
            </div>

            <!-- Registrations Table Container -->
            <div class="table-card">
                <div class="table-toolbar">
                    <div>
                        <h2 class="table-title">Attendee Registrations</h2>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">
                            Showing all registered attendees across events
                        </span>
                    </div>

                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input 
                            type="text" 
                            id="tableSearch" 
                            class="search-input" 
                            placeholder="Search by name, email, event..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table" id="registrationsTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Event</th>
                                <th>Date Registered</th>
                                <th style="width: 150px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrations)): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">📭</div>
                                            <h3>No Registrations Found</h3>
                                            <p style="margin-top: 6px;">No users have registered for any event yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrations as $row): ?>
                                    <?php 
                                        $formattedDate = date('M d, Y h:i A', strtotime($row['reg_date']));
                                        $eventDateFormatted = date('M d, Y h:i A', strtotime($row['event_date']));
                                    ?>
                                    <tr 
                                        data-id="<?= (int)$row['id'] ?>"
                                        data-name="<?= htmlspecialchars($row['user_name'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($row['user_email'], ENT_QUOTES) ?>"
                                        data-event="<?= htmlspecialchars($row['event_title'], ENT_QUOTES) ?>"
                                        data-event-date="<?= htmlspecialchars($eventDateFormatted, ENT_QUOTES) ?>"
                                        data-event-location="<?= htmlspecialchars($row['event_location'], ENT_QUOTES) ?>"
                                        data-reg-date="<?= htmlspecialchars($formattedDate, ENT_QUOTES) ?>"
                                    >
                                        <td>
                                            <span class="table-id-badge">#<?= (int)$row['id'] ?></span>
                                        </td>
                                        <td style="font-weight: 600;">
                                            <?= htmlspecialchars($row['user_name']) ?>
                                        </td>
                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($row['user_email']) ?>" style="color: var(--primary); text-decoration: none;">
                                                <?= htmlspecialchars($row['user_email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span style="font-weight: 500; color: #1e293b;">
                                                <?= htmlspecialchars($row['event_title']) ?>
                                            </span>
                                        </td>
                                        <td style="color: var(--text-muted); font-size: 0.88rem;">
                                            <?= htmlspecialchars($formattedDate) ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="table-actions" style="justify-content: center;">
                                                <!-- View Button -->
                                                <button 
                                                    type="button" 
                                                    class="action-btn view btn-view-modal"
                                                    title="View Registration Details"
                                                >
                                                    <span>👁️</span> View
                                                </button>

                                                <!-- Delete Button -->
                                                <button 
                                                    type="button" 
                                                    class="action-btn delete btn-delete-modal"
                                                    data-id="<?= (int)$row['id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['user_name'], ENT_QUOTES) ?>"
                                                    title="Delete Registration"
                                                >
                                                    <span>🗑️</span> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal 1: View Registration Details -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Registration Details</h3>
                <button type="button" class="modal-close" id="closeViewModal">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                    <span class="ticket-label">Registration ID</span>
                    <span class="table-id-badge" id="modalRegId" style="font-size: 0.95rem;"></span>
                </div>

                <div class="ticket-grid" style="margin-bottom: 16px;">
                    <div class="ticket-item">
                        <span class="ticket-label">Attendee Name</span>
                        <span class="ticket-value" id="modalUserName"></span>
                    </div>
                    <div class="ticket-item">
                        <span class="ticket-label">Attendee Email</span>
                        <span class="ticket-value" id="modalUserEmail"></span>
                    </div>
                </div>

                <hr class="ticket-divider">

                <div style="margin-bottom: 16px;">
                    <span class="ticket-label">Event Name</span>
                    <div id="modalEventTitle" style="font-size: 1.15rem; font-weight: 700; color: var(--primary); margin-top: 4px;"></div>
                </div>

                <div class="ticket-grid">
                    <div class="ticket-item">
                        <span class="ticket-label">Event Date</span>
                        <span class="ticket-value" id="modalEventDate" style="font-size: 0.95rem;"></span>
                    </div>
                    <div class="ticket-item">
                        <span class="ticket-label">Location / Venue</span>
                        <span class="ticket-value" id="modalEventLocation" style="font-size: 0.95rem;"></span>
                    </div>
                </div>

                <hr class="ticket-divider">

                <div class="ticket-item">
                    <span class="ticket-label">Date & Time Registered</span>
                    <span class="ticket-value" id="modalRegDate" style="font-size: 0.95rem; color: var(--text-muted);"></span>
                </div>
            </div>
            <div class="modal-footer">
                <a id="modalDirectLink" href="#" class="btn btn-outline btn-sm" target="_blank">
                    Open Dedicated Page &nearr;
                </a>
                <button type="button" class="btn btn-secondary btn-sm" id="btnDismissView">Close</button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="color: var(--danger);">Confirm Deletion</h3>
                <button type="button" class="modal-close" id="closeDeleteModal">&times;</button>
            </div>
            <div class="modal-body">
                <div style="font-size: 44px; text-align: center; margin-bottom: 12px;">⚠️</div>
                <p style="text-align: center; color: var(--text-main); font-size: 1rem; margin-bottom: 8px;">
                    Are you sure you want to delete registration <strong id="deleteTargetId"></strong> for <strong id="deleteTargetName"></strong>?
                </p>
                <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                    This action is permanent and cannot be undone.
                </p>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" class="btn btn-secondary btn-sm" id="btnCancelDelete">Cancel</button>
                <form id="deleteForm" method="POST" action="delete.php" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="id" id="deleteInputId" value="">
                    <button type="submit" class="btn btn-danger btn-sm">
                        Yes, Delete Registration
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="site-footer" style="margin-top: 40px;">
        <div class="container">
            <p>&copy; <?= date('Y') ?> LevelX Event Management &bull; Admin Portal</p>
        </div>
    </footer>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
