<?php
// $db jest dostępne globalnie z migrate.php

$email = 'jewgienij.brzozowskin@germaniamint.com';
$password = 'technics1210';
$role = 'admin';

// Czy istnieje już administrator?
$stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$stmt->execute();
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
  echo "⏭  Pominięto dodanie admina (już istnieje)\n";
  return;
}

// Haszujemy hasło
$hash = password_hash($password, PASSWORD_BCRYPT);

// Dodaj administratora
$stmt = $db->prepare("
  INSERT INTO users (email, name, last_name, password_hash, role, is_active)
  VALUES (:email, :name, :last_name, :hash, :role, 1)
");
$stmt->execute([
  'email' => $email,
  'name' => 'System',
  'last_name' => 'Admin',
  'hash' => $hash,
  'role' => $role
]);

echo "✅ Dodano użytkownika administratora: $email";
