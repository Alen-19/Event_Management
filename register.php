<?php
/**
 * Event Registration Page
 * Event Management System
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getActiveDB();
$pageTitle = 'Event Registration';
$activePage = 'register';
$basePath = '';

// Pre-selected event from GET query
$selectedEventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$errors = [];
$userName = '';
$userEmail = '';

// Fetch available events for dropdown
try {
    $stmt = $pdo->query("SELECT id, title, event_date, location FROM events ORDER BY event_date ASC");
    $allEvents = $stmt->fetchAll();
} catch (PDOException $e) {
    $allEvents = [];
    $errors[] = "Failed to load events: " . $e->getMessage();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($submittedToken)) {
        $errors[] = "Invalid session token. Please try submitting the form again.";
    }

    // Retrieve and trim inputs
    $userName = trim($_POST['user_name'] ?? '');
    $userEmail = trim($_POST['user_email'] ?? '');
    $selectedEventId = (int)($_POST['event_id'] ?? 0);

    // Validation 1: No empty fields
    if ($userName === '') {
        $errors[] = "Full Name is required.";
    } elseif (mb_strlen($userName) < 2) {
        $errors[] = "Full Name must be at least 2 characters.";
    }

    // Validation 2: Valid email
    if ($userEmail === '') {
        $errors[] = "Email Address is required.";
    } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    // Validation 3: Valid event
    if ($selectedEventId <= 0) {
        $errors[] = "Please select an event to register for.";
    } else {
        // Check if event actually exists in database
        $eventCheckStmt = $pdo->prepare("SELECT id, title FROM events WHERE id = :id LIMIT 1");
        $eventCheckStmt->execute([':id' => $selectedEventId]);
        $eventRecord = $eventCheckStmt->fetch();
        if (!$eventRecord) {
            $errors[] = "Selected event does not exist.";
        }
    }

    // If validation passes, save registration to MySQL
    if (empty($errors)) {
        try {
            $insertStmt = $pdo->prepare("
                INSERT INTO registrations (event_id, user_name, user_email, created_at)
                VALUES (:event_id, :user_name, :user_email, NOW())
            ");
            $insertStmt->execute([
                ':event_id'   => $selectedEventId,
                ':user_name'  => $userName,
                ':user_email' => $userEmail,
            ]);

            $newRegistrationId = $pdo->lastInsertId();

            // Redirect to success page
            header("Location: success.php?id=" . $newRegistrationId);
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error while saving registration: " . $e->getMessage();
        }
    }
}

// Find details for selected event to display preview
$selectedEventData = null;
foreach ($allEvents as $ev) {
    if ((int)$ev['id'] === $selectedEventId) {
        $selectedEventData = $ev;
        break;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="form-card">
        <div class="form-header">
            <div style="font-size: 36px; margin-bottom: 8px;">🎟️</div>
            <h2>Register for an Event</h2>
            <p>Fill out the registration details below to reserve your attendance.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <div>
                    <strong>Please correct the following:</strong>
                    <ul style="margin: 8px 0 0 18px; padding: 0;">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($allEvents)): ?>
            <div class="alert alert-danger">
                <div>
                    No events available right now. Please <a href="setup.php" style="color: inherit; font-weight: bold; text-decoration: underline;">run setup</a> to seed sample events.
                </div>
            </div>
        <?php else: ?>
            <form id="registrationForm" method="POST" action="register.php<?= $selectedEventId ? '?event_id=' . $selectedEventId : '' ?>" novalidate>
                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

                <!-- Full Name -->
                <div class="form-group">
                    <label for="user_name" class="form-label">
                        Full Name <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="user_name" 
                        name="user_name" 
                        class="form-control <?= (!empty($errors) && empty($userName)) ? 'is-invalid' : '' ?>" 
                        placeholder="e.g. Alex Johnson"
                        value="<?= htmlspecialchars($userName) ?>"
                        required
                    >
                    <div class="invalid-feedback">Full Name is required.</div>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="user_email" class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="user_email" 
                        name="user_email" 
                        class="form-control <?= (!empty($errors) && (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL))) ? 'is-invalid' : '' ?>" 
                        placeholder="e.g. alex@example.com"
                        value="<?= htmlspecialchars($userEmail) ?>"
                        required
                    >
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                    <div class="form-hint">Confirmation summary will be linked to this email address.</div>
                </div>

                <!-- Event (Auto-filled / Selectable) -->
                <div class="form-group">
                    <label for="event_id" class="form-label">
                        Event <span class="required">*</span>
                    </label>
                    <select 
                        id="event_id" 
                        name="event_id" 
                        class="form-control <?= (!empty($errors) && $selectedEventId <= 0) ? 'is-invalid' : '' ?>"
                        required
                    >
                        <option value="">-- Choose an Event --</option>
                        <?php foreach ($allEvents as $ev): ?>
                            <?php 
                                $isSel = ($selectedEventId === (int)$ev['id']);
                                $evDate = date('M d, Y', strtotime($ev['event_date']));
                            ?>
                            <option value="<?= (int)$ev['id'] ?>" <?= $isSel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ev['title']) ?> (<?= $evDate ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select an event.</div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 10px;">
                    <span>Complete Registration</span> &rarr;
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
