<?php
// logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Alle Session-Daten löschen
$_SESSION = [];
session_destroy();

// Zurück auf die Startseite
header('Location: index.php');
exit;