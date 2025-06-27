<?php
header('Content-Type: text/plain; charset=utf-8');
require_once 'bootstrap.php';

function migrateSchema(string $schemaPath, PDO $db): void {
  $name = basename($schemaPath);
  if (!file_exists($schemaPath)) {
    echo "❌ Nie znaleziono pliku: $name\n";
    return;
  }

  $executed = $db->query("SELECT name FROM migrations WHERE name = " . $db->quote($name))->fetchColumn();
  if ($executed) {
    echo "⏭  Pominięto (już wykonana): $name\n";
    return;
  }

  $schema = require $schemaPath;
  $table = $schema['table'];
  $fields = $schema['fields'];

  $cols = [];
  foreach ($fields as $col => $opts) {
    $sql = "`$col` " . $opts['type'];
    if (isset($opts['default'])) {
      $sql .= " DEFAULT " . $opts['default'];
    }
    $cols[] = $sql;
  }

  $sql = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(", ", $cols) . ")";

  try {
      $result = $db->exec($sql);

      if ($result !== false) {
          $stmt = $db->prepare("INSERT INTO migrations (name) VALUES (:name)");
          $stmt->execute(['name' => $name]);
          echo "✅ Zmigrowano: $name\n";
      } else {
          // exec zwrócił false, ale bez wyjątku – sprawdź errorInfo
          $error = $db->errorInfo();
          echo "❌ Błąd migracji tabeli: $name\n";
          echo "SQL: $sql\n";
          echo "SQLSTATE: {$error[0]}\n";
          echo "Driver Error: {$error[2]}\n";
      }

  } catch (PDOException $e) {
      echo "❌ Wyjątek przy migracji tabeli: $name\n";
      echo "SQL: $sql\n";
      echo "Błąd: " . $e->getMessage() . "\n";
  }
}

function runMigrationFile(string $path, PDO $db): void {
  $name = basename($path);
  if (!file_exists($path)) {
    echo "❌ Brak migracji: $name\n";
    return;
  }

  $executed = $db->query("SELECT name FROM migrations WHERE name = " . $db->quote($name))->fetchColumn();
  if ($executed) {
    echo "⏭  Pominięto migrację plikową: $name\n";
    return;
  }

  include $path;

  $stmt = $db->prepare("INSERT INTO migrations (name) VALUES (:name)");
  $stmt->execute(['name' => $name]);

  echo "✅ Wykonano migrację plikową: $name\n";
}

// === MIGRACJE START ===

$db = getDB();

// Migrations table first
$db->exec("
  CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Zdefiniowane migracje w ustalonej kolejności:
migrateSchema(__DIR__ . '/schema/UserSchema.php', $db);
runMigrationFile(__DIR__ . '/migrations/InitAdmin.php', $db);
migrateSchema(__DIR__ . '/schema/ClockifySchema.php', $db);
runMigrationFile(__DIR__ . '/migrations/ClockifyAlter.php', $db);