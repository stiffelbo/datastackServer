<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../models/UserModel.php';

function parseCookies(): array {
    $cookies = [];
    $header = $_SERVER['HTTP_COOKIE'] ?? '';

    foreach (explode(';', $header) as $pair) {
        $parts = explode('=', trim($pair), 2);
        if (count($parts) === 2) {
            $cookies[$parts[0]] = urldecode($parts[1]);
        }
    }

    return $cookies;
}

$controller = new Controller(getDB());

$cookies = parseCookies();
$userId = $cookies['id'] ?? null;
$token = $cookies['token'] ?? null;

if (!$userId || !$token) {
    $controller->json(['message' => 'Nie jesteś zalogowany'], 401);
} 

// 🔐 Zweryfikuj token
$model = new UserModel(getDB());
$user = $model->getByToken((int)$userId, $token);

if (!$user) {
    $controller->json(['message' => 'Błędny token lub użytkownik'], 401);
} else {
    // 🧹 Wyczyść token w bazie
    $model->update((int)$userId, ['token' => null]);

    // 🧽 Usuń ciasteczka
    $controller->clearAuthCookies();

    // ✅ Odpowiedź
    $controller->json(['message' => 'Wylogowano pomyślnie']);

}

