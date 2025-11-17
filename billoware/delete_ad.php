<?php
// delete_ad.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nur eingeloggt + Rolle 'user' oder 'admin'
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$isAdmin     = ($currentUser['role'] ?? '') === 'admin';
$userEmail   = $currentUser['email'] ?? '';

$id = $_GET['id'] ?? null;

if ($id && !empty($_SESSION['ads']) && is_array($_SESSION['ads'])) {
    foreach ($_SESSION['ads'] as $index => $ad) {
        if (($ad['id'] ?? null) === $id) {
            // Darf nur löschen, wenn:
            // - Admin ODER
            // - Besitzer der Anzeige
            $isOwner = ($ad['owner'] ?? '') === $userEmail;

            if ($isAdmin || $isOwner) {
                unset($_SESSION['ads'][$index]);
                // Indizes neu ordnen
                $_SESSION['ads'] = array_values($_SESSION['ads']);
            }
            break;
        }
    }
}

// Nach dem Löschen zurückleiten
if ($isAdmin) {
    header('Location: admin_panel.php');
} else {
    header('Location: dashboard.php');
}
exit;
