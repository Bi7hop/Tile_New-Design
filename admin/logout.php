<?php
require_once 'config.php';

// Session beenden
session_unset();
session_destroy();

// Weiterleitung zur Login-Seite
header('Location: index.php');
exit;
?>