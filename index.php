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
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fliesen Runnebaum - Handwerk mit Präzision | Fliesenleger Steinfeld</title>
    <meta name="description" content="Professionelle Fliesenarbeiten in Steinfeld und Umgebung. Über 20 Jahre Erfahrung in Badezimmer, Küche, Wohnräume und Außenbereiche.">
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cookie.css">
    <link rel="stylesheet" href="assets/css/lager-banner.css">
</head>
<body>
    <header class="header" id="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <img src="assets/img/logotest.png" alt="Fliesen Runnebaum" style="height: 50px; width: auto;">
                </a>
                <button class="menu-toggle" aria-label="Menü öffnen">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu">
                    <li class="nav-item"><a href="index.php" class="nav-link active">Start</a></li>
                    <li class="nav-item"><a href="leistungen.html" class="nav-link">Leistungen</a></li>
                    <li class="nav-item"><a href="galerie.html" class="nav-link">Galerie</a></li>
                    <li class="nav-item"><a href="kontakt.html" class="nav-link cta">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-label">Fliesenleger Meisterbetrieb</div>
                <h1 class="hero-title">
                    Handwerk<br>
                    mit <span>Präzision</span>
                </h1>
                <p class="hero-description">
                    Seit über 20 Jahren gestalten wir Räume in Steinfeld und Umgebung. 
                    Mit Liebe zum Detail und dem Auge für das Besondere.
                </p>
                <div class="hero-actions">
                    <a href="kontakt.html" class="btn-primary">
                        Projekt starten
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="galerie.html" class="btn-secondary">
                        Arbeiten ansehen
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80" alt="Modernes Badezimmer">
            </div>
        </div>
        
        <div class="hero-grid-overlay"></div>
    </section>

    <section class="tile-spotlight">
        <div class="container">
            <div class="spotlight-card" id="tileOfMonthCard">
                <div class="spotlight-image">
                    <div class="spotlight-tag">Fliese des Monats</div>
                    <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($tile['main_image']); ?>" 
                         alt="<?php echo htmlspecialchars($tile['title']); ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1615971677499-5467cbab01c0?w=800&q=80'">
                </div>
                <div class="spotlight-content">
                    <div class="spotlight-month"><?php echo htmlspecialchars($tile['month']); ?></div>
                    <h2 class="spotlight-title"><?php echo htmlspecialchars($tile['title']); ?></h2>
                    <p class="spotlight-description">
                        <?php echo htmlspecialchars($tile['description']); ?>
                    </p>
                    <div class="spotlight-features">
                        <?php foreach ($tile['features'] as $feature): ?>
                            <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="spotlight-price">
                        <span class="price-old"><?php echo htmlspecialchars($tile['old_price']); ?> €</span>
                        <span class="price-new"><?php echo htmlspecialchars($tile['new_price']); ?> <span class="price-unit">€/m²</span></span>
                        <span class="price-save">-<?php echo htmlspecialchars($tile['saving']); ?> € sparen</span>
                    </div>
                    <a href="fliese-des-monats.php" class="btn-primary">Mehr erfahren</a>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section section-spacing">
        <div class="container">
            <div class="about-inner">
                <div class="about-image-container">
                    <div class="about-image">
                        <img src="assets/img/olli.png" alt="Oliver Runnebaum bei der Arbeit">
                    </div>
                    <div class="about-decor"></div>
                </div>
                <div class="about-content">
                    <div class="section-label">Über uns</div>
                    <h2 class="section-title">
                        Qualität, die<br>
                        man sieht & fühlt
                    </h2>
                    <p class="about-text">
                        Seit über zwei Jahrzehnten sind wir Ihr kompetenter Partner für hochwertige 
                        Fliesenarbeiten. Ob Badezimmer, Küche, Wohnbereich oder Terrasse – wir 
                        verlegen Ihre Fliesen fachgerecht und mit Liebe zum Detail.
                    </p>
                    <p class="about-text">
                        Unsere langjährige Erfahrung und das handwerkliche Können garantieren 
                        Ihnen ein perfektes Ergebnis. Wir beraten Sie gerne und setzen Ihre 
                        individuellen Wünsche professionell um.
                    </p>
                    <div class="about-stats">
                        <div class="stat">
                            <div class="stat-number">20+</div>
                            <div class="stat-label">Jahre Erfahrung</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Projekte</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Zufriedenheit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services-teaser">
        <div class="container">
            <div class="services-intro">
                <div>
                    <div class="section-label">Leistungen</div>
                    <h2 class="section-title">Vom Bad bis<br>zur Terrasse.</h2>
                </div>
                <p class="services-intro-text">
                    Jedes Projekt ist anders. Wir passen uns an – ob komplette Sanierung 
                    oder einzelner Raum. Hier ein Überblick, was wir für Sie tun können.
                </p>
            </div>
            
            <div class="services-grid">
                <article class="service-card">
                    <div class="service-image">
                        <span class="service-num">01</span>
                        <img src="https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=800&q=80" alt="Badezimmer">
                    </div>
                    <div class="service-content">
                        <span class="service-cat">Kernkompetenz</span>
                        <h3>Badezimmer</h3>
                        <p>Komplette Sanierung, bodenebene Duschen, fachgerechte Abdichtung. Das Herzstück unserer Arbeit.</p>
                    </div>
                </article>
                
                <article class="service-card">
                    <div class="service-image">
                        <span class="service-num">02</span>
                        <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80" alt="Küche">
                    </div>
                    <div class="service-content">
                        <span class="service-cat">Innenbereich</span>
                        <h3>Küche</h3>
                        <p>Robuste Bodenfliesen und praktische Fliesenspiegel für den täglichen Gebrauch.</p>
                    </div>
                </article>
                
                <article class="service-card">
                    <div class="service-image">
                        <span class="service-num">03</span>
                        <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80" alt="Wohnbereich">
                    </div>
                    <div class="service-content">
                        <span class="service-cat">Wohnen</span>
                        <h3>Wohnräume</h3>
                        <p>Großformate und Holzoptik – modern, pflegeleicht und überraschend warm.</p>
                    </div>
                </article>
                
                <article class="service-card">
                    <div class="service-image">
                        <span class="service-num">04</span>
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80" alt="Terrasse">
                    </div>
                    <div class="service-content">
                        <span class="service-cat">Outdoor</span>
                        <h3>Terrasse & Balkon</h3>
                        <p>Frostsichere Fliesen und Platten für draußen. Trittsicher bei jedem Wetter.</p>
                    </div>
                </article>
            </div>
            
            <div class="text-center">
                <a href="leistungen.html" class="btn-primary">Alle Leistungen ansehen</a>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-inner">
                <div class="section-label" style="justify-content: center;">Bereit loszulegen?</div>
                <h2 class="section-title">
                    Lassen Sie uns über<br>
                    Ihr Projekt sprechen
                </h2>
                <p>
                    Kontaktieren Sie uns für eine unverbindliche Beratung und 
                    ein individuelles Angebot.
                </p>
                <div class="cta-actions">
                    <a href="kontakt.html" class="btn-primary">
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
                    <a href="index.php" class="logo">
                        <img src="assets/img/logotest.png" alt="Fliesen Runnebaum" style="height: 40px; width: auto;">
                    </a>
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
                <p>Wir verwenden ausschließlich technisch notwendige Cookies, um Ihre Cookie-Einstellungen zu speichern. Es werden keine Analyse- oder Marketing-Cookies verwendet.</p>
                
                <div class="cookie-options">
                    <div class="cookie-option">
                        <input type="checkbox" id="essential-cookies" checked disabled>
                        <label for="essential-cookies">
                            <strong>Notwendige Cookies</strong>
                            <p>Diese Cookies sind für die Grundfunktionen der Website erforderlich und können nicht deaktiviert werden.</p>
                        </label>
                    </div>
                </div>
                
                <p class="cookie-info-text">Weitere Informationen finden Sie in unserer <a href="datenschutz.html">Datenschutzerklärung</a>.</p>
            </div>
            <div class="modal-footer">
                <button id="cookie-accept-all" class="btn-primary">Verstanden</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/header-scroll.js"></script>
    <script src="assets/js/cookie.js"></script>
    <script src="assets/js/lager-banner.js"></script>
</body>
</html>