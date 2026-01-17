<?php
// Test ob Verbindung zur DB klappt (nur zum testen)
require_once __DIR__ . '/../utils/db.php';
echo "OK verbunden. MySQL Version: " . db()->getAttribute(PDO::ATTR_SERVER_VERSION);
