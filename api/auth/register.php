<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../models/UserModel.php';

$controller = new Controller(getDB());
$data = $controller->getJsonData();

// 🧪 Walidacja
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$name = trim($data['name'] ?? '');
$lastName = trim($data['last_name'] ?? '');

if (!$email || !$password || !$name || !$lastName) {
    $controller->badRequest('Wszystkie pola są wymagane');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $controller->badRequest('Niepoprawny adres email');
}

// 🔐 Hashuj hasło
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// 📦 Dane do zapisu
$userData = [
    'email' => $email,
    'password_hash' => $passwordHash,
    'name' => $name,
    'last_name' => $lastName,
    'role' => 'viewer', // 👤 Zawsze viewer przy rejestracji
];

// 🔁 Zapisz użytkownika (tylko jeśli nie istnieje)
$model = new UserModel(getDB());
$existing = $model->findByEmail($email);

if ($existing) {
    $controller->badRequest('Użytkownik z takim emailem już istnieje');
}

$userId = $model->create($userData);

if (!$userId) {
    $controller->error('Nie udało się utworzyć konta');
}

// 🔄 Pobierz i zwróć dane (bez hasła)
$user = $model->getById($userId);

//zaloguj użytkownika od razu:
$token = bin2hex(random_bytes(16));
$now = date('Y-m-d H:i:s');

$model->update($user['id'], [
    'token' => $token,
    'last_login_at' => $now
]);

// Ustaw cookie
$controller->setAuthCookies([
    'id' => $user['id'],
    'email' => $user['email'],
    'name' => $user['name'],
    'last_name' => $user['last_name'],
    'role' => $user['role'],
    'last_login_at' => $now,
    'token' => $token
]);

// Usuń hasło z odpowiedzi
$user['token'] = $token;

$controller->json(['user' => $user]);
