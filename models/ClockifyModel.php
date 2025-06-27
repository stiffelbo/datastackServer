<?php
require_once 'Model.php';

class ClockifyModel extends Model {
    public function __construct(PDO $db) {
        $schema = require __DIR__ . '/../schema/ClockifySchema.php';
        parent::__construct($db, $schema);
    }

    private function prepareSelectQuery(): string {
        $select = $this->buildSelectFields();
        $sql = "SELECT $select, ";
        $sql .= " CONCAT(u.name, ' ', u.last_name) as createdBy";
        $sql .= " FROM {$this->table}";
        $sql .= " JOIN users u ON {$this->table}.user_id = u.id";
        return $sql;
    }

    public function getAll(): array {
        $q = $this->prepareSelectQuery();
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}