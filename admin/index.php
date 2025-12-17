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
        else if ($user = validate_user($username, $password)) {
            // Erfolgreicher Login
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_display_name'] = $user['display_name'];
            $_SESSION['admin_login_time'] = time();
            $_SESSION['admin_last_activity'] = time();
            $_SESSION['admin_session_regenerate'] = time();
            $_SESSION['admin_ip'] = $ip;
            
            // CSRF-Token erneuern
            unset($_SESSION['csrf_token']);
            
            register_successful_login($ip, $user['username']);
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
    <link rel="stylesheet" href="../assets/css/fonts.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="icon" href="../assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    
    <style>
        /* Verbesserte Alert-Styles für Login-Seite */
        .login-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        
        .login-alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fcd34d;
            color: #92400e;
        }
        
        .login-alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .login-alert-error .login-alert-icon {
            background: #fee2e2;
            color: #dc2626;
            border: 2px solid #fca5a5;
        }
        
        .login-alert-warning .login-alert-icon {
            background: #fef3c7;
            color: #d97706;
            border: 2px solid #fcd34d;
        }
        
        .login-alert-icon i {
            font-size: 1.1rem;
        }
        
        .login-alert-content {
            flex: 1;
        }
        
        .login-alert-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }
        
        .login-alert-message {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Security Info Box - verbessert */
        .security-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #7dd3fc;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .security-info-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 10px;
        }
        
        .security-info-header i {
            font-size: 1rem;
        }
        
        .security-info ul {
            margin: 0;
            padding-left: 1.5rem;
            color: #0c4a6e;
        }
        
        .security-info li {
            margin-bottom: 6px;
            line-height: 1.4;
        }
        
        .security-info li:last-child {
            margin-bottom: 0;
        }
        
        /* Password Wrapper */
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
            right: 12px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            color: #9ca3af !important;
            z-index: 10 !important;
            padding: 8px !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
            font-size: 16px !important;
            line-height: 1 !important;
            transition: color 0.2s ease !important;
            border-radius: 4px !important;
        }
        
        .eye-button:hover {
            color: #C67B5C !important;
            background: transparent !important;
        }
        
        .eye-button i {
            pointer-events: none !important;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <img src="../assets/img/logotest.png" alt="Fliesen Runnebaum" class="login-logo" style="width: 120px; height: auto;">
            <h1>Fliesen Runnebaum</h1>
            <p>Sicherer Admin-Bereich</p>
        </div>
        
        <?php if ($error): ?>
            <div class="login-alert login-alert-error">
                <div class="login-alert-icon">
                    <i class="fas fa-times"></i>
                </div>
                <div class="login-alert-content">
                    <div class="login-alert-title">Anmeldung fehlgeschlagen</div>
                    <div class="login-alert-message"><?php echo htmlspecialchars($error); ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($warning): ?>
            <div class="login-alert login-alert-warning">
                <div class="login-alert-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="login-alert-content">
                    <div class="login-alert-title">Sitzung abgelaufen</div>
                    <div class="login-alert-message"><?php echo htmlspecialchars($warning); ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="security-info">
            <div class="security-info-header">
                <i class="fas fa-shield-alt"></i>
                Sicherheitshinweise
            </div>
            <ul>
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
                            class="eye-button"
                            aria-label="Passwort anzeigen">
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
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.className = 'fas fa-eye-slash';
                this.setAttribute('aria-label', 'Passwort verbergen');
            } else {
                passwordField.type = 'password';
                icon.className = 'fas fa-eye';
                this.setAttribute('aria-label', 'Passwort anzeigen');
            }
        });
    </script>
</body>
</html>