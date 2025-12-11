<?php
require_once 'config.php';

$error = '';
$warning = '';

// Wenn Benutzer bereits eingeloggt ist, zum Dashboard weiterleiten
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Session-Timeout-Meldung
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $warning = 'Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.';
}

// Login-Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    $ip = get_client_ip();
    
    // CSRF-Token validieren
    if (!validate_csrf_token($csrf_token)) {
        $error = 'Sicherheitsfehler. Bitte versuchen Sie es erneut.';
        security_log("CSRF-Token-Fehler bei Login-Versuch von IP: $ip");
    }
    // Login-Versuche prüfen
    else {
        $attempt_check = check_login_attempts($ip);
        
        if ($attempt_check['blocked']) {
            $error = "Zu viele fehlgeschlagene Login-Versuche. Bitte warten Sie {$attempt_check['remaining_minutes']} Minuten.";
            security_log("Blockierter Login-Versuch von IP: $ip (noch {$attempt_check['remaining_minutes']} Min blockiert)");
        }
        // Login-Daten prüfen
        else if (empty($username) || empty($password)) {
            $error = 'Bitte füllen Sie alle Felder aus.';
            register_failed_login($ip);
        }
        // Benutzername und Passwort validieren
        else if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            // Erfolgreicher Login
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = ADMIN_USERNAME;
            $_SESSION['admin_login_time'] = time();
            $_SESSION['admin_last_activity'] = time();
            $_SESSION['admin_session_regenerate'] = time();
            $_SESSION['admin_ip'] = $ip;
            
            // CSRF-Token erneuern
            unset($_SESSION['csrf_token']);
            
            register_successful_login($ip);
            header('Location: dashboard.php');
            exit;
        }
        else {
            // Fehlgeschlagener Login
            $error = 'Ungültige Zugangsdaten.';
            register_failed_login($ip);
            
            // Kleiner Delay zur Erschwerung von Brute-Force-Angriffen
            usleep(rand(500000, 1500000)); // 0.5-1.5 Sekunden
        }
    }
}

// Neuen CSRF-Token generieren
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Login - Fliesen Runnebaum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="icon" href="../assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    
    <style>
        .security-info {
            background-color: #e8f4f8;
            border: 1px solid #126e82;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .timeout-warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #721c24;
        }
        
        /* PASSWORT-INPUT FIX - VERSTÄRKT */
        .password-wrapper {
            position: relative !important;
            width: 100% !important;
            display: block !important;
        }
        
        .password-input {
            width: 100% !important;
            padding-right: 50px !important;
            box-sizing: border-box !important;
        }
        
        .eye-button {
            position: absolute !important;
            right: 10px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            color: #666 !important;
            z-index: 999999 !important;
            padding: 8px !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
            font-size: 16px !important;
            line-height: 1 !important;
        }
        
        .eye-button:hover {
            color: #333 !important;
            background: transparent !important;
        }
        
        .eye-button i {
            pointer-events: none !important;
        }
        
        /* Überschreibe alle möglichen form-control Styles */
        .form-group .password-wrapper .password-input {
            padding-right: 50px !important;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <img src="../assets/img/logo.png" alt="Fliesen Runnebaum" class="login-logo" onerror="this.style.display='none'">
            <h1>Fliesen Runnebaum</h1>
            <p>Sicherer Admin-Bereich</p>
        </div>
        
        <?php if ($warning): ?>
            <div class="timeout-warning">
                <i class="fas fa-clock"></i>
                <?php echo htmlspecialchars($warning); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Anmeldung fehlgeschlagen</div>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="security-info">
            <i class="fas fa-shield-alt"></i>
            <strong>Sicherheitshinweise:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                <li>Ihre Sitzung läuft nach 30 Minuten Inaktivität ab</li>
                <li>Nach 5 fehlgeschlagenen Versuchen wird der Zugang für 15 Minuten gesperrt</li>
                <li>Alle Login-Aktivitäten werden protokolliert</li>
            </ul>
        </div>
        
        <form class="login-form" method="post" action="" autocomplete="off">
            <!-- CSRF-Schutz -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       autofocus 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Passwort</label>
                <div class="password-wrapper">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="password-input"
                           required>
                    <button type="button" 
                            id="toggle-password" 
                            class="eye-button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt btn-icon"></i>
                    Sicher anmelden
                </button>
            </div>
        </form>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> Fliesen Runnebaum</p>
        </div>
    </div>
    
    <script>
        // Passwort anzeigen/verstecken
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    </script>
</body>
</html>