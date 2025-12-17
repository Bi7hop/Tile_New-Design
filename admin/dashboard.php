<?php
require_once 'config.php';
require_login();

// Aktuelle Fliese des Monats laden
$current_tile = get_current_tile();

// Benutzername aus Session
$username = $_SESSION['admin_username'] ?? 'Admin';

// Zeitbasierte Begrüßung
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Guten Morgen';
    $greeting_icon = 'fa-sun';
    $greeting_color = '#F59E0B';
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = 'Guten Tag';
    $greeting_icon = 'fa-cloud-sun';
    $greeting_color = '#3B82F6';
} elseif ($hour >= 18 && $hour < 22) {
    $greeting = 'Guten Abend';
    $greeting_icon = 'fa-moon';
    $greeting_color = '#8B5CF6';
} else {
    $greeting = 'Gute Nacht';
    $greeting_icon = 'fa-star';
    $greeting_color = '#6366F1';
}

// Aktuelles Datum formatiert
$weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
$months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
$current_weekday = $weekdays[(int)date('w')];
$current_day = (int)date('j');
$current_month = $months[(int)date('n') - 1];
$current_year = date('Y');
$formatted_date = "$current_weekday, $current_day. $current_month $current_year";

// Berechne Tage bis Monatsende
$days_in_month = (int)date('t');
$current_day_num = (int)date('j');
$days_until_end = $days_in_month - $current_day_num;

// Letzte Änderung der Fliese (aus JSON-Datei)
$last_modified = "Unbekannt";
$days_since_update = 0;
if (file_exists(TILE_DATA_FILE)) {
    $last_modified_timestamp = filemtime(TILE_DATA_FILE);
    $last_modified = date('d.m.Y \u\m H:i', $last_modified_timestamp);
    $days_since_update = floor((time() - $last_modified_timestamp) / 86400);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fliesen Runnebaum Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/fonts.css">
    
    <style>
        /* Willkommens-Card - Terracotta Design */
        .welcome-card {
            background: linear-gradient(135deg, #C67B5C 0%, #A85D40 100%);
            border-radius: var(--border-radius, 12px);
            box-shadow: 0 10px 30px rgba(198, 123, 92, 0.3);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
        }
        
        .welcome-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        
        .welcome-greeting {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 0.4rem;
        }
        
        .welcome-greeting i {
            font-size: 1.2rem;
            color: #FDE68A;
        }
        
        .welcome-title {
            font-family: var(--font-display, 'Playfair Display', serif);
            font-size: clamp(1.6rem, 4vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.4rem 0;
            color: white;
        }
        
        .welcome-title span {
            color: #FEF3C7;
        }
        
        .welcome-date {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .welcome-date i {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .welcome-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(4px);
        }
        
        .welcome-badge i {
            font-size: 0.5rem;
            color: #4ADE80;
        }
        
        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .quick-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.875rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }
        
        .quick-stat:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        .quick-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .quick-stat-content {
            flex: 1;
            min-width: 0;
        }
        
        .quick-stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: white;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .quick-stat-label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Reminder Alert */
        .reminder-alert {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            color: #92400e;
        }
        
        .reminder-alert i {
            font-size: 1.2rem;
            color: #d97706;
        }
        
        .reminder-alert-content {
            flex: 1;
        }
        
        .reminder-alert strong {
            color: #78350f;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .welcome-card {
                padding: 1.5rem;
            }
            
            .welcome-top {
                flex-direction: column;
                gap: 1rem;
            }
            
            .quick-stats {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Top-Navigation -->
        <header class="admin-topbar">
            <div class="admin-topbar-inner">
                <div class="admin-logo">
                    <img src="../assets/img/logotest.png" alt="Fliesen Runnebaum">
                    <span>Adminpanel</span>
                </div>
                <div class="user-menu">
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt btn-icon"></i>
                        Abmelden
                    </a>
                </div>
            </div>
        </header>
        
        <main class="admin-main">
            <!-- Willkommens-Card -->
            <div class="welcome-card">
                <div class="welcome-top">
                    <div>
                        <div class="welcome-greeting">
                            <i class="fas <?php echo $greeting_icon; ?>" style="color: <?php echo $greeting_color; ?>"></i>
                            <?php echo $greeting; ?>
                        </div>
                        <h1 class="welcome-title">Willkommen zurück, <span><?php echo htmlspecialchars($username); ?></span>!</h1>
                        <div class="welcome-date">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo $formatted_date; ?>
                        </div>
                    </div>
                    <div class="welcome-badge">
                        <i class="fas fa-circle"></i>
                        Eingeloggt
                    </div>
                </div>
                
                <div class="quick-stats">
                    <div class="quick-stat">
                        <div class="quick-stat-icon">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="quick-stat-content">
                            <div class="quick-stat-value"><?php echo htmlspecialchars($current_tile['month']); ?></div>
                            <div class="quick-stat-label">Aktuelle Fliese</div>
                        </div>
                    </div>
                    
                    <div class="quick-stat">
                        <div class="quick-stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="quick-stat-content">
                            <div class="quick-stat-value"><?php echo $last_modified; ?></div>
                            <div class="quick-stat-label">Letzte Änderung</div>
                        </div>
                    </div>
                    
                    <div class="quick-stat">
                        <div class="quick-stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="quick-stat-content">
                            <div class="quick-stat-value"><?php echo $days_until_end; ?> Tage</div>
                            <div class="quick-stat-label">Bis Monatsende</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($days_until_end <= 5): ?>
            <!-- Erinnerung wenn Monatsende naht -->
            <div class="reminder-alert">
                <i class="fas fa-bell"></i>
                <div class="reminder-alert-content">
                    <strong>Erinnerung:</strong> Nur noch <?php echo $days_until_end; ?> Tage bis zum Monatsende. 
                    Denken Sie daran, die Fliese des Monats zu aktualisieren!
                </div>
            </div>
            <?php endif; ?>

            <!-- Navigationsmenü -->
            <nav class="admin-nav mb-4">
                <ul class="nav-tabs">
                    <li>
                        <a href="dashboard.php" class="active">
                            <i class="fas fa-home icon"></i>
                            Startseite
                        </a>
                    </li>
                    <li>
                        <a href="edit-tile.php">
                            <i class="fas fa-edit icon"></i>
                            Fliese bearbeiten
                        </a>
                    </li>
                    <li>
                        <a href="../fliese-des-monats.php" target="_blank">
                            <i class="fas fa-eye icon"></i>
                            Vorschau
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt icon"></i>
                            Abmelden
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Aktuelle Fliese des Monats Vorschau -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-star icon"></i> Aktuelle Fliese des Monats</h2>
                    <a href="edit-tile.php" class="btn btn-primary">
                        <i class="fas fa-edit btn-icon"></i>
                        Fliese bearbeiten
                    </a>
                </div>
                <div class="card-body">
                    <div class="tile-preview">
                        <div class="tile-preview-image">
                            <img src="../assets/img/tile-of-month/<?php echo htmlspecialchars($current_tile['main_image']); ?>" 
                                alt="<?php echo htmlspecialchars($current_tile['title']); ?>"
                                onerror="this.src=''; this.alt='Kein Bild gefunden'; this.style.height='200px'; this.style.width='100%'; this.style.padding='30px'; this.style.border='2px dashed #ccc'; this.style.backgroundColor='#f8f8f8'; this.style.textAlign='center'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.onerror=null; this.style.fontSize='16px'; this.parentNode.appendChild(document.createTextNode('Kein Bild gefunden'));">
                        </div>
                        
                        <div class="text-center">
                            <span class="tile-preview-badge"><?php echo htmlspecialchars($current_tile['month']); ?></span>
                            <h2 class="tile-preview-title"><?php echo htmlspecialchars($current_tile['title']); ?></h2>
                            <p style="font-size: 1.1rem;"><?php echo htmlspecialchars($current_tile['description']); ?></p>
                            
                            <div class="tile-preview-price">
                                <span class="old-price"><?php echo htmlspecialchars($current_tile['old_price']); ?> €/m²</span>
                                <span class="new-price"><?php echo htmlspecialchars($current_tile['new_price']); ?> €/m²</span>
                                <span class="price-save">Sie sparen: <?php echo htmlspecialchars($current_tile['saving']); ?> €/m²</span>
                            </div>
                            
                            <div class="feature-tags">
                                <?php foreach ($current_tile['features'] as $feature): ?>
                                    <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <a href="edit-tile.php" class="btn btn-primary btn-lg mt-4">
                                <i class="fas fa-edit btn-icon"></i>
                                Fliese des Monats bearbeiten
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hilfebereich -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-question-circle icon"></i> Hilfe</h2>
                </div>
                <div class="card-body">
                    <div class="instruction-box">
                        <div class="instruction-title">
                            <i class="fas fa-lightbulb"></i>
                            So funktioniert's
                        </div>
                        <ol class="instruction-steps">
                            <li><strong>Fliese bearbeiten</strong> - Klicken Sie auf den Button "Fliese des Monats bearbeiten"</li>
                            <li><strong>Daten eingeben</strong> - Geben Sie den neuen Monat, Titel, Beschreibung und Preise ein</li>
                            <li><strong>Bilder hochladen</strong> - Falls nötig, laden Sie neue Bilder hoch</li>
                            <li><strong>Speichern</strong> - Klicken Sie unten auf "Änderungen speichern"</li>
                            <li><strong>Fertig!</strong> - Die neue Fliese wird sofort auf der Website angezeigt</li>
                        </ol>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <div class="alert-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Tipp für Bilder</div>
                            <p>Bilder sollten im Format JPG oder PNG sein und folgende Größen haben:</p>
                            <ul>
                                <li><strong>Hauptbild:</strong> etwa 600×400 Pixel</li>
                                <li><strong>Detailbilder:</strong> etwa 300×200 Pixel</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> Fliesen Runnebaum | <a href="../index.php" target="_blank">Website anzeigen</a></p>
        </footer>
    </div>
    
    <script src="js/admin-scripts.js"></script>
</body>
</html>