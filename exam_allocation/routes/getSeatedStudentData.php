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

$sql = "SELECT sad.reg_no,sad.seat,s.rollno,s.branch,s.semester FROM `seating_allocation_data` sad JOIN students s on sad.reg_no = s.reg_no  WHERE sad.aid=? and sad.edate = ? and sad.session=? and sad.room = ? order by sad.seatingId ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $aid, $date, $session, $room);
$stmt->execute();

$res = $stmt->get_result();
$students = [];
while ($row = $res->fetch_assoc()) {
  $students[] =  [
    "reg_no" => $row['reg_no'],
    "branch" => $row['branch'],
    "rollno" => $row['rollno'],
    "seat"    => $row['seat'],
    "semester" => $row['semester']
  ];
}

echo json_encode(["success" => true, "students" => $students]);
exit;
