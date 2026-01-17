<?php
declare(strict_types=1);

// Session starten (nur wenn noch keine läuft)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Speichert minimalen User in $_SESSION (id/email/role/name)
function login_user(array $user): void
{
    // minimaler Session-User
    $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'email' => (string)$user['email'],
        'role'  => (string)($user['role'] ?? 'user'),
        'name'  => (string)($user['name'] ?? ''),
    ];
}

// Session löschen = logout
function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}


// Flash
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function flash_get(): ?array
{
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}