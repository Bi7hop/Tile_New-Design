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
    if (strpos($domain, 'fliesen-runnebaum.net') !== false) {
        return 'kontakt@fliesen-runnebaum.net';
    }
    return 'noreply@' . $domain;
}

// Konfiguration
$config = [
    'to_email' => 'runnebaum.fliesentechnik@gmail.com', 
    'from_email' => get_from_email(),
    'reply_to_name' => 'Fliesen Runnebaum Kontaktformular',
    'subject_prefix' => '[Anfrage] ',
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

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Honeypot-Check
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

$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
}

$spam_domains = ['tempmail.org', '10minutemail.com', 'guerrillamail.com', 'mailinator.com'];
$email_domain = strtolower(substr(strrchr($email, "@"), 1));
if (in_array($email_domain, $spam_domains)) {
    $errors[] = 'Diese E-Mail-Domain ist nicht erlaubt.';
}

if ($_POST['privacy'] !== 'on') {
    $errors[] = 'Sie müssen der Datenschutzerklärung zustimmen.';
}

$message = sanitize_input($_POST['message']);
if (strlen($message) > $config['max_message_length']) {
    $errors[] = 'Die Nachricht ist zu lang (max. ' . $config['max_message_length'] . ' Zeichen).';
}

$spam_words = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'bitcoin', 'crypto'];
$message_lower = strtolower($message);
foreach ($spam_words as $spam_word) {
    if (strpos($message_lower, $spam_word) !== false) {
        echo json_encode(['success' => true, 'message' => 'Nachricht gesendet']);
        exit;
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Fehler bei der Validierung: ' . implode(' ', $errors)
    ]);
    exit;
}

$name = sanitize_input($_POST['name']);
$phone = !empty($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$subject_user = !empty($_POST['subject']) ? sanitize_input($_POST['subject']) : 'Allgemeine Anfrage';

// Betreff-Labels
$subject_labels = [
    'badezimmer' => 'Badezimmer',
    'kueche' => 'Küche',
    'wohnbereich' => 'Wohnbereich',
    'terrasse' => 'Terrasse/Balkon',
    'fussbodenheizung' => 'Fußbodenheizung',
    'sonstiges' => 'Sonstiges'
];
$subject_display = isset($subject_labels[$subject_user]) ? $subject_labels[$subject_user] : $subject_user;

$email_subject = $config['subject_prefix'] . $name . ' - ' . $subject_display;

// TEXT-VERSION
function create_text_email($name, $email, $phone, $subject_display, $message, $client_ip) {
    $phone_line = $phone ? "\nTelefon: $phone" : "";
    return "NEUE ANFRAGE - FLIESEN RUNNEBAUM
=====================================

Von: $name
E-Mail: $email$phone_line
Betreff: $subject_display

Nachricht:
-------------------------------------
$message
-------------------------------------

Eingegangen: " . date('d.m.Y, H:i') . " Uhr
IP: $client_ip";
}

// HTML-VERSION - Hell, Clean, funktioniert überall
function create_html_email($name, $email, $phone, $subject_display, $message, $client_ip) {
    $date = date('d.m.Y');
    $time = date('H:i');
    $has_phone = !empty($phone);
    
    $phone_row = '';
    if ($has_phone) {
        $phone_row = '
                                <tr>
                                    <td style="padding: 8px 0; color: #666666; font-size: 14px; border-bottom: 1px solid #eeeeee;">Telefon</td>
                                    <td style="padding: 8px 0; padding-left: 15px; border-bottom: 1px solid #eeeeee;">
                                        <a href="tel:' . htmlspecialchars($phone) . '" style="color: #333333; text-decoration: none; font-size: 14px; font-weight: 600;">' . htmlspecialchars($phone) . '</a>
                                    </td>
                                </tr>';
    }
    
    $call_button = $has_phone ? 
        '<a href="tel:' . htmlspecialchars($phone) . '" style="display: inline-block; padding: 12px 28px; background-color: #D4A574; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin-left: 10px;">📞 Anrufen</a>' :
        '';

    return '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Anfrage</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #333333; background-color: #f4f4f4;">
    
    <!-- Wrapper -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td style="padding: 30px 15px;">
                
                <!-- Container -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 550px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #2C5545; padding: 20px 25px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <span style="color: #ffffff; font-size: 18px; font-weight: bold;">Fliesen Runnebaum</span>
                                    </td>
                                    <td align="right">
                                        <span style="color: rgba(255,255,255,0.8); font-size: 13px;">' . $date . '</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Betreff-Zeile -->
                    <tr>
                        <td style="background-color: #D4A574; padding: 12px 25px;">
                            <span style="color: #ffffff; font-size: 13px; font-weight: 600;">📋 ' . htmlspecialchars($subject_display) . '</span>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 25px;">
                            
                            <!-- Kundenname -->
                            <h1 style="margin: 0 0 20px 0; font-size: 24px; font-weight: bold; color: #222222;">' . htmlspecialchars($name) . '</h1>
                            
                            <!-- Kontaktdaten Tabelle -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 8px 0; color: #666666; font-size: 14px; border-bottom: 1px solid #eeeeee; width: 80px;">E-Mail</td>
                                    <td style="padding: 8px 0; padding-left: 15px; border-bottom: 1px solid #eeeeee;">
                                        <a href="mailto:' . htmlspecialchars($email) . '" style="color: #2C5545; text-decoration: none; font-size: 14px; font-weight: 600;">' . htmlspecialchars($email) . '</a>
                                    </td>
                                </tr>
                                ' . $phone_row . '
                                <tr>
                                    <td style="padding: 8px 0; color: #666666; font-size: 14px;">Uhrzeit</td>
                                    <td style="padding: 8px 0; padding-left: 15px; color: #333333; font-size: 14px;">' . $time . ' Uhr</td>
                                </tr>
                            </table>
                            
                            <!-- Nachricht -->
                            <div style="background-color: #f9f9f9; border-left: 4px solid #D4A574; padding: 20px; margin-bottom: 25px; border-radius: 0 6px 6px 0;">
                                <p style="margin: 0 0 8px 0; font-size: 12px; color: #888888; text-transform: uppercase; letter-spacing: 0.5px;">Nachricht</p>
                                <p style="margin: 0; font-size: 15px; color: #333333; line-height: 1.7; white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</p>
                            </div>
                            
                            <!-- Buttons -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <a href="mailto:' . htmlspecialchars($email) . '?subject=Re: Ihre Anfrage - ' . htmlspecialchars($subject_display) . '" style="display: inline-block; padding: 12px 28px; background-color: #2C5545; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">✉️ Antworten</a>
                                        ' . $call_button . '
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9f9f9; padding: 15px 25px; border-top: 1px solid #eeeeee;">
                            <span style="font-size: 11px; color: #999999;">IP: ' . htmlspecialchars($client_ip) . ' · fliesen-runnebaum.net</span>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>';
}

// MULTIPART E-MAIL VERSENDEN
function send_multipart_email($to_email, $subject, $name, $email, $phone, $subject_display, $message, $client_ip, $from_email) {
    $text_body = create_text_email($name, $email, $phone, $subject_display, $message, $client_ip);
    $html_body = create_html_email($name, $email, $phone, $subject_display, $message, $client_ip);
    
    $boundary = uniqid('boundary_');
    
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
    
    $multipart_body = "This is a multi-part message in MIME format.\n\n";
    $multipart_body .= "--{$boundary}\n";
    $multipart_body .= "Content-Type: text/plain; charset=UTF-8\n";
    $multipart_body .= "Content-Transfer-Encoding: 8bit\n\n";
    $multipart_body .= $text_body . "\n\n";
    $multipart_body .= "--{$boundary}\n";
    $multipart_body .= "Content-Type: text/html; charset=UTF-8\n";
    $multipart_body .= "Content-Transfer-Encoding: 8bit\n\n";
    $multipart_body .= $html_body . "\n\n";
    $multipart_body .= "--{$boundary}--";
    
    return mail($to_email, $subject, $multipart_body, implode("\r\n", $headers));
}

$mail_sent = send_multipart_email(
    $config['to_email'],
    $email_subject,
    $name,
    $email,
    $phone,
    $subject_display,
    $message,
    $client_ip,
    $config['from_email']
);

if ($mail_sent) {
    $_SESSION[$last_submission_key] = time();
    error_log("Kontaktformular: E-Mail erfolgreich versendet von $email");
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