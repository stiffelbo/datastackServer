<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'datastack';
    private $username = 'root';      // zmień jeśli nie używasz root
    private $password = '';          // dopasuj do swojego XAMPP-a Produkcja N0de2025!
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Połączenie z bazą nieudane: ' . $e->getMessage()]));
        }

        return $this->conn;
    }
}