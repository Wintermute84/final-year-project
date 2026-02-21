<?php

include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

header("Content-Type: application/json");

$payload = json_decode(file_get_contents("php://input"), true);
$aid = $payload['aid'] ?? null;
$date = $payload['edate'] ?? null;
$session = $payload['session'] ?? null;
$room = $payload['roomId'] ?? null;
$etype = $payload['etype'] ?? null;

if (!$aid || !$date || !$session || !$room || !$etype) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid request"]);
  exit;
}

if ($etype == 1) {
  $sql = "SELECT sad.seat,s.rollno,s.branch,s.semester,sad.reg_no, sad.electiveCourseId as course FROM `seating_allocation_data` sad JOIN students s on sad.reg_no = s.reg_no  WHERE sad.aid=? and sad.edate = ? and sad.session=? and sad.room = ? order by sad.seatingId ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $aid, $date, $session, $room);
  $stmt->execute();
} elseif ($etype == 2) {
  $sql = "SELECT sad.reg_no, a.branch,sad.seat, sad.electiveCourseId as course FROM `seating_allocation_data` sad JOIN ( SELECT DISTINCT student, branch FROM appearing_list ) a  ON sad.reg_no = a.student where sad.aid=? and sad.edate = ? and sad.session=? and sad.room = ? order by sad.seatingId";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isss", $aid, $date, $session, $room);
  $stmt->execute();
} else {
  die("Error getting Seating Data!");
  exit;
}

$res = $stmt->get_result();
$students = [];
while ($row = $res->fetch_assoc()) {
  $students[] =  [
    "reg_no" => $row['reg_no'],
    "branch" => $row['branch'],
    "rollno" => $etype == 1 ? $row['rollno'] : $row['reg_no'],
    "seat"    => $row['seat'],
    "semester" => $etype == 1 ? $row['semester'] : null,
    "course" => $row['course']
  ];
}

echo json_encode(["success" => true, "students" => $students]);
exit;
