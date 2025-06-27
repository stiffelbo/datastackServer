<?php

return [
  'table' => 'clockify',
  'fields' => [
    'id' => ['type' => 'INT AUTO_INCREMENT PRIMARY KEY'],
    'project' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'client' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'description' => ['type' => 'TEXT'],
    'task' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'parrentTask' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'user' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'group' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'email' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'tags' => ['type' => 'VARCHAR(255)', 'default' => "''"],
    'billable' => ['type' => 'TINYINT(1)', 'default' => '0'],
    'start_date' => ['type' => 'DATE', 'default' => 'NULL'],
    'start_time' => ['type' => 'TIME', 'default' => 'NULL'],
    'end_date' => ['type' => 'DATE', 'default' => 'NULL'],
    'end_time' => ['type' => 'TIME', 'default' => 'NULL'],
    'duration_h' => ['type' => 'TIME', 'default' => 'NULL'],
    'duration_decimal' => ['type' => 'DECIMAL(5,2)', 'default' => '0'],
    'billable_rate_pln' => ['type' => 'DECIMAL(6,2)', 'default' => '0'],
    'billable_amount_pln' => ['type' => 'DECIMAL(6,2)', 'default' => '0'],
    'period_id' => ['type' => 'INT NOT NULL'], //fk periods - on upload handled on server
    'structure_id' => ['type' => 'INT NOT NULL', 'default' => '1'], //fk structures on upload handled on server
    'user_id' => ['type' => 'INT NOT NULL'], //fk users on upload handled on server
    'imported_at' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
    'updated_at' => [
      'type' => 'DATETIME',
      'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ]
  ]
];
