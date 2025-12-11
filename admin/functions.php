<?php
// Funktion, um die aktuelle Fliese des Monats zu laden
function get_current_tile() {
    if (file_exists(TILE_DATA_FILE)) {
        $data = file_get_contents(TILE_DATA_FILE);
        return json_decode($data, true);
    }
    
    // Standard-Werte, falls keine Datei existiert
    return [
        'month' => date('F Y'),
        'title' => 'XXL-Betonoptik Fliesen',
        'description' => 'Moderne Großformatfliesen in zeitloser Betonoptik.',
        'format' => '120×60cm',
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

// Funktion, um die Fliese des Monats zu speichern
function save_tile_data($data) {
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
    
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents(TILE_DATA_FILE, $json) !== false;
}

// Funktion zum sicheren Hochladen von Bildern
function upload_image($file, $target_dir) {
    // Erstelle Upload-Verzeichnis, falls es nicht existiert
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Überprüfen, ob es ein Bild ist
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'Die Datei ist kein Bild.'];
    }
    
    // Nur bestimmte Bildformate zulassen
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Nur JPG, PNG und GIF-Dateien sind erlaubt.'];
    }
    
    // Dateiname generieren
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . '/' . $unique_filename;
    
    // Datei hochladen
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            'success' => true, 
            'filename' => $unique_filename,
            'path' => $target_file
        ];
    } else {
        return ['success' => false, 'message' => 'Fehler beim Hochladen der Datei.'];
    }
}

// Funktion zum Erstellen des Monatsnamens (z.B. "Mai 2025")
function get_month_options() {
    $months = [
        '01' => 'Januar',
        '02' => 'Februar',
        '03' => 'März',
        '04' => 'April',
        '05' => 'Mai',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'August',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Dezember'
    ];
    
    $current_year = date('Y');
    $years = [$current_year, $current_year + 1];
    
    $options = [];
    
    foreach ($years as $year) {
        foreach ($months as $month_num => $month_name) {
            $value = $month_name . ' ' . $year;
            $options[$value] = $value;
        }
    }
    
    return $options;
}
?>