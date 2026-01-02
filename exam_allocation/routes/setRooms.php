<?php
include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

$payload = json_decode(file_get_contents("php://input"), true) ?? null;
$selectedRoom   = $payload['rooms'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $payload) {
  if (empty($selectedRoom)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
  }
  echo json_encode(["success" => true]);
  $_SESSION['step'] = 3;
}
