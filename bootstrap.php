<?php
// Dynamiczne dopasowanie do originu
$allowedOrigins = [
    'http://localhost:8080',
    'http://192.168.1.135', // dodaj tu inne jeśli potrzebujesz
    'http://192.168.1.135/datastack', // dodaj tu inne jeśli potrzebujesz
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}


//register error handling
require_once __DIR__ . '/errorHandler.php';
registerErrorHandling();

// Autoload + połączenie z bazą
require_once __DIR__ . '/models/Database.php';

function getDB() {
    $database = new Database();
    return $database->connect();
}