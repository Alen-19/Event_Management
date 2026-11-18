<?php
/**
 * Database Configuration & Connection
 * Event Management System
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'event_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO Database Connection
 *
 * @param bool $selectDb Whether to select DB_NAME or connect to server only
 * @return PDO
 * @throws PDOException
 */
function getDBConnection(bool $selectDb = true): PDO {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ($selectDb ? ";dbname=" . DB_NAME : "") . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, DB_USER, DB_PASS, $options);
}

/**
 * Helper to get active DB connection with user-friendly error output for web requests
 *
 * @return PDO
 */
function getActiveDB(): PDO {
    static $pdoInstance = null;
    if ($pdoInstance !== null) {
        return $pdoInstance;
    }

    try {
        $pdoInstance = getDBConnection(true);
        return $pdoInstance;
    } catch (PDOException $e) {
        $isCli = (php_sapi_name() === 'cli');
        if ($isCli) {
            fwrite(STDERR, "[Database Error] " . $e->getMessage() . "\n");
            exit(1);
        }

        // If database does not exist
        if ($e->getCode() == 1049) {
            http_response_code(500);
            echo '
            <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 620px; margin: 50px auto; padding: 28px; border-radius: 12px; background: #fff; border: 1px solid #fecaca; box-shadow: 0 10px 25px rgba(0,0,0,0.05); color: #1e293b;">
                <div style="font-size: 32px; margin-bottom: 12px;">🛠️</div>
                <h2 style="margin: 0 0 10px 0; color: #991b1b; font-size: 1.4rem;">Database Not Initialized</h2>
                <p style="color: #475569; line-height: 1.6; margin: 0 0 18px 0;">The database <strong>' . htmlspecialchars(DB_NAME) . '</strong> hasn\'t been created yet. Please run the automatic installer to initialize tables and sample events.</p>
                <a href="setup.php" style="display:inline-block; padding: 11px 22px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem;">Run Setup Wizard &rarr;</a>
            </div>';
            exit;
        }

        // Connection failure
        http_response_code(500);
        echo '
        <div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; max-width: 620px; margin: 50px auto; padding: 28px; border-radius: 12px; background: #fff; border: 1px solid #fecaca; box-shadow: 0 10px 25px rgba(0,0,0,0.05); color: #1e293b;">
            <div style="font-size: 32px; margin-bottom: 12px;">⚠️</div>
            <h2 style="margin: 0 0 10px 0; color: #991b1b; font-size: 1.4rem;">Database Connection Failed</h2>
            <p style="color: #475569; line-height: 1.6; margin: 0 0 14px 0;">Could not connect to MySQL server at <strong>' . htmlspecialchars(DB_HOST) . ':' . htmlspecialchars(DB_PORT) . '</strong>.</p>
            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 10px 14px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; color: #b91c1c; margin-bottom: 18px;">
                ' . htmlspecialchars($e->getMessage()) . '
            </div>
            <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Please ensure that <strong>MySQL</strong> and <strong>Apache</strong> are started in your XAMPP Control Panel.</p>
        </div>';
        exit;
    }
}
