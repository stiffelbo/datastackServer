<?php

require_once __DIR__ . '/models/UserModel.php';

class Controller {
    protected PDO $db;
    protected ?array $user = null;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ===== REQUEST / INPUT =====

    public function getJsonData(): array {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->json(['error' => 'Niepoprawny JSON'], 400);
        }

        return $data;
    }

    public function requireLogin(): void {
        $cookies = $this->parseCookies();

        if (!isset($cookies['id'], $cookies['token'])) {
            $this->unauthorized('Brak ciasteczek sesyjnych');
        }

        $userModel = new UserModel($this->db);
        $user = $userModel->getByToken((int)$cookies['id'], $cookies['token']);

        if (!$user) {
            $this->unauthorized('Nieprawidłowy token lub użytkownik');
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            $this->forbidden('Użytkownik nieaktywny');
        }

        $this->user = $user;
    }

    public function requireRole(array $roles): void {
        if (!$this->user) {
            $this->unauthorized('Użytkownik nie zalogowany');
        }

        if (!in_array($this->user['role'], $roles)) {
            $this->forbidden('Brak uprawnień');
        }
    }

    public function parseCookies(): array {
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

    // ===== RESPONSE / OUTPUT =====

    public function json($data, int $code = 200): void {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    public function error(string $message, int $code = 500): void {
        $this->json(['error' => $message], $code);
    }

    public function unauthorized(string $msg = 'Unauthorized'): void {
        $this->error($msg, 401);
    }

    public function forbidden(string $msg = 'Forbidden'): void {
        $this->error($msg, 403);
    }

    public function badRequest(string $msg = 'Bad request'): void {
        $this->error($msg, 400);
    }

    public function notFound(string $msg = 'Not found'): void {
        $this->error($msg, 404);
    }

    // ===== USER CONTEXT =====

    public function getUser(): ?array {
        $cookies = $this->parseCookies();

        if (!isset($cookies['id'], $cookies['token'])) {
            return null;
        }

        $userModel = new UserModel($this->db);
        $user = $userModel->getByToken((int) $cookies['id'], $cookies['token']);

        $this->user = $user;
        return $this->user;
    }

    public function setAuthCookies(array $data, int $days = 30): void {
        $expire = time() + (86400 * $days);
        $options = ['path' => '/', 'expires' => $expire, 'httponly' => true, 'samesite' => 'Lax'];

        foreach ($data as $key => $value) {
            if (PHP_VERSION_ID >= 70300) {
                setcookie($key, $value, $options);
            } else {
                setcookie($key, $value, $expire, "/");
            }
        }
    }

    public function clearAuthCookies(): void {
        $keys = ['id', 'email', 'name', 'last_name', 'role', 'token', 'login_id'];
        foreach ($keys as $key) {
            setcookie($key, '', time() - 3600, '/');
        }
    }
}
