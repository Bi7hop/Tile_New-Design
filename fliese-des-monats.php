<?php
// Datei mit den Fliesen-des-Monats-Daten laden
$tile_data_file = __DIR__ . '/data/tile-of-month.json';
$tile_data = [];

if (file_exists($tile_data_file)) {
    $tile_data = json_decode(file_get_contents($tile_data_file), true);
}

// Standardwerte für den Fall, dass keine Datei existiert
if (empty($tile_data)) {
    $tile_data = [
        'month' => date('F Y'),
        'title' => 'XXL-Betonoptik Fliesen',
        'description' => 'Moderne Großformatfliesen in zeitloser Betonoptik.',
        'format' => '120×60 cm',
        'material' => 'Feinsteinzeug',
        'look' => 'Betonoptik, matt',
        'surface' => 'Leicht strukturiert',
        'properties' => 'Frostsicher, abriebfest, rutschhemmend R9',
        'usage' => 'Wohnräume, Badezimmer, Küche',
        'floor_heating' => 'Geeignet',
        'availability' => 'Sofort lieferbar',
        'features' => ['120×60cm', 'Frostsicher', 'Für Fußbodenheizung'],
        'old_price' => '59.95',
        'new_price' => '49.95',
        'saving' => '10.00',
        'main_image' => 'fliesedesmonats.png',
        'detail_images' => ['Detail.png', 'Detail.png', 'Detail.png'],
        'detailed_description' => [
            'Die XXL-Betonoptik Fliesen vereinen modernen Industriestil mit praktischer Funktionalität. Das großzügige Format von 120×60 cm sorgt für ein offenes Raumgefühl mit minimalen Fugen und lässt selbst kleinere Räume größer wirken.',
            'Die matte, leicht strukturierte Oberfläche imitiert Beton perfekt, bietet jedoch deutlich mehr Vorteile: Die Fliesen sind pflegeleichter, widerstandsfähiger und langlebiger als echter Beton, dabei aber genauso ausdrucksstark.',
            'Dank der Frostsicherheit eignen sich die Fliesen auch für den Übergang zu überdachten Außenbereichen, was ein durchgängiges Designkonzept ermöglicht. Die rutschhemmende Eigenschaft R9 sorgt für die nötige Sicherheit in Wohnräumen.'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fliese des Monats - <?php echo htmlspecialchars($tile_data['title']); ?> | Fliesen Runnebaum</title>
    <meta name="description" content="<?php echo htmlspecialchars($tile_data['description']); ?> Zum Sonderpreis von <?php echo htmlspecialchars($tile_data['new_price']); ?> €/m².">
    
    <link rel="icon" href="assets/img/fliesenrunnebaum_favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/fliese-des-monats.css">
    <link rel="stylesheet" href="assets/css/common.css">
    
    <script src="assets/js/animations.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/preloader.js" defer></script>
</head>
<body>
    <header class="header">
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
                    <li class="nav-item"><a href="kontakt.html" class="nav-link">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header">
        <div class="container">
            <h1>Fliese des Monats</h1>
            <p>Entdecken Sie unsere monatlich wechselnden Fliesenhighlights zu Sonderpreisen</p>
        </div>
    </section>

    <main>
        <section class="tile-detail-section">
            <div class="container">
                <div class="tile-detail-header">
                    <div class="tile-month-badge"><?php echo htmlspecialchars($tile_data['month']); ?></div>
                    <h2><?php echo htmlspecialchars($tile_data['title']); ?></h2>
                </div>
                
                <div class="tile-detail-content">
                    <div class="tile-detail-gallery">
                        <div class="tile-main-image">
                            <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($tile_data['main_image']); ?>" alt="<?php echo htmlspecialchars($tile_data['title']); ?> Hauptbild" class="img-fluid">
                        </div>
                        <div class="tile-thumbnails">
                            <?php foreach ($tile_data['detail_images'] as $index => $image): ?>
                                <img src="assets/img/tile-of-month/<?php echo htmlspecialchars($image); ?>" alt="Detailansicht <?php echo $index + 1; ?>" class="img-fluid">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="tile-detail-info">
                        <div class="tile-price-box">
                            <div class="price-comparison">
                                <span class="old-price"><?php echo htmlspecialchars($tile_data['old_price']); ?> €/m²</span>
                                <span class="new-price"><?php echo htmlspecialchars($tile_data['new_price']); ?> €/m²</span>
                            </div>
                            <span class="price-save">Sie sparen: <?php echo htmlspecialchars($tile_data['saving']); ?> €/m²</span>
                        </div>
                        
                        <div class="tile-specs">
                            <h3>Produktdetails</h3>
                            <ul class="specs-list">
                                <li><strong>Format:</strong> <?php echo htmlspecialchars($tile_data['format']); ?></li>
                                <li><strong>Material:</strong> <?php echo htmlspecialchars($tile_data['material']); ?></li>
                                <li><strong>Optik:</strong> <?php echo htmlspecialchars($tile_data['look']); ?></li>
                                <li><strong>Oberfläche:</strong> <?php echo htmlspecialchars($tile_data['surface']); ?></li>
                                <li><strong>Eigenschaften:</strong> <?php echo htmlspecialchars($tile_data['properties']); ?></li>
                                <li><strong>Einsatzbereich:</strong> <?php echo htmlspecialchars($tile_data['usage']); ?></li>
                                <li><strong>Fußbodenheizung:</strong> <?php echo htmlspecialchars($tile_data['floor_heating']); ?></li>
                                <li><strong>Verfügbarkeit:</strong> <?php echo htmlspecialchars($tile_data['availability']); ?></li>
                            </ul>
                        </div>
                        
                        <div class="tile-description">
                            <h3>Produktbeschreibung</h3>
                            <?php foreach ($tile_data['detailed_description'] as $paragraph): ?>
                                <p><?php echo htmlspecialchars($paragraph); ?></p>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="tile-cta">
                            <a href="kontakt.html" class="btn">Unverbindlich anfragen</a>
                            <a href="tel:+17610432567" class="btn btn-secondary">0176 / 10432567</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Interesse geweckt?</h2>
                    <p>Kontaktieren Sie uns für eine persönliche Beratung oder besuchen Sie unsere Ausstellung in Steinfeld.</p>
                    <div class="cta-buttons">
                        <a href="kontakt.html" class="btn">Kontakt aufnehmen</a>
                        <a href="galerie.html" class="btn btn-secondary">Weitere Referenzen ansehen</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>Fliesen Runnebaum</h3>
                    <p>Rouen Kamp 1<br>49439 Steinfeld</p>
                    <p>Telefon: 0176 / 10432567<br>E-Mail: info@fliesen-runnebaum.net</p>
                </div>
                <div class="footer-nav">
                    <h3>Navigation</h3>
                    <ul>
                        <li><a href="index.php">Start</a></li>
                        <li><a href="leistungen.html">Leistungen</a></li>
                        <li><a href="galerie.html">Galerie</a></li>
                        <li><a href="kontakt.html">Kontakt</a></li>
                    </ul>
                </div>
                <div class="footer-hours">
                    <h3>Öffnungszeiten</h3>
                    <p>Mo - Fr: 8:00 - 17:00 Uhr<br>Sa: Nach Vereinbarung<br>So: Geschlossen</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Fliesen Runnebaum. Alle Rechte vorbehalten.</p>
                <p><a href="impressum.html">Impressum</a> | <a href="datenschutz.html">Datenschutz</a></p>
            </div>
        </div>
    </footer>
    <a href="#" class="back-to-top" aria-label="Nach oben scrollen">&#8679;</a>
</body>
</html>