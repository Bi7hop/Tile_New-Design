<?php
/**
 * Admin-Funktionen für Fliesen des Monats
 */

/**
 * Aktuelle Fliese des Monats laden
 */
function get_current_tile() {
    if (!file_exists(TILE_DATA_FILE)) {
        return get_default_tile_data();
    }
    
    $json = file_get_contents(TILE_DATA_FILE);
    $data = json_decode($json, true);
    
    if (!$data) {
        return get_default_tile_data();
    }
    
    return $data;
}

/**
 * Fliese des Monats speichern
 */
function save_tile_data($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if ($json === false) {
        return false;
    }
    
    return file_put_contents(TILE_DATA_FILE, $json, LOCK_EX) !== false;
}

/**
 * Standard-Daten für Fliese des Monats
 */
function get_default_tile_data() {
    return [
        'month' => 'Dezember 2025',
        'title' => 'XXL-Betonoptik Fliesen',
        'description' => 'Moderne Großformatfliesen in zeitloser Betonoptik. Ideal für Wohnräume und Badezimmer. Minimale Fugen für ein großzügiges Raumgefühl.',
        'main_image' => 'default-tile.jpg',
        'detail_images' => [],
        'features' => ['120×60cm', 'Frostsicher', 'Fußbodenheizung'],
        'old_price' => '59,95',
        'new_price' => '49,95',
        'saving' => '10,00',
        'format' => '120×60 cm',
        'material' => 'Feinsteinzeug',
        'look' => 'Betonoptik',
        'surface' => 'Matt',
        'properties' => 'Rutschfest, Frostsicher',
        'usage' => 'Innen und Außen',
        'floor_heating' => 'Geeignet',
        'availability' => 'Sofort verfügbar',
        'detailed_description' => [
            'Diese moderne Großformatfliese in Betonoptik verleiht jedem Raum einen zeitgemäßen, urbanen Charakter.',
            'Die matte Oberfläche und die minimalen Fugen sorgen für ein großzügiges Raumgefühl.'
        ]
    ];
}

/**
 * Bild hochladen
 */
function upload_image($file, $target_dir) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload-Fehler: ' . $file['error']];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Datei ist zu groß (max. 5 MB)'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Ungültiger Dateityp. Nur JPG, PNG und GIF erlaubt.'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('tile_') . '.' . $extension;
    $target_file = $target_dir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => false, 'message' => 'Fehler beim Speichern der Datei'];
    }
    
    return ['success' => true, 'filename' => $filename];
}

/**
 * Monatsliste generieren
 */
function get_month_options() {
    $months = [];
    $current_year = date('Y');
    $next_year = $current_year + 1;
    
    $month_names = [
        'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
    ];
    
    foreach ($month_names as $month) {
        $months["$month $current_year"] = "$month $current_year";
        $months["$month $next_year"] = "$month $next_year";
    }
    
    return $months;
}
?>