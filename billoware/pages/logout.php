<?php
// Session helper laden und Session löschen
require_once __DIR__ . '/../utils/session.php';

logout_user();
header('Location: /billoware/pages/index.php');
exit;
