<?php

include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';
header("Content-Type: application/json");

$payload = json_decode(file_get_contents("php://input"), true);
$eid = $payload['eid'] ?? null;
$date = $payload['edate'] ?? null;
$session = $payload['sess'] ?? null;

if (!$eid || !$date || !$session) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid request"]);
  exit;
}

$sql = "SELECT DISTINCT a.aid,s.created_at from seating_allocation_data a JOIN seating_allocation_definition s ON a.aid = s.aid where s.eid = ? and a.edate = ? and a.session = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $eid, $date, $session);
$stmt->execute();

$res = $stmt->get_result();
$allocations = [];
while ($row = $res->fetch_assoc()) {
  $allocations[] = [
    "aid" => $row['aid'],
    "created_at" => $row['created_at']
  ];
}

echo json_encode([
  "success" => true,
  "allocations" => $allocations
]);
exit;
