<?php
// data/users.php

// Einfache Fake-User-Liste (später kann das in eine Datenbank wandern)
$users = [
    [
        'email' => 'user@billoware.at',
        'password' => 'user123',   // für den Anfang Klartext, später mit password_hash()
        'role' => 'user'
    ],
    [
        'email' => 'admin@billoware.at',
        'password' => 'admin123',
        'role' => 'admin'
    ],
];
