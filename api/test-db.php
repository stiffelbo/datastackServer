<?php
require_once '../bootstrap.php';

$db = getDB();
echo json_encode(['message' => 'Połączenie z bazą działa!']);