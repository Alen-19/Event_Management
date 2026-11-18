<?php
/**
 * Homepage - Upcoming Events
 * Event Management System
 */

require_once __DIR__ . '/config/db.php';

$pdo = getActiveDB();
$pageTitle = 'Upcoming Events';
$activePage = 'home';
$basePath = '';

// Fetch upcoming events ordered by date
try {
    $stmt = $pdo->query("SELECT id, title, description, event_date, location FROM events ORDER BY event_date ASC");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
    $dbError = $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-pill">
            <span>✨</span> Discover & Join Amazing Tech Events
        </div>
        <h1 class="hero-title">Experience Unforgettable <span>Live Events</span></h1>
        <p class="hero-subtitle">
            Explore our curated lineup of conferences, hackathons, and masterclasses. Secure your spot in seconds.
        </p>
    </div>
</section>

<!-- Events Listing -->
<div class="container" style="padding-top: 40px;">
    <div class="section-header">
        <div>
            <h2 class="section-title">Upcoming Schedule</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Showing <?= count($events) ?> available event(s)</p>
        </div>
    </div>

    <?php if (empty($events)): ?>
        <div class="form-card" style="text-align: center; padding: 50px 20px;">
            <div style="font-size: 40px; margin-bottom: 12px;">📅</div>
            <h3>No Events Found</h3>
            <p style="color: var(--text-muted); margin: 10px 0 20px 0;">
                There are currently no upcoming events registered in the database.
            </p>
            <a href="setup.php" class="btn btn-primary">Run Database Installer &rarr;</a>
        </div>
    <?php else: ?>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <?php
                    $eventTimestamp = strtotime($event['event_date']);
                    $formattedDate = date('M d, Y', $eventTimestamp);
                    $formattedTime = date('h:i A', $eventTimestamp);
                ?>
                <article class="event-card">
                    <div class="event-card-header">
                        <div class="event-date-badge">
                            <span>📅</span>
                            <span><?= htmlspecialchars($formattedDate) ?> &bull; <?= htmlspecialchars($formattedTime) ?></span>
                        </div>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-location">
                            <span>📍</span>
                            <span><?= htmlspecialchars($event['location']) ?></span>
                        </div>
                    </div>
                    <div class="event-card-body">
                        <p class="event-description">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </p>
                        <a href="register.php?event_id=<?= (int)$event['id'] ?>" class="btn btn-primary btn-block">
                            Register For Event &rarr;
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
