<?php

/** @var PDO $db */
if (!isset($db)) {
    die("❌ Brak obiektu PDO (baza danych). Upewnij się, że \$db jest zainicjalizowane.\n");
}

$table = 'clockify';
$indexName = 'unique_clockify_entry';

// Sprawdź, czy indeks już istnieje
$existing = $db->query("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE table_schema = DATABASE()
      AND table_name = '$table' 
      AND index_name = '$indexName'
")->fetchColumn();

if ($existing > 0) {
    echo "⏭  Pominięto — indeks '$indexName' już istnieje.\n";
    return;
}

try {
    $sql = "
        ALTER TABLE `$table`
        ADD UNIQUE KEY `$indexName` (
            `user`,
            `email`,
            `start_date`,
            `start_time`,
            `end_date`,
            `end_time`,
            `project`,
            `task`
        )
    ";
    $db->exec($sql);
    echo "✅ Dodano unikalny indeks: $indexName\n";
} catch (PDOException $e) {
    echo "❌ Błąd przy dodawaniu indeksu: $indexName\n";
    echo "SQL: $sql\n";
    echo "Błąd: " . $e->getMessage() . "\n";
}
