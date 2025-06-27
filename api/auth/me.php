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

$token = $cookies['token'] ?? null;

//pobierz dane użytkownika po tokenie
$model = new UserModel(getDB());
$user = $model->getMe($token);

if ($user) {
    $responseData = [
        "userData" => $user,
        "pages" => [
            ["name" => 'clockify', 'label' => 'Wgraj Clockify', 'group' => 'reports'],
            ["name" => 'clockifyData', 'label' => 'Dane Clockify', 'group' => 'reports'],
            ["name" => 'structure', 'label' => 'Struktura', 'group' => 'hr'],
            ["name" => 'employees', 'label' => 'Pracownicy', 'group' => 'hr'],
            ["name" => 'salaries', 'label' => 'Wynagrodzenia', 'group' => 'hr'],
            ["name" => 'users', 'label' => 'Użytkownicy', 'group' => 'admin'],
            ["name" => 'pages', 'label' => 'Strony', 'group' => 'admin'],
            ["name" => 'accessusers', 'label' => 'Dostępy Użytkowników', 'group' => 'admin'],
            ["name" => 'periods', 'label' => 'Okresy', 'group' => 'controlling'],
            ["name" => 'deptcosts', 'label' => 'Koszty Wydziałowe', 'group' => 'controlling'],
        ] // TODO: pobierz strony/uprawnienia
    ];

    echo json_encode($responseData);
    exit;
}

// Brak autoryzacji
http_response_code(401);
echo json_encode(["message" => "Nieautoryzowany"]);
exit;

