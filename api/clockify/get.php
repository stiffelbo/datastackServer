<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../models/ClockifyModel.php';

$controller = new Controller(getDB());
//$controller->requireLogin(); // 🔐 wymaga zalogowania

$model = new ClockifyModel(getDB());
$data = $model->getAll();
$controller->json(['data' => $data]);
