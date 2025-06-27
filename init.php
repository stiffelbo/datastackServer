<?php
require_once 'bootstrap.php';

$db = getDB();

// Tylko to — twarde zapewnienie, że 'migrations' istnieje
$db->exec("
  CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

echo json_encode(['status' => 'OK', 'message' => 'Tabela migrations istnieje']);
