<?php

include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

header("Content-Type: application/json");

$payload = json_decode(file_get_contents("php://input"), true);
$aid = $payload['aid'] ?? null;
$date = $payload['edate'] ?? null;
$session = $payload['session'] ?? null;
$room = $payload['roomId'] ?? null;

if (!$aid || !$date || !$session || !$room) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid request"]);
  exit;
}

$sql = "SELECT * FROM `seating_allocation_data` WHERE aid=? and edate = ? and session=? and room = ? order by seatingId ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $aid, $date, $session, $room);
$stmt->execute();

$res = $stmt->get_result();
$students = [];
while ($row = $row = $res->fetch_assoc()) {
  $students[] =  [
    "reg_no" => $row['reg_no'],
    "seat"    => $row['seat']
  ];
}

echo json_encode(["success" => true, "students" => $students]);
exit;
