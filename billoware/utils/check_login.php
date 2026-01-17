<?php
// check_login: muss eingeloggt sein + wenn DB says "blocked" -> sofort ausloggen
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../services/user_service.php';

if (!isset($_SESSION['user'])) {
    header('Location: /billoware/pages/login.php');
    exit;
}

$u = user_find_by_email($_SESSION['user']['email'] ?? '');
if (!$u || (int)($u['is_blocked'] ?? 0) === 1) {
    // sofort rauswerfen
    logout_user();
    header('Location: /billoware/pages/login.php');
    exit;
}