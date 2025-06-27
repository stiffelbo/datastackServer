<?php

require_once 'Model.php';

class UserModel extends Model {
    public function __construct(PDO $db) {
        $schema = require __DIR__ . '/../schema/UserSchema.php';
        parent::__construct($db, $schema);
    }

    // Auth methods
   public function findByEmail(string $email): ?array {
        $select = $this->buildSelectFields();
        $sql = "SELECT $select FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function getByToken(int $id, string $token): ?array {
        $select = $this->buildSelectFields(['password_hash']);
        $sql = "SELECT {$select} FROM {$this->table} WHERE id = :id AND token = :token LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id, 'token' => $token]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getMe(string $token): ?array{
        $select = $this->buildSelectFields(['password_hash']);
        $sql = "SELECT {$select} FROM {$this->table} WHERE token = :token LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['token' => $token]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getById(int $id): ?array{
        $select = $this->buildSelectFields(['password_hash']);
        $sql = "SELECT {$select} FROM {$this->table} WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    // Możesz dodawać kolejne metody: findByEmail, checkPassword, itd.
}
