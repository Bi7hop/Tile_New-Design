<?php
require_once 'config.php';
require_login();

// Aktuelle Fliese des Monats laden
$current_tile = get_current_tile();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fliese des Monats - Verwaltung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="admin-layout">
        <!-- Top-Navigation -->
        <header class="admin-topbar">
            <div class="admin-logo">
                <img src="../assets/img/logo.png" alt="Fliesen Runnebaum" onerror="this.style.display='none'">
                <h1>Fliesen Runnebaum <span>Admin</span></h1>
            </div>
            <div class="user-menu">
                <a href="logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt btn-icon"></i>
                    Abmelden
                </a>
            </div>
        </header>
        
        <main class="admin-main">
            <!-- Willkommensbereich -->
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="mb-3"><i class="fas fa-home"></i> Willkommen!</h1>
                    <p class="mb-2" style="font-size: 1.2rem;">Hier können Sie die "Fliese des Monats" auf Ihrer Website verwalten.</p>
                    <p>Wählen Sie unten eine Option aus oder klicken Sie direkt auf "Fliese des Monats bearbeiten".</p>
                </div>
            </div>

            <!-- Vereinfachtes Navigationsmenü -->
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