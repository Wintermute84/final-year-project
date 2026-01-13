<?php
include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

$payload = json_decode(file_get_contents("php://input"), true) ?? null;
$selectedRoom = $payload['rooms'] ?? [];
$capacity     = $payload['roomCapacity'] ?? 0;
$eid          = $_SESSION['eid'];
$_SESSION['rooms'] = $selectedRoom;

$stmt = $conn->prepare("
  SELECT 
    et.edate,
    et.session,
    et.eid,
    COUNT(DISTINCT s.reg_no) AS total_students
  FROM exam_time_table et
  JOIN students s 
      ON s.semester = et.sem 
     AND s.branch   = et.branch
  WHERE et.eid = ?
  GROUP BY et.edate, et.session, et.eid
  ORDER BY total_students DESC
  LIMIT 1;
");

$stmt->bind_param("i", $eid);
$stmt->execute();

$result = $stmt->get_result();
$row    = $result->fetch_assoc();

$edate   = $row['edate'];
$session = $row['session'];
$students = $row['total_students'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $payload) {

  if (empty($selectedRoom)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit;
  }

  if ($students > $capacity) {
    http_response_code(400);
    echo json_encode([
      "error" => "Room capacity will not fit all students. Please ensure sufficient rooms are selected.",
      "discrepancy" => "$edate $session"
    ]);
    exit;
  }

  echo json_encode(["success" => true]);
  $_SESSION['step'] = 3;
}
