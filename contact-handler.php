<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Nur POST-Requests erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nur POST-Requests erlaubt']);
    exit;
}

// From-E-Mail intelligente Auswahl
function get_from_email() {
    $domain = $_SERVER['SERVER_NAME'];
    
    // Für die echte Domain (Live)
    if (strpos($domain, 'fliesen-runnebaum.net') !== false) {
        return 'kontakt@fliesen-runnebaum.net';
    }
    
    // Für Test-Domains (funktioniert weiterhin)
    return 'noreply@' . $domain;
}

// Konfiguration
$config = [
    'to_email' => 'bi7hop@googlemail.com', 
    'from_email' => get_from_email(),
    'reply_to_name' => 'Fliesen Runnebaum Kontaktformular',
    'subject_prefix' => '[Fliesen Runnebaum] Neue Anfrage - ',
    'max_message_length' => 5000,
    'honeypot_field' => 'website', 
    'rate_limit_minutes' => 5, 
];

session_start();
$client_ip = $_SERVER['REMOTE_ADDR'];
$last_submission_key = 'last_contact_' . md5($client_ip);

// Rate Limiting
if (isset($_SESSION[$last_submission_key])) {
    $time_diff = time() - $_SESSION[$last_submission_key];
    if ($time_diff < ($config['rate_limit_minutes'] * 60)) {
        http_response_code(429);
        echo json_encode([
            'success' => false, 
            'message' => 'Bitte warten Sie ' . $config['rate_limit_minutes'] . ' Minuten zwischen den Anfragen.'
        ]);
        exit;
    }
}

// Input-Daten sanitizen
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Honeypot-Check (Spam-Schutz)
if (!empty($_POST[$config['honeypot_field']])) {
    echo json_encode(['success' => true, 'message' => 'Nachricht gesendet']);
    exit;
}

// Pflichtfelder prüfen
$required_fields = ['name', 'email', 'message', 'privacy'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Das Feld '$field' ist erforderlich.";
    }
}

// E-Mail-Validierung
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
}

// Domain-Blacklist
$spam_domains = ['tempmail.org', '10minutemail.com', 'guerrillamail.com', 'mailinator.com'];
$email_domain = strtolower(substr(strrchr($email, "@"), 1));
if (in_array($email_domain, $spam_domains)) {
    $errors[] = 'Diese E-Mail-Domain ist nicht erlaubt.';
}

// Datenschutz-Checkbox prüfen
if ($_POST['privacy'] !== 'on') {
    $errors[] = 'Sie müssen der Datenschutzerklärung zustimmen.';
}

// Nachrichtenlänge prüfen
$message = sanitize_input($_POST['message']);
if (strlen($message) > $config['max_message_length']) {
    $errors[] = 'Die Nachricht ist zu lang (max. ' . $config['max_message_length'] . ' Zeichen).';
}

// Spam-Wörter Check
$spam_words = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'bitcoin', 'crypto'];
$message_lower = strtolower($message);
foreach ($spam_words as $spam_word) {
    if (strpos($message_lower, $spam_word) !== false) {
        echo json_encode(['success' => true, 'message' => 'Nachricht gesendet']);
        exit;
    }
}

// Bei Fehlern abbrechen
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Fehler bei der Validierung: ' . implode(' ', $errors)
    ]);
    exit;
}

// Daten für E-Mail vorbereiten
$name = sanitize_input($_POST['name']);
$phone = !empty($_POST['phone']) ? sanitize_input($_POST['phone']) : 'Nicht angegeben';
$subject_user = !empty($_POST['subject']) ? sanitize_input($_POST['subject']) : 'Allgemeine Anfrage';

// E-Mail-Betreff
$email_subject = $config['subject_prefix'] . $subject_user;

// TEXT-VERSION der E-Mail
function create_text_email($name, $email, $phone, $subject_user, $message, $client_ip) {
    return "FLIESEN RUNNEBAUM - Neue Kundenanfrage
" . str_repeat("=", 60) . "

KUNDENDATEN:
Name:        $name
E-Mail:      $email
Telefon:     $phone
Betreff:     $subject_user

NACHRICHT:
" . str_repeat("-", 40) . "
$message

TECHNISCHE DETAILS:
" . str_repeat("-", 40) . "
Eingegangen:    " . date('d.m.Y H:i:s') . "
IP-Adresse:     $client_ip
Website:        https://fliesen-runnebaum.net

SCHNELLE AKTIONEN:
Anrufen:        $phone
Antworten:      $email

" . str_repeat("=", 60) . "
Fliesen Runnebaum | Rouen Kamp 1, 49439 Steinfeld
Tel: 0176 / 10432567 | E-Mail: info@fliesen-runnebaum.net
" . str_repeat("=", 60);
}

// HTML-VERSION der E-Mail
function create_html_email($name, $email, $phone, $subject_user, $message, $client_ip) {
    return '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Kundenanfrage - Fliesen Runnebaum</title>
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #126e82 0%, #1a8fa8 100%); color: white; padding: 30px 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: bold;">🏠 Fliesen Runnebaum</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 16px;">Neue Kundenanfrage eingegangen</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">
            
            <!-- Kundendaten -->
            <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <h2 style="color: #126e82; font-size: 18px; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center;">
                    <span style="color: #d96941; margin-right: 10px; font-size: 20px;">●</span>
                    Kundendaten
                </h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; width: 120px; vertical-align: top;">Name:</td>
                        <td style="padding: 8px 0; color: #333;"><strong>' . htmlspecialchars($name) . '</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; vertical-align: top;">E-Mail:</td>
                        <td style="padding: 8px 0; color: #333;"><a href="mailto:' . htmlspecialchars($email) . '" style="color: #126e82; text-decoration: none;">' . htmlspecialchars($email) . '</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; vertical-align: top;">Telefon:</td>
                        <td style="padding: 8px 0; color: #333;"><a href="tel:' . htmlspecialchars($phone) . '" style="color: #126e82; text-decoration: none;">' . htmlspecialchars($phone) . '</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; vertical-align: top;">Betreff:</td>
                        <td style="padding: 8px 0; color: #333;">' . htmlspecialchars($subject_user) . '</td>
                    </tr>
                </table>
            </div>
            
            <!-- Nachricht -->
            <div style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <h2 style="color: #126e82; font-size: 18px; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center;">
                    <span style="color: #d96941; margin-right: 10px; font-size: 20px;">●</span>
                    Nachricht
                </h2>
                <div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #126e82; border-radius: 0 5px 5px 0; margin: 15px 0;">
                    ' . nl2br(htmlspecialchars($message)) . '
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="margin: 25px 0; text-align: center;">
                <a href="mailto:' . htmlspecialchars($email) . '?subject=Re: ' . htmlspecialchars($subject_user) . '" 
                   style="display: inline-block; padding: 12px 24px; margin: 5px 10px; background: #126e82; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                    📧 Direkt antworten
                </a>
                <a href="tel:' . htmlspecialchars($phone) . '" 
                   style="display: inline-block; padding: 12px 24px; margin: 5px 10px; background: #d96941; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                    📞 Anrufen
                </a>
            </div>
            
            <!-- Technische Details -->
            <div style="margin-bottom: 0;">
                <h2 style="color: #126e82; font-size: 18px; font-weight: bold; margin-bottom: 15px; display: flex; align-items: center;">
                    <span style="color: #d96941; margin-right: 10px; font-size: 20px;">●</span>
                    Technische Details
                </h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; width: 120px;">Eingegangen:</td>
                        <td style="padding: 8px 0; color: #333;">' . date('d.m.Y H:i:s') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">IP-Adresse:</td>
                        <td style="padding: 8px 0; color: #333;">' . htmlspecialchars($client_ip) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Website:</td>
                        <td style="padding: 8px 0; color: #333;">fliesen-runnebaum.net</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; border-top: 1px solid #eee;">
            <strong style="color: #126e82;">Fliesen Runnebaum</strong><br>
            Rouen Kamp 1, 49439 Steinfeld<br>
            📞 0176 / 10432567 | ✉ info@fliesen-runnebaum.net
        </div>
    </div>
</body>
</html>';
}

// MULTIPART E-MAIL VERSENDEN
function send_multipart_email($to_email, $subject, $name, $email, $phone, $subject_user, $message, $client_ip, $from_email) {
    
    // Text-Version erstellen
    $text_body = create_text_email($name, $email, $phone, $subject_user, $message, $client_ip);
    
    // HTML-Version erstellen
    $html_body = create_html_email($name, $email, $phone, $subject_user, $message, $client_ip);
    
    // Multipart-Boundary erstellen
    $boundary = uniqid('boundary_');
    
    // Headers für Multipart-E-Mail
    $headers = [
        'From: ' . $from_email,
        'Reply-To: ' . $email . ' (' . $name . ')',
        'Return-Path: ' . $from_email,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'Message-ID: <' . uniqid() . '@' . $_SERVER['SERVER_NAME'] . '>',
        'Date: ' . date('r'),
        'X-Priority: 3'
    ];
    
    // Multipart-Body zusammenbauen
    $multipart_body = "This is a multi-part message in MIME format.\n\n";
    
    // Text-Part
    $multipart_body .= "--{$boundary}\n";
    $multipart_body .= "Content-Type: text/plain; charset=UTF-8\n";
    $multipart_body .= "Content-Transfer-Encoding: 8bit\n\n";
    $multipart_body .= $text_body . "\n\n";
    
    // HTML-Part
    $multipart_body .= "--{$boundary}\n";
    $multipart_body .= "Content-Type: text/html; charset=UTF-8\n";
    $multipart_body .= "Content-Transfer-Encoding: 8bit\n\n";
    $multipart_body .= $html_body . "\n\n";
    
    // Boundary beenden
    $multipart_body .= "--{$boundary}--";
    
    // E-Mail senden
    return mail($to_email, $subject, $multipart_body, implode("\r\n", $headers));
}

// E-MAIL VERSENDEN
$mail_sent = send_multipart_email(
    $config['to_email'],
    $email_subject,
    $name,
    $email,
    $phone,
    $subject_user,
    $message,
    $client_ip,
    $config['from_email']
);

if ($mail_sent) {
    // Rate Limiting aktualisieren
    $_SESSION[$last_submission_key] = time();
    
    error_log("Kontaktformular: HTML-E-Mail erfolgreich versendet von $email");
    
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für Ihre Nachricht! Wir werden uns schnellstmöglich bei Ihnen melden.'
    ]);
} else {
    error_log("Kontaktformular: Fehler beim E-Mail-Versand von $email");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ein technischer Fehler ist aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns telefonisch unter 0176 / 10432567.'
    ]);
}
?>