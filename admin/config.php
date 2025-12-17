<?php
/**
 * Admin-Konfiguration für Fliesen Runnebaum
 * Unterstützt mehrere Benutzer
 */

// Sitzung starten
session_start();

// Zeitzone setzen
date_default_timezone_set('Europe/Berlin');

// Fehlerbehandlung
ini_set('display_errors', 0);
error_reporting(E_ALL);

// ==========================================
// ADMIN-ZUGANGSDATEN (EXTERN LADEN)
// ==========================================

if (file_exists(__DIR__ . '/credentials.php')) {
    require_once __DIR__ . '/credentials.php';
} else {
    // Fallback - NICHT FÜR PRODUKTION!
    define('ADMIN_USERS', [
        'admin' => [
            'password_hash' => 'PRODUCTION_HASH_HERE',
            'display_name' => 'Administrator'
        ]
    ]);
    error_log("WARNUNG: credentials.php fehlt! Bitte auf Server erstellen.");
}

// ==========================================
// SICHERHEITS-KONFIGURATION
// ==========================================

define('SESSION_TIMEOUT', 1800); // 30 Minuten
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 Minuten

// ==========================================
// PFADE
// ==========================================

define('BASE_PATH', dirname(__DIR__));
define('DATA_PATH', BASE_PATH . '/data');
define('TILE_DATA_FILE', DATA_PATH . '/tile-of-month.json');
define('UPLOAD_PATH', BASE_PATH . '/assets/img/tile-of-month');
define('ADMIN_URL', '/admin');
define('LOG_FILE', DATA_PATH . '/admin-security.log');

// Verzeichnisse erstellen
if (!is_dir(DATA_PATH)) {
    mkdir(DATA_PATH, 0755, true);
}

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// ==========================================
// AUTHENTIFIZIERUNGS-FUNKTIONEN
// ==========================================

/**
 * Prüft Login-Daten gegen alle registrierten Benutzer
 */
function validate_user($username, $password) {
    if (!defined('ADMIN_USERS') || !is_array(ADMIN_USERS)) {
        return false;
    }
    
    // Benutzer suchen (case-sensitive)
    if (!isset(ADMIN_USERS[$username])) {
        return false;
    }
    
    $user = ADMIN_USERS[$username];
    
    // Passwort prüfen
    if (password_verify($password, $user['password_hash'])) {
        return [
            'username' => $username,
            'display_name' => $user['display_name'] ?? $username
        ];
    }
    
    return false;
}

/**
 * Gibt den Anzeigenamen des aktuellen Benutzers zurück
 */
function get_current_user_display_name() {
    return $_SESSION['admin_display_name'] ?? $_SESSION['admin_username'] ?? 'Admin';
}

// ==========================================
// SICHERHEITSFUNKTIONEN
// ==========================================

function get_client_ip() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function security_log($message) {
    $log_entry = date('Y-m-d H:i:s') . " - " . $message . " - IP: " . get_client_ip() . PHP_EOL;
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
}

function check_login_attempts($ip) {
    $attempts_file = DATA_PATH . '/login_attempts.json';
    $attempts = [];
    
    if (file_exists($attempts_file)) {
        $attempts = json_decode(file_get_contents($attempts_file), true) ?: [];
    }
    
    $current_time = time();
    foreach ($attempts as $attempt_ip => $data) {
        if ($current_time - $data['last_attempt'] > LOGIN_LOCKOUT_TIME) {
            unset($attempts[$attempt_ip]);
        }
    }
    
    if (isset($attempts[$ip]) && $attempts[$ip]['count'] >= MAX_LOGIN_ATTEMPTS) {
        if ($current_time - $attempts[$ip]['last_attempt'] < LOGIN_LOCKOUT_TIME) {
            $remaining = LOGIN_LOCKOUT_TIME - ($current_time - $attempts[$ip]['last_attempt']);
            return [
                'blocked' => true,
                'remaining_minutes' => ceil($remaining / 60)
            ];
        }
    }
    
    return ['blocked' => false];
}

function register_failed_login($ip) {
    $attempts_file = DATA_PATH . '/login_attempts.json';
    $attempts = [];
    
    if (file_exists($attempts_file)) {
        $attempts = json_decode(file_get_contents($attempts_file), true) ?: [];
    }
    
    if (!isset($attempts[$ip])) {
        $attempts[$ip] = ['count' => 0, 'last_attempt' => 0];
    }
    
    $attempts[$ip]['count']++;
    $attempts[$ip]['last_attempt'] = time();
    
    file_put_contents($attempts_file, json_encode($attempts, JSON_PRETTY_PRINT));
    
    security_log("Fehlgeschlagener Login-Versuch von IP: $ip (Versuch {$attempts[$ip]['count']})");
}

function register_successful_login($ip, $username) {
    $attempts_file = DATA_PATH . '/login_attempts.json';
    if (file_exists($attempts_file)) {
        $attempts = json_decode(file_get_contents($attempts_file), true) ?: [];
        unset($attempts[$ip]);
        file_put_contents($attempts_file, json_encode($attempts, JSON_PRETTY_PRINT));
    }
    
    security_log("Erfolgreicher Login von IP: $ip, User: $username");
}

function require_login() {
    if (isset($_SESSION['admin_last_activity'])) {
        if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: index.php?timeout=1');
            exit;
        }
    }
    
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit;
    }
    
    $_SESSION['admin_last_activity'] = time();
    
    if (!isset($_SESSION['admin_session_regenerate']) || 
        time() - $_SESSION['admin_session_regenerate'] > 900) {
        session_regenerate_id(true);
        $_SESSION['admin_session_regenerate'] = time();
    }
}

function show_error($message = 'Ein unbekannter Fehler ist aufgetreten.', $type = 'danger') {
    $_SESSION['message'] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $_SESSION['message_type'] = $type;
}

function show_success($message) {
    show_error($message, 'success');
}

// Functions.php laden
if (file_exists(__DIR__ . '/functions.php')) {
    require_once 'functions.php';
}
?>