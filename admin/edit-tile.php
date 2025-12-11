<?php
require_once 'config.php';
require_login();

// Nachricht-Variable initialisieren
$message = '';
$message_type = '';

// Aktuelle Fliese des Monats laden
$tile_data = get_current_tile();

// Wenn das Formular abgesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daten aus dem Formular übernehmen
    $tile_data['month'] = $_POST['month'] ?? $tile_data['month'];
    $tile_data['title'] = $_POST['title'] ?? $tile_data['title'];
    $tile_data['description'] = $_POST['description'] ?? $tile_data['description'];
    $tile_data['format'] = $_POST['format'] ?? $tile_data['format'];
    $tile_data['material'] = $_POST['material'] ?? $tile_data['material'];
    $tile_data['look'] = $_POST['look'] ?? $tile_data['look'];
    $tile_data['surface'] = $_POST['surface'] ?? $tile_data['surface'];
    $tile_data['properties'] = $_POST['properties'] ?? $tile_data['properties'];
    $tile_data['usage'] = $_POST['usage'] ?? $tile_data['usage'];
    $tile_data['floor_heating'] = $_POST['floor_heating'] ?? $tile_data['floor_heating'];
    $tile_data['availability'] = $_POST['availability'] ?? $tile_data['availability'];
    
    // Features als Array
    $tile_data['features'] = explode(',', $_POST['features'] ?? '');
    $tile_data['features'] = array_map('trim', $tile_data['features']);
    $tile_data['features'] = array_filter($tile_data['features']);
    
    // Preise
    $tile_data['old_price'] = $_POST['old_price'] ?? $tile_data['old_price'];
    $tile_data['new_price'] = $_POST['new_price'] ?? $tile_data['new_price'];
    $tile_data['saving'] = $_POST['saving'] ?? $tile_data['saving'];
    
    // Detaillierte Beschreibung als Array (jeder Absatz ist ein Element)
    $tile_data['detailed_description'] = explode("\n\n", $_POST['detailed_description'] ?? '');
    $tile_data['detailed_description'] = array_map('trim', $tile_data['detailed_description']);
    $tile_data['detailed_description'] = array_filter($tile_data['detailed_description']);
    
    // Hauptbild hochladen, wenn vorhanden
    if (isset($_FILES['main_image']) && $_FILES['main_image']['size'] > 0) {
        $upload_result = upload_image($_FILES['main_image'], UPLOAD_PATH);
        if ($upload_result['success']) {
            $tile_data['main_image'] = $upload_result['filename'];
        } else {
            $message = $upload_result['message'];
            $message_type = 'danger';
        }
    }
    
    // Detailbilder hochladen, wenn vorhanden
    $detail_images = [];
    if (isset($_FILES['detail_images'])) {
        for ($i = 0; $i < count($_FILES['detail_images']['name']); $i++) {
            if ($_FILES['detail_images']['size'][$i] > 0) {
                $file = [
                    'name' => $_FILES['detail_images']['name'][$i],
                    'type' => $_FILES['detail_images']['type'][$i],
                    'tmp_name' => $_FILES['detail_images']['tmp_name'][$i],
                    'error' => $_FILES['detail_images']['error'][$i],
                    'size' => $_FILES['detail_images']['size'][$i]
                ];
                
                $upload_result = upload_image($file, UPLOAD_PATH);
                if ($upload_result['success']) {
                    $detail_images[] = $upload_result['filename'];
                }
            }
        }
    }
    
    // Nur überschreiben, wenn neue Bilder hochgeladen wurden
    if (!empty($detail_images)) {
        $tile_data['detail_images'] = $detail_images;
    }
    
    // Daten speichern
    if (save_tile_data($tile_data)) {
        $message = 'Super! Die Fliese des Monats wurde erfolgreich aktualisiert.';
        $message_type = 'success';
    } else {
        $message = 'Leider ist beim Speichern ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
        $message_type = 'danger';
    }
}

// Monatsliste für das Dropdown-Menü
$month_options = get_month_options();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fliese des Monats bearbeiten</title>
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
            <!-- Navigationsmenü -->
            <nav class="admin-nav mb-4">
                <ul class="nav-tabs">
                    <li>
                        <a href="dashboard.php">
                            <i class="fas fa-home icon"></i>
                            Startseite
                        </a>
                    </li>
                    <li>
                        <a href="edit-tile.php" class="active">
                            <i class="fas fa-edit icon"></i>
                            Fliese bearbeiten
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="showPreview(); return false;">
                            <i class="fas fa-eye icon"></i>
                            Vorschau
                        </a>
                    </li>
                    <li>
                        <a href="../fliese-des-monats.php" target="_blank">
                            <i class="fas fa-globe icon"></i>
                            Live-Seite
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
            
            <!-- Seitentitel und Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="mb-3"><i class="fas fa-edit"></i> Fliese des Monats bearbeiten</h1>
                    <p class="mb-0" style="font-size: 1.1rem;">Hier können Sie alle Informationen zur aktuellen Fliese des Monats ändern.</p>
                </div>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> mb-4">
                    <div class="alert-icon">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo $message_type === 'success' ? 'Erfolgreich gespeichert!' : 'Ein Fehler ist aufgetreten'; ?></div>
                        <p><?php echo $message; ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Bearbeitungsformular -->
            <form method="post" action="" enctype="multipart/form-data" id="edit-form">
                <!-- Allgemeine Informationen -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-info-circle icon"></i> Allgemeine Informationen</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="month" class="form-label">Monat</label>
                                    <select id="month" name="month" class="form-control form-select" required>
                                        <?php foreach ($month_options as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($value === $tile_data['month']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="form-hint">Wählen Sie den Monat aus, für den die Fliese gelten soll.</span>
                                </div>
                                <div class="form-group">
                                    <label for="title" class="form-label">Titel</label>
                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($tile_data['title']); ?>" required>
                                    <span class="form-hint">Der Name oder die Bezeichnung der Fliese.</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">Kurzbeschreibung</label>
                                <textarea id="description" name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($tile_data['description']); ?></textarea>
                                <span class="form-hint">Eine kurze Beschreibung, die auf der Startseite angezeigt wird.</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Preise -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-tag icon"></i> Preise</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="old_price" class="form-label">Alter Preis (€/m²)</label>
                                    <input type="text" id="old_price" name="old_price" class="form-control" value="<?php echo htmlspecialchars($tile_data['old_price']); ?>" required>
                                    <span class="form-hint">Regulärer Preis vor dem Angebot.</span>
                                </div>
                                <div class="form-group">
                                    <label for="new_price" class="form-label">Neuer Preis (€/m²)</label>
                                    <input type="text" id="new_price" name="new_price" class="form-control" value="<?php echo htmlspecialchars($tile_data['new_price']); ?>" required>
                                    <span class="form-hint">Aktueller Angebotspreis.</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="saving" class="form-label">Ersparnis (€/m²)</label>
                                <input type="text" id="saving" name="saving" class="form-control" value="<?php echo htmlspecialchars($tile_data['saving']); ?>" required>
                                <span class="form-hint">Wird automatisch berechnet, wenn Sie den alten und neuen Preis ändern.</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bilder -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-images icon"></i> Bilder</h2>
                    </div>
                    <div class="card-body">
                        <!-- Hauptbild -->
                        <div class="form-row">
                            <div class="form-row-title">Hauptbild</div>
                            
                            <div class="image-preview mb-3 text-center">
                                <img src="../assets/img/tile-of-month/<?php echo htmlspecialchars($tile_data['main_image']); ?>" 
                                     alt="Aktuelles Hauptbild" id="main-image-preview"
                                     onerror="this.src=''; this.alt='Kein Bild gefunden'; this.style.height='200px'; this.style.width='100%'; this.style.padding='30px'; this.style.border='2px dashed #ccc'; this.style.backgroundColor='#f8f8f8'; this.style.textAlign='center'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.onerror=null; this.style.fontSize='16px'; this.parentNode.appendChild(document.createTextNode('Kein Bild gefunden'));">
                                <p class="image-name">Aktuelles Bild: <?php echo htmlspecialchars($tile_data['main_image']); ?></p>
                            </div>
                            
                            <div class="upload-container" id="main-image-upload">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h3 class="upload-title">Neues Hauptbild hochladen</h3>
                                <p class="upload-text">Klicken Sie hier, um ein neues Bild auszuwählen</p>
                                <button type="button" class="upload-button" id="main-image-button">Bild auswählen</button>
                                <input type="file" id="main_image" name="main_image" class="upload-input" accept="image/*">
                                <p class="upload-hint">Empfohlene Größe: 600×400 Pixel</p>
                            </div>
                        </div>
                        
                        <!-- Detailbilder -->
                        <div class="form-row mt-4">
                            <div class="form-row-title">Detailbilder</div>
                            
                            <div class="image-gallery" id="detail-images-gallery">
                                <?php foreach ($tile_data['detail_images'] as $index => $image): ?>
                                    <div class="image-gallery-item">
                                        <img src="../assets/img/tile-of-month/<?php echo htmlspecialchars($image); ?>" 
                                             alt="Detailbild <?php echo $index + 1; ?>"
                                             onerror="this.src=''; this.alt='Kein Bild'; this.style.height='120px'; this.style.width='100%'; this.style.padding='20px'; this.style.border='2px dashed #ccc'; this.style.backgroundColor='#f8f8f8'; this.style.textAlign='center'; this.style.display='flex'; this.style.alignItems='center'; this.style.justifyContent='center'; this.onerror=null; this.style.fontSize='14px'; this.parentNode.insertBefore(document.createTextNode('Kein Bild'), this.nextSibling);">
                                        <div class="image-gallery-actions">
                                            <span class="image-action" title="Entfernen">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="upload-container" id="detail-images-upload">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h3 class="upload-title">Neue Detailbilder hochladen</h3>
                                <p class="upload-text">Klicken Sie hier, um Bilder auszuwählen</p>
                                <button type="button" class="upload-button" id="detail-images-button">Bilder auswählen</button>
                                <input type="file" id="detail_images" name="detail_images[]" class="upload-input" accept="image/*" multiple>
                                <p class="upload-hint">Sie können bis zu 3 Bilder auswählen. Empfohlene Größe: 300×200 Pixel</p>
                            </div>
                            
                            <div class="form-hint mt-3">
                                <strong>Wichtig:</strong> Wenn Sie neue Detailbilder hochladen, werden alle bisherigen Detailbilder ersetzt.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Produktdetails -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-list icon"></i> Produktdetails</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="format" class="form-label">Format</label>
                                    <input type="text" id="format" name="format" class="form-control" value="<?php echo htmlspecialchars($tile_data['format']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" id="material" name="material" class="form-control" value="<?php echo htmlspecialchars($tile_data['material']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="look" class="form-label">Optik</label>
                                    <input type="text" id="look" name="look" class="form-control" value="<?php echo htmlspecialchars($tile_data['look']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="surface" class="form-label">Oberfläche</label>
                                    <input type="text" id="surface" name="surface" class="form-control" value="<?php echo htmlspecialchars($tile_data['surface']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="properties" class="form-label">Eigenschaften</label>
                                    <input type="text" id="properties" name="properties" class="form-control" value="<?php echo htmlspecialchars($tile_data['properties']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="usage" class="form-label">Einsatzbereich</label>
                                    <input type="text" id="usage" name="usage" class="form-control" value="<?php echo htmlspecialchars($tile_data['usage']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="floor_heating" class="form-label">Fußbodenheizung</label>
                                    <input type="text" id="floor_heating" name="floor_heating" class="form-control" value="<?php echo htmlspecialchars($tile_data['floor_heating']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="availability" class="form-label">Verfügbarkeit</label>
                                    <input type="text" id="availability" name="availability" class="form-control" value="<?php echo htmlspecialchars($tile_data['availability']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="features" class="form-label">Features (durch Komma getrennt)</label>
                                <input type="text" id="features" name="features" class="form-control" value="<?php echo htmlspecialchars(implode(', ', $tile_data['features'])); ?>">
                                <span class="form-hint">Beispiel: 120×60cm, Frostsicher, Für Fußbodenheizung</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ausführliche Beschreibung -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-align-left icon"></i> Ausführliche Beschreibung</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="detailed_description" class="form-label">Beschreibung</label>
                                <textarea id="detailed_description" name="detailed_description" class="form-control form-textarea" rows="8"><?php echo htmlspecialchars(implode("\n\n", $tile_data['detailed_description'])); ?></textarea>
                                <span class="form-hint">Für jeden neuen Absatz bitte eine Leerzeile einfügen.</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Speichern-Buttons -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="alert alert-info mb-4">
                            <div class="alert-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">Änderungen speichern</div>
                                <p>Wenn Sie auf "Speichern" klicken, werden die Änderungen sofort auf der Website sichtbar.</p>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <a href="dashboard.php" class="btn btn-outline btn-lg">
                                <i class="fas fa-times btn-icon"></i>
                                Abbrechen
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="showPreview()">
                                <i class="fas fa-eye btn-icon"></i>
                                Vorschau anzeigen
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save btn-icon"></i>
                                Änderungen speichern
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
        
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> Fliesen Runnebaum | <a href="../index.php" target="_blank">Website anzeigen</a></p>
        </footer>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatische Berechnung der Ersparnis
        const oldPriceInput = document.getElementById('old_price');
        const newPriceInput = document.getElementById('new_price');
        const savingInput = document.getElementById('saving');
        
        function calculateSaving() {
            if (oldPriceInput && newPriceInput && savingInput) {
                // Werte als Zahlen interpretieren, Komma durch Punkt ersetzen
                const oldPrice = parseFloat(oldPriceInput.value.replace(',', '.')) || 0;
                const newPrice = parseFloat(newPriceInput.value.replace(',', '.')) || 0;
                
                // Differenz berechnen (mindestens 0)
                const saving = Math.max(0, oldPrice - newPrice).toFixed(2).replace('.', ',');
                
                // Ergebnis in das Feld eintragen
                savingInput.value = saving;
            }
        }
        
        // Event-Listener für Preisänderungen
        if (oldPriceInput && newPriceInput) {
            oldPriceInput.addEventListener('input', calculateSaving);
            newPriceInput.addEventListener('input', calculateSaving);
        }
        
        // Bildvorschau für Hauptbild
        const mainImageInput = document.getElementById('main_image');
        const mainImagePreview = document.getElementById('main-image-preview');
        const mainImageButton = document.getElementById('main-image-button');
        const mainImageUpload = document.getElementById('main-image-upload');
        
        if (mainImageInput && mainImagePreview && mainImageButton) {
            // Klick auf Button öffnet Dateiauswahl
            mainImageButton.addEventListener('click', function() {
                mainImageInput.click();
            });
            
            // Klick auf Container öffnet Dateiauswahl
            mainImageUpload.addEventListener('click', function(e) {
                if (e.target !== mainImageButton) {
                    mainImageInput.click();
                }
            });
            
            // Änderung des Input-Felds
            mainImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    // Datei als URL laden
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Vorschaubild aktualisieren
                        mainImagePreview.src = e.target.result;
                        
                        // Lösche vorherigen "Kein Bild gefunden" Text, falls vorhanden
                        const parent = mainImagePreview.parentNode;
                        for (let i = 0; i < parent.childNodes.length; i++) {
                            if (parent.childNodes[i].nodeType === 3) { // Textknoten
                                parent.removeChild(parent.childNodes[i]);
                            }
                        }
                        
                        // Vorschaubild zurücksetzen
                        mainImagePreview.style = '';
                        mainImagePreview.alt = file.name;
                        
                        // Bildname anzeigen
                        const imageName = mainImagePreview.nextElementSibling;
                        if (imageName) {
                            imageName.textContent = 'Neues Bild: ' + file.name;
                        }
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Drag & Drop Funktionalität
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                mainImageUpload.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                mainImageUpload.addEventListener(eventName, function() {
                    this.classList.add('dragover');
                });
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                mainImageUpload.addEventListener(eventName, function() {
                    this.classList.remove('dragover');
                });
            });
            
            mainImageUpload.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    mainImageInput.files = files;
                    const file = files[0];
                    
                    // Datei als URL laden
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Vorschaubild aktualisieren
                        mainImagePreview.src = e.target.result;
                        
                        // Lösche vorherigen "Kein Bild gefunden" Text, falls vorhanden
                        const parent = mainImagePreview.parentNode;
                        for (let i = 0; i < parent.childNodes.length; i++) {
                            if (parent.childNodes[i].nodeType === 3) { // Textknoten
                                parent.removeChild(parent.childNodes[i]);
                            }
                        }
                        
                        // Vorschaubild zurücksetzen
                        mainImagePreview.style = '';
                        mainImagePreview.alt = file.name;
                        
                        // Bildname anzeigen
                        const imageName = mainImagePreview.nextElementSibling;
                        if (imageName) {
                            imageName.textContent = 'Neues Bild: ' + file.name;
                        }
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Bildvorschau für Detailbilder
        const detailImagesInput = document.getElementById('detail_images');
        const detailImagesGallery = document.getElementById('detail-images-gallery');
        const detailImagesButton = document.getElementById('detail-images-button');
        const detailImagesUpload = document.getElementById('detail-images-upload');
        
        if (detailImagesInput && detailImagesGallery && detailImagesButton) {
            // Klick auf Button öffnet Dateiauswahl
            detailImagesButton.addEventListener('click', function() {
                detailImagesInput.click();
            });
            
            // Klick auf Container öffnet Dateiauswahl
            detailImagesUpload.addEventListener('click', function(e) {
                if (e.target !== detailImagesButton) {
                    detailImagesInput.click();
                }
            });
            
            // Änderung des Input-Felds
            detailImagesInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    // Galerie leeren
                    detailImagesGallery.innerHTML = '';
                    
                    // Maximale Anzahl von Bildern (3)
                    const maxImages = 3;
                    const filesArray = Array.from(this.files).slice(0, maxImages);
                    
                    // Für jede Datei eine Vorschau erstellen
                    filesArray.forEach((file, index) => {
                        // Datei als URL laden
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Neue Galerie-Element erstellen
                            const galleryItem = document.createElement('div');
                            galleryItem.className = 'image-gallery-item';
                            
                            // Bild erstellen
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Neues Detailbild ' + (index + 1);
                            
                            // Aktionen-Container
                            const actionsDiv = document.createElement('div');
                            actionsDiv.className = 'image-gallery-actions';
                            
                            // Löschen-Button
                            const removeAction = document.createElement('span');
                            removeAction.className = 'image-action';
                            removeAction.title = 'Entfernen';
                            removeAction.innerHTML = '<i class="fas fa-trash"></i>';
                            
                            // Löschen-Button-Event (nur visuell)
                            removeAction.addEventListener('click', function() {
                                galleryItem.style.opacity = '0.3';
                                galleryItem.style.filter = 'grayscale(100%)';
                                
                                const hint = document.createElement('div');
                                hint.style.position = 'absolute';
                                hint.style.top = '0';
                                hint.style.left = '0';
                                hint.style.right = '0';
                                hint.style.bottom = '0';
                                hint.style.display = 'flex';
                                hint.style.alignItems = 'center';
                                hint.style.justifyContent = 'center';
                                hint.style.background = 'rgba(255,255,255,0.7)';
                                hint.style.color = '#e74c3c';
                                hint.style.fontWeight = 'bold';
                                hint.innerHTML = 'Wird entfernt';
                                
                                galleryItem.appendChild(hint);
                            });
                            
                            // Elemente zusammenfügen
                            actionsDiv.appendChild(removeAction);
                            galleryItem.appendChild(img);
                            galleryItem.appendChild(actionsDiv);
                            detailImagesGallery.appendChild(galleryItem);
                        };
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
            
            // Drag & Drop Funktionalität
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                detailImagesUpload.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                detailImagesUpload.addEventListener(eventName, function() {
                    this.classList.add('dragover');
                });
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                detailImagesUpload.addEventListener(eventName, function() {
                    this.classList.remove('dragover');
                });
            });
            
            detailImagesUpload.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    detailImagesInput.files = files;
                    
                    detailImagesGallery.innerHTML = '';
                    
                    const maxImages = 3;
                    const filesArray = Array.from(files).slice(0, maxImages);
                    filesArray.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const galleryItem = document.createElement('div');
                            galleryItem.className = 'image-gallery-item';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Neues Detailbild ' + (index + 1);
                            
                            const actionsDiv = document.createElement('div');
                            actionsDiv.className = 'image-gallery-actions';
                            
                            const removeAction = document.createElement('span');
                            removeAction.className = 'image-action';
                            removeAction.title = 'Entfernen';
                            removeAction.innerHTML = '<i class="fas fa-trash"></i>';
                            
                            removeAction.addEventListener('click', function() {
                                galleryItem.style.opacity = '0.3';
                                galleryItem.style.filter = 'grayscale(100%)';
                                
                                const hint = document.createElement('div');
                                hint.style.position = 'absolute';
                                hint.style.top = '0';
                                hint.style.left = '0';
                                hint.style.right = '0';
                                hint.style.bottom = '0';
                                hint.style.display = 'flex';
                                hint.style.alignItems = 'center';
                                hint.style.justifyContent = 'center';
                                hint.style.background = 'rgba(255,255,255,0.7)';
                                hint.style.color = '#e74c3c';
                                hint.style.fontWeight = 'bold';
                                hint.innerHTML = 'Wird entfernt';
                                
                                galleryItem.appendChild(hint);
                            });
                            
                            actionsDiv.appendChild(removeAction);
                            galleryItem.appendChild(img);
                            galleryItem.appendChild(actionsDiv);
                            detailImagesGallery.appendChild(galleryItem);
                        };
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
        }
        
        const removeButtons = document.querySelectorAll('.image-action');
        if (removeButtons.length > 0) {
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const galleryItem = this.closest('.image-gallery-item');
                    if (galleryItem) {
                        galleryItem.style.opacity = '0.3';
                        galleryItem.style.filter = 'grayscale(100%)';
                        
                        const hint = document.createElement('div');
                        hint.style.position = 'absolute';
                        hint.style.top = '0';
                        hint.style.left = '0';
                        hint.style.right = '0';
                        hint.style.bottom = '0';
                        hint.style.display = 'flex';
                        hint.style.alignItems = 'center';
                        hint.style.justifyContent = 'center';
                        hint.style.background = 'rgba(255,255,255,0.7)';
                        hint.style.color = '#e74c3c';
                        hint.style.fontWeight = 'bold';
                        hint.innerHTML = 'Wird entfernt';
                        
                        galleryItem.appendChild(hint);
                    }
                });
            });
        }
        
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(function() {
                    if (successAlert.parentNode) {
                        successAlert.parentNode.removeChild(successAlert);
                    }
                }, 500);
            }, 5000);
        }
    });
    
    function showPreview() {
        const formData = new FormData();
        
        formData.append('month', document.getElementById('month').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('format', document.getElementById('format').value);
        formData.append('material', document.getElementById('material').value);
        formData.append('look', document.getElementById('look').value);
        formData.append('surface', document.getElementById('surface').value);
        formData.append('properties', document.getElementById('properties').value);
        formData.append('usage', document.getElementById('usage').value);
        formData.append('floor_heating', document.getElementById('floor_heating').value);
        formData.append('availability', document.getElementById('availability').value);
        formData.append('features', document.getElementById('features').value);
        formData.append('old_price', document.getElementById('old_price').value);
        formData.append('new_price', document.getElementById('new_price').value);
        formData.append('saving', document.getElementById('saving').value);
        formData.append('detailed_description', document.getElementById('detailed_description').value);
        
        formData.append('current_main_image', '<?php echo htmlspecialchars($tile_data['main_image']); ?>');
        formData.append('current_detail_images', '<?php echo implode(',', $tile_data['detail_images']); ?>');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'preview.php';
        form.target = '_blank';
        form.style.display = 'none';
        
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
    </script>
</body>
</html>2