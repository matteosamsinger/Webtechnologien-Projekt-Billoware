<?php
// check_admin: nur eingeloggter Admin darf weiter
require_once __DIR__ . '/session.php';

if (!isset($_SESSION['user']) || (($_SESSION['user']['role'] ?? '') !== 'admin')) {
    header('Location: /billoware/pages/login.php');
    exit;
}
