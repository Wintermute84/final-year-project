<?php

include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

header("Content-Type: application/json");

$payload = json_decode(file_get_contents("php://input"), true);
$eid = $payload['eid'] ?? null;
$date = $payload['date'] ?? null;
$session = $payload['session'] ?? null;
$branches = [];

if (!$eid || !$date || !$session) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid request"]);
  exit;
}

if ($_SESSION['examType'] == 1) {
  $sql = "SELECT distinct branch,sem FROM `exam_time_table` WHERE eid=? and edate = ? and session=? order by sem ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $eid, $date, $session);
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $row = $res->fetch_assoc()) {
    $branches[] =  [
      "branch" => $row['branch'],
      "sem"    => $row['sem']
    ];
  }
} elseif ($_SESSION['examType'] == 2) {
  $sql = "SELECT distinct branch,ccode FROM `appearing_list` WHERE eid=? and edate = ? and session=? order by branch ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $eid, $date, $session);
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $row = $res->fetch_assoc()) {
    $branches[] =  [
      "branch" => $row['branch'],
      "ccode"    => $row['ccode']
    ];
  }
}
echo json_encode(["success" => true, "branches" => $branches]);
exit;
