<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../models/ClockifyModel.php';
require_once __DIR__ . '/../../models/UserModel.php';

$controller = new Controller(getDB());
// 📦 Modele
$userModel = new UserModel(getDB());
$clockifyModel = new ClockifyModel(getDB());

$data = $controller->getJsonData();
$user = $controller->getUser();
$userId = (int) $user['id'];

// 🧪 Walidacja
if (!is_array($data) || empty($data)) {
    $controller->json(['error' => 'Niepoprawny format danych lub pusta paczka'], 400);
}

// 🔄 Przetwarzanie danych
$mapped = [];

foreach ($data as $row) {
    $entry = [
        'project' => $row['project'] ?? '',
        'client' => $row['client'] ?? '',
        'description' => $row['description'] ?? '',
        'task' => $row['task'] ?? '',
        'parrentTask' => $row['parrentTask'] ?? '',
        'user' => $row['user'] ?? '',
        'group' => $row['group'] ?? '',
        'email' => $row['email'] ?? '',
        'tags' => $row['tags'] ?? '',
        'billable' => isset($row['billable']) ? (int) $row['billable'] : 0,
        'start_date' => $row['start_date'] ?? null,
        'start_time' => $row['start_time'] ?? null,
        'end_date' => $row['end_date'] ?? null,
        'end_time' => $row['end_time'] ?? null,
        'duration_h' => $row['duration_h'] ?? null,
        'duration_decimal' => isset($row['duration_decimal']) ? (float)$row['duration_decimal'] : 0,
        'billable_rate_pln' => isset($row['billable_rate_pln']) ? (float)$row['billable_rate_pln'] : 0,
        'billable_amount_pln' => isset($row['billable_amount_pln']) ? (float)$row['billable_amount_pln'] : 0,
        'user_id' => $userId,
        'period_id' => 1,
        'structure_id' => 1
    ];

    $mapped[] = $entry;
}

// 🧩 Wstaw do bazy
$success = $clockifyModel->createMany($mapped);

if ($success) {
    $controller->json(['message' => 'Zaimportowano', 'count' => count($data)]);
} else {
    $controller->json(['error' => 'Nie udało się zapisać danych'], 500);
}
