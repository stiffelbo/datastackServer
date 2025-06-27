<?php

return [
  'table' => 'users',
  'fields' => [
    'id' => ['type' => 'INT AUTO_INCREMENT PRIMARY KEY'],
    'email' => ['type' => 'VARCHAR(255) NOT NULL UNIQUE'],
    'name' => ['type' => 'VARCHAR(100)', 'default' => "''"],
    'last_name' => ['type' => 'VARCHAR(100)', 'default' => "''"],
    'password_hash' => ['type' => 'TEXT', 'default' => "''"],

    'role' => [
      'type' => "ENUM('admin','manager','viewer')",
      'default' => "'viewer'"
    ],

    'token' => ['type' => 'VARCHAR(255)', 'default' => "NULL"],
    'last_login_at' => ['type' => 'DATETIME', 'default' => 'NULL'],

    'is_active' => ['type' => 'TINYINT(1)', 'default' => 1],
    'is_active_date' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],

    'created_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
    'updated_at' => [
      'type' => 'DATETIME',
      'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ]
  ]
];
