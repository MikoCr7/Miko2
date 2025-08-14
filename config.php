<?php
// Database Configuration for 251KENO Game
define('DB_HOST', 'localhost');
define('DB_NAME', 'kenocobq_keno_db');
define('DB_USER', 'kenocobq_keno_db');
define('DB_PASS', 'Bcs8gJWUFcPwmCVQY4U4');

// Game Configuration
define('GAME_NAME', '251KENO');
define('WELCOME_BONUS', 25.00); // ETB
define('MIN_BET', 5.00); // ETB
define('MAX_BET', 1000.00); // ETB
define('MIN_WITHDRAWAL', 20.00); // ETB
define('MAX_WITHDRAWAL', 500.00); // ETB

// Chapa Payment Configuration
define('CHAPA_PUBLIC_KEY', 'CHAPUBK_TEST-HbYr8RhzEbxGcD3TBjRPa5rk6ahGwyfA');
define('CHAPA_SECRET_KEY', 'CHASECK_TEST-xxxxxxxxxxxxxxxxxxxxxxxxxx');

// Security Configuration
define('ADMIN_PASSWORD', 'admin123');
define('JWT_SECRET', 'your-secret-key-here-change-this-in-production');

// Database Connection Function
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

// Utility Functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

function validateEthiopianPhone($phone) {
    return preg_match('/^09\d{8}$/', $phone);
}

function formatCurrency($amount) {
    return number_format($amount, 2) . ' ETB';
}

function logActivity($action, $details = '') {
    try {
        // Create logs directory if it doesn't exist
        $logsDir = 'logs';
        if (!is_dir($logsDir)) {
            if (!mkdir($logsDir, 0755, true)) {
                // If we can't create the directory, fail silently
                return;
            }
        }
        
        $log = date('Y-m-d H:i:s') . " - " . $action . " - " . $details . "\n";
        @file_put_contents('logs/activity.log', $log, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        // Fail silently if logging fails - don't break the application
    }
}
?>
