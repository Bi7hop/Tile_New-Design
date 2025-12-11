<?php
require_once 'config.php';
require_login();

$preview_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preview_data = [
        'month' => $_POST['month'] ?? '',
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'format' => $_POST['format'] ?? '',
        'material' => $_POST['material'] ?? '',
        'look' => $_POST['look'] ?? '',
        'surface' => $_POST['surface'] ?? '',
        'properties' => $_POST['properties'] ?? '',
        'usage' => $_POST['usage'] ?? '',
        'floor_heating' => $_POST['floor_heating'] ?? '',
        'availability' => $_POST['availability'] ?? '',
        'features' => explode(',', $_POST['features'] ?? ''),
        'old_price' => $_POST['old_price'] ?? '',
        'new_price' => $_POST['new_price'] ?? '',
        'saving' => $_POST['saving'] ?? '',
        'main_image' => $_POST['current_main_image'] ?? 'fliesedesmonats.png',
        'detail_images' => explode(',', $_POST['current_detail_images'] ?? ''),
        'detailed_description' => explode("\n\n", $_POST['detailed_description'] ?? '')
    ];
    
    $preview_data['features'] = array_map('trim', $preview_data['features']);
    $preview_data['features'] = array_filter($preview_data['features']);
    
    $preview_data['detailed_description'] = array_map('trim', $preview_data['detailed_description']);
    $preview_data['detailed_description'] = array_filter($preview_data['detailed_description']);
    
    $_SESSION['preview_data'] = $preview_data;
} elseif (isset($_SESSION['preview_data'])) {
    $preview_data = $_SESSION['preview_data'];
} else {
    $preview_data = get_current_tile();
}

$tile_data = $preview_data;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VORSCHAU - Fliese des Monats - <?php echo htmlspecialchars($tile_data['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($tile_data['description']); ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/fliese-des-monats.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    
    <style>
        .preview-warning {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ff6b35;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 1.1rem;
        }
        
        body {
            padding-top: 55px;
        }
        
        .preview-actions {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .preview-btn {
            background: #e74c3c;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
            transition: all 0.3s ease;
        }
        
        .preview-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(231, 76, 60, 0.4);
        }
        
        /* Custom Modal für Schließen-Bestätigung */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .custom-modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .modal-icon {
            font-size: 3rem;
            color: #ff6b35;
            margin-bottom: 15px;
        }
        
        .modal-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .modal-text {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-btn-cancel {
            background: #95a5a6;
            color: white;
        }
        
        .modal-btn-cancel:hover {
            background: #7f8c8d;
        }
        
        .modal-btn-confirm {
            background: #e74c3c;
            color: white;
        }
        
        .modal-btn-confirm:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="preview-warning">
        🔍 VORSCHAU-MODUS - Diese Änderungen sind noch nicht gespeichert!
    </div>

    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="../index.html" class="logo">Fliesen Runnebaum</a>
                <span style="color: #ff6b35; font-weight: bold; font-size: 1.1rem;">VORSCHAU-MODUS</span>
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
                            <img src="../assets/img/tile-of-month/<?php echo htmlspecialchars($tile_data['main_image']); ?>" alt="<?php echo htmlspecialchars($tile_data['title']); ?> Hauptbild" class="img-fluid">
                        </div>
                        <div class="tile-thumbnails">
                            <?php foreach ($tile_data['detail_images'] as $index => $image): ?>
                                <img src="../assets/img/tile-of-month/<?php echo htmlspecialchars($image); ?>" alt="Detailansicht <?php echo $index + 1; ?>" class="img-fluid">
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
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="preview-actions">
        <button onclick="confirmClose()" class="preview-btn">
            <i class="fas fa-times" style="margin-right: 8px;"></i>
            Vorschau schließen
        </button>
    </div>

    <div class="custom-modal" id="confirmModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Vorschau schließen?</div>
            <div class="modal-text">
                Möchten Sie die Vorschau wirklich schließen?<br>
                <strong>Ungespeicherte Änderungen gehen verloren.</strong>
            </div>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="cancelClose()">
                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                    Weiter bearbeiten
                </button>
                <button class="modal-btn modal-btn-confirm" onclick="closePreview()">
                    <i class="fas fa-times" style="margin-right: 5px;"></i>
                    Schließen
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmClose() {
            document.getElementById('confirmModal').classList.add('active');
        }
        
        function cancelClose() {
            document.getElementById('confirmModal').classList.remove('active');
        }
        
        function closePreview() {
            window.close();
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('confirmModal');
                if (modal.classList.contains('active')) {
                    cancelClose();
                } else {
                    confirmClose();
                }
            }
        });
        
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cancelClose();
            }
        });
        
        
    </script>
</body>
</html>