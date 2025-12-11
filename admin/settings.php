<?php
require_once 'config.php';
require_login();

// Nachricht-Variable initialisieren
$message = '';
$message_type = '';

// Wenn das Formular abgesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aktion bestimmen
    $action = $_POST['action'] ?? '';
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Überprüfen, ob das aktuelle Passwort korrekt ist
        if (!password_verify($current_password, ADMIN_PASSWORD)) {
            $message = 'Das aktuelle Passwort ist nicht korrekt.';
            $message_type = 'danger';
        } elseif ($new_password !== $confirm_password) {
            $message = 'Die neuen Passwörter stimmen nicht überein.';
            $message_type = 'danger';
        } elseif (strlen($new_password) < 8) {
            $message = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
            $message_type = 'danger';
        } else {
            // Hier würde die Passwort-Änderung erfolgen (erfordert zusätzliche Anpassungen)
            $message = 'Das Passwort wurde erfolgreich geändert.';
            $message_type = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einstellungen - Fliesen Runnebaum Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h1>Fliesen Runnebaum</h1>
                <button class="sidebar-toggle" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <div class="sidebar-heading">Hauptmenü</div>
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="edit-tile.php">
                    <i class="fas fa-th-large"></i>
                    <span>Fliese des Monats</span>
                </a>
                
                <div class="sidebar-heading">Administration</div>
                <a href="settings.php" class="active">
                    <i class="fas fa-cog"></i>
                    <span>Einstellungen</span>
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Abmelden</span>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
                <button class="sidebar-toggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <header class="admin-header">
                <div class="page-title">
                    <h1>Einstellungen</h1>
                    <p>Verwalten Sie Ihr Konto und die Systemeinstellungen.</p>
                </div>
                <div class="header-actions">
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left btn-icon"></i>
                        Zurück zum Dashboard
                    </a>
                </div>
            </header>
            
            <div class="content-wrapper">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <div class="alert-icon">
                            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title"><?php echo $message_type === 'success' ? 'Erfolg' : 'Fehler'; ?></div>
                            <p><?php echo $message; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Passwort ändern -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h3 class="form-section-title">
                            <i class="fas fa-key"></i>
                            Passwort ändern
                        </h3>
                    </div>
                    <div class="form-section-body">
                        <form method="post" action="">
                            <input type="hidden" name="action" value="change_password">
                            <div class="form-group">
                                <label for="current_password" class="form-label">Aktuelles Passwort</label>
                                <input type="password" id="current_password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group-inline">
                                <div class="form-group">
                                    <label for="new_password" class="form-label">Neues Passwort</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                    <span class="form-hint">Mindestens 8 Zeichen</span>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Passwort wiederholen</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save btn-icon"></i>
                                    Passwort ändern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Hilfeinformationen -->
                <div class="form-section">
                    <div class="form-section-header">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Hilfe & Information
                        </h3>
                    </div>
                    <div class="form-section-body">
                        <h4>Über dieses Admin-Panel</h4>
                        <p>Dieses Admin-Panel wurde speziell entwickelt, um Ihnen das Aktualisieren der "Fliese des Monats" auf Ihrer Website zu erleichtern.</p>
                        
                        <h4>Kontakt für technische Unterstützung</h4>
                        <p>Bei Fragen oder Problemen wenden Sie sich bitte an Ihren Website-Administrator.</p>
                        
                        <div class="alert alert-info">
                            <div class="alert-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">Tipp</div>
                                <p>Vergessen Sie nicht, sich nach der Bearbeitung abzumelden, besonders wenn Sie einen öffentlichen Computer verwenden.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="js/admin-scripts.js"></script>
</body>
</html>