<?php

class Model {
    protected PDO $conn;
    protected array $schema;
    protected string $table;

    // ważne dla typów

    public function __construct(PDO $db, array $schema) {
        $this->conn = $db;
        $this->schema = $schema;
        $this->table = $schema['table'];

        // 🛠️ Ustawiamy tryb fetchowania i typów
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function getLastInsertId(): int {
        return (int) $this->conn->lastInsertId();
    }

    public function create(array $data): ?int {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ")
                VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }

        if ($stmt->execute()) {
            return $this->getLastInsertId();
        }

        return null;
    }

    public function createDistinct(array $data, array $fields): ?int {
        // 1. Budujemy WHERE
        $where = [];
        $params = [];
        foreach ($fields as $field) {
            $where[] = "`$field` = :$field";
            $params[$field] = $data[$field];
        }

        $sql = "SELECT id FROM {$this->table} WHERE " . implode(' AND ', $where) . " LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return null; // już istnieje, nie tworzymy
        }

        // 2. Dodajemy nowy
        return $this->create($data); // zwraca id lub null
    }

    public function createMany(array $rows, int $chunkSize = 500): bool {
        if (empty($rows)) return false;

        $allSucceeded = true;

        foreach (array_chunk($rows, $chunkSize) as $chunkIndex => $chunk) {
            $fields = array_keys($chunk[0]);
            $placeholders = [];
            $params = [];

            foreach ($chunk as $i => $row) {
                $rowPlaceholders = [];
                foreach ($fields as $field) {
                    $key = ":{$field}_{$chunkIndex}_{$i}";
                    $rowPlaceholders[] = $key;
                    $params[$key] = $row[$field];
                }
                $placeholders[] = '(' . implode(',', $rowPlaceholders) . ')';
            }

            $escapedFields = array_map(fn($f) => "`$f`", $fields);
            $sql = "INSERT INTO `{$this->table}` (" . implode(',', $escapedFields) . ") VALUES " . implode(',', $placeholders);

            $stmt = $this->conn->prepare($sql);

            if (!$stmt->execute($params)) {
                $allSucceeded = false;
            }
        }

        return $allSucceeded;
    }

    public function update(int $id, array $data): bool {
        $set = [];
        foreach ($data as $field => $value) {
            $set[] = "`$field` = :$field";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function buildSelectFields(array $exclude = []): string {
        $fields = [];

        foreach ($this->schema['fields'] as $field => $_) {
            if (in_array($field, $exclude)) continue;

            $escapedField = "`{$this->table}`.`$field`";
            $alias = "`$field`";
            $fields[] = "$escapedField AS $alias";
        }

        return implode(', ', $fields);
    }

}
