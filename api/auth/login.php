<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../models/UserModel.php';


$controller = new Controller(getDB());

// Odbierz dane
$data = $controller->getJsonData();
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;




if (!$email || !$password) {
    $controller->json(['error' => 'Email i hasło są wymagane'], 400);
}

// Pobierz użytkownika po emailu
$model = new UserModel(getDB());
$user = $model->findByEmail($email);

if (!$user || !password_verify($password, $user['password_hash'])) {
    $controller->json(['error' => 'Nieprawidłowy login lub hasło'], 401);
}

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
unset($user['password_hash']);
$user['token'] = $token;

$controller->json(['user' => $user]);
