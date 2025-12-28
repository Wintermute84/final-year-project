<?php
include 'db_connect.php';
include 'functions.php';
if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}
$payload = json_decode(file_get_contents("php://input"), true) ?? null;
$selectedGroups   = $payload['sem_groupings'] ?? [];
$_SESSION['sem_groupings'] = $payload['sem_groupings'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $payload) {
  if (empty($selectedGroups)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
  }
  echo json_encode(["success" => true]);
  $_SESSION['step'] = 4;
}
