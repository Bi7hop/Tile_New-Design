<?php
$tile_data_file = __DIR__ . '/data/tile-of-month.json';
$tile = null;

if (file_exists($tile_data_file)) {
    $json = file_get_contents($tile_data_file);
    $tile = json_decode($json, true);
}

if (!$tile) {
    $tile = [
        'month' => 'Dezember 2025',
        'title' => 'XXL-Betonoptik Fliesen',
        'description' => 'Moderne Großformatfliesen in zeitloser Betonoptik. Ideal für Wohnräume und Badezimmer. Minimale Fugen für ein großzügiges Raumgefühl.',
        'main_image' => 'default-tile.jpg',
        'features' => ['120×60cm', 'Frostsicher', 'Fußbodenheizung'],
        'old_price' => '59,95',
        'new_price' => '49,95',
        'saving' => '10,00'
    ];
}

// Preise formatieren (Punkt durch Komma ersetzen)
if (isset($tile['old_price'])) {
    $tile['old_price'] = str_replace('.', ',', $tile['old_price']);
}
if (isset($tile['new_price'])) {
    $tile['new_price'] = str_replace('.', ',', $tile['new_price']);
}
if (isset($tile['saving'])) {
    $tile['saving'] = str_replace('.', ',', $tile['saving']);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fliese des Monats - <?php echo htmlspecialchars($tile['title']); ?> | Fliesen Runnebaum</title>
    <meta name="description" content="<?php echo htmlspecialchars($tile['title']); ?> - Unser Angebot des Monats. <?php echo htmlspecialchars($tile['description']); ?>">
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/fliese-des-monats.css">
    <link rel="stylesheet" href="assets/css/cookie.css">
</head>
<body>
    <header class="header" id="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Fliesen Runnebaum</a>
                <button class="menu-toggle" aria-label="Menü öffnen">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu">
                    <li class="nav-item"><a href="index.php" class="nav-link">Start</a></li>
                    <li class="nav-item"><a href="leistungen.html" class="nav-link">Leistungen</a></li>
                    <li class="nav-item"><a href="galerie.html" class="nav-link">Galerie</a></li>
                    <li class="nav-item"><a href="kontakt.html" class="nav-link cta">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header tile-header">
        <div class="container">
            <div class="page-header-content">
                <div class="section-label">Angebot des Monats</div>
                <h1 class="page-title"><?php echo htmlspecialchars($tile['title']); ?></h1>
                <p class="page-description"><?php echo htmlspecialchars($tile['month']); ?></p>
            </div>
        </div>
    </section>

    <section class="tile-detail section-spacing">
        <div class="container">
            <div class="tile-detail-grid">
                <div class="tile-gallery">
                    <div class="main-image">
                        <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($tile['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($tile['title']); ?>"
                             id="main-tile-image"
                             onerror="this.src='https://images.unsplash.com/photo-1615971677499-5467cbab01c0?w=800&q=80'">
                        <span class="sale-badge">Angebot</span>
                    </div>
                    <?php if (!empty($tile['detail_images'])): ?>
                    <div class="thumbnail-row">
                        <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($tile['main_image']); ?>" 
                             alt="Hauptbild" 
                             class="thumbnail active"
                             onclick="changeMainImage(this.src)">
                        <?php foreach ($tile['detail_images'] as $img): ?>
                        <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($img); ?>" 
                             alt="Detailbild" 
                             class="thumbnail"
                             onclick="changeMainImage(this.src)"
                             onerror="this.style.display='none'">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="tile-info">
                    <div class="tile-meta">
                        <span class="month-badge"><?php echo htmlspecialchars($tile['month']); ?></span>
                    </div>
                    
                    <h2 class="tile-title"><?php echo htmlspecialchars($tile['title']); ?></h2>
                    
                    <p class="tile-description">
                        <?php echo htmlspecialchars($tile['description']); ?>
                    </p>

                    <div class="tile-features">
                        <h3>Eigenschaften</h3>
                        <ul class="feature-list">
                            <?php foreach ($tile['features'] as $feature): ?>
                            <li>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="tile-pricing">
                        <div class="price-block">
                            <span class="old-price"><?php echo htmlspecialchars($tile['old_price']); ?> €/m²</span>
                            <span class="current-price"><?php echo htmlspecialchars($tile['new_price']); ?> <span>€/m²</span></span>
                        </div>
                        <div class="savings">
                            <span class="save-badge">Sie sparen <?php echo htmlspecialchars($tile['saving']); ?> €/m²</span>
                        </div>
                    </div>

                    <div class="tile-actions">
                        <a href="kontakt.html?subject=fliese-des-monats" class="btn-primary btn-large">
                            Jetzt anfragen
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="tel:+4917610432567" class="btn-secondary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Anrufen
                        </a>
                    </div>

                    <div class="tile-note">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        <p>Angebot gültig solange der Vorrat reicht. Verlegung auf Anfrage.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Produktdetails Section -->
    <?php if (!empty($tile['format']) || !empty($tile['material']) || !empty($tile['look']) || 
              !empty($tile['surface']) || !empty($tile['properties']) || !empty($tile['usage']) || 
              !empty($tile['floor_heating']) || !empty($tile['availability'])): ?>
    <section class="product-specs section-spacing">
        <div class="container">
            <div class="section-header">
                <div class="section-label">Technische Details</div>
                <h2 class="section-title">Produktspezifikationen</h2>
            </div>
            
            <div class="specs-grid">
                <?php if (!empty($tile['format'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Format</h4>
                        <p><?php echo htmlspecialchars($tile['format']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['material'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Material</h4>
                        <p><?php echo htmlspecialchars($tile['material']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['look'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Optik</h4>
                        <p><?php echo htmlspecialchars($tile['look']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['surface'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Oberfläche</h4>
                        <p><?php echo htmlspecialchars($tile['surface']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['properties'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Eigenschaften</h4>
                        <p><?php echo htmlspecialchars($tile['properties']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['usage'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Einsatzbereich</h4>
                        <p><?php echo htmlspecialchars($tile['usage']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['floor_heating'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Fußbodenheizung</h4>
                        <p><?php echo htmlspecialchars($tile['floor_heating']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($tile['availability'])): ?>
                <div class="spec-item">
                    <div class="spec-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                    </div>
                    <div class="spec-content">
                        <h4>Verfügbarkeit</h4>
                        <p><?php echo htmlspecialchars($tile['availability']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Detaillierte Beschreibung Section -->
    <?php if (!empty($tile['detailed_description']) && is_array($tile['detailed_description'])): ?>
    <section class="detailed-description section-spacing">
        <div class="container">
            <div class="section-header">
                <div class="section-label">Details</div>
                <h2 class="section-title">Ausführliche Produktbeschreibung</h2>
            </div>
            
            <div class="description-content">
                <?php foreach ($tile['detailed_description'] as $paragraph): ?>
                    <?php if (!empty(trim($paragraph))): ?>
                        <p><?php echo nl2br(htmlspecialchars(trim($paragraph))); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <div class="section-label">Warum bei uns kaufen?</div>
                <h2 class="section-title">Ihre Vorteile</h2>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3>Qualitätsgarantie</h3>
                    <p>Nur hochwertige Markenware von renommierten Herstellern.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M16 8l-4 4-2-2"/>
                        </svg>
                    </div>
                    <h3>Fachberatung</h3>
                    <p>Kompetente Beratung durch unsere erfahrenen Fliesenleger.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <h3>Lieferung möglich</h3>
                    <p>Wir liefern auf Wunsch direkt zu Ihnen nach Hause.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h3>Verlegung inklusive</h3>
                    <p>Auf Wunsch übernehmen wir auch die fachgerechte Verlegung.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-inner">
                <div class="section-label" style="justify-content: center;">Interesse geweckt?</div>
                <h2 class="section-title">
                    Sichern Sie sich jetzt<br>
                    dieses Angebot
                </h2>
                <p>
                    Kontaktieren Sie uns für eine unverbindliche Beratung. 
                    Wir beantworten gerne Ihre Fragen.
                </p>
                <div class="cta-actions">
                    <a href="kontakt.html?subject=fliese-des-monats" class="btn-primary">
                        Jetzt anfragen
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="tel:+4917610432567" class="btn-secondary">
                        0176 / 10432567
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <a href="index.php" class="logo">Fliesen Runnebaum</a>
                    <p>
                        Ihr zuverlässiger Fliesenleger in Steinfeld und Umgebung. 
                        Qualität und Präzision seit über 20 Jahren.
                    </p>
                </div>
                <div class="footer-nav">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="index.php">Start</a></li>
                        <li><a href="leistungen.html">Leistungen</a></li>
                        <li><a href="galerie.html">Galerie</a></li>
                        <li><a href="kontakt.html">Kontakt</a></li>
                    </ul>
                </div>
                <div class="footer-info">
                    <h4>Kontakt</h4>
                    <ul>
                        <li><a href="tel:+4917610432567">0176 / 10432567</a></li>
                        <li><a href="mailto:info@fliesen-runnebaum.net">info@fliesen-runnebaum.net</a></li>
                    </ul>
                </div>
                <div class="footer-hours">
                    <h4>Adresse</h4>
                    <ul>
                        <li>Rouen Kamp 1</li>
                        <li>49439 Steinfeld</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Fliesen Runnebaum. Alle Rechte vorbehalten.</p>
                <div>
                    <a href="impressum.html">Impressum</a> · <a href="datenschutz.html">Datenschutz</a>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal-overlay" id="cookie-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Cookie-Einstellungen</h2>
                <button class="modal-close" id="cookie-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Wir verwenden Cookies, um Ihnen die bestmögliche Erfahrung auf unserer Website zu bieten. Sie können selbst entscheiden, welche Cookies Sie zulassen möchten.</p>
                
                <div class="cookie-options">
                    <div class="cookie-option">
                        <input type="checkbox" id="essential-cookies" checked disabled>
                        <label for="essential-cookies">
                            <strong>Notwendige Cookies</strong>
                            <p>Diese Cookies sind für die Grundfunktionen der Website erforderlich und können nicht deaktiviert werden.</p>
                        </label>
                    </div>
                    
                    <div class="cookie-option">
                        <input type="checkbox" id="analytics-cookies">
                        <label for="analytics-cookies">
                            <strong>Analyse-Cookies</strong>
                            <p>Helfen uns zu verstehen, wie Besucher mit unserer Website interagieren.</p>
                        </label>
                    </div>
                    
                    <div class="cookie-option">
                        <input type="checkbox" id="marketing-cookies">
                        <label for="marketing-cookies">
                            <strong>Marketing-Cookies</strong>
                            <p>Werden verwendet, um Besuchern relevante Werbung anzuzeigen.</p>
                        </label>
                    </div>
                </div>
                
                <p class="cookie-info-text">Weitere Informationen finden Sie in unserer <a href="datenschutz.html">Datenschutzerklärung</a>.</p>
            </div>
            <div class="modal-footer">
                <button id="cookie-reject-all" class="btn-outline">Alle ablehnen</button>
                <button id="cookie-save" class="btn-secondary">Auswahl speichern</button>
                <button id="cookie-accept-all" class="btn-primary">Alle akzeptieren</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/header-scroll.js"></script>
    <script src="assets/js/cookie.js"></script>
    <script>
    function changeMainImage(src) {
        document.getElementById('main-tile-image').src = src;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
    }
    </script>
</body>
</html>