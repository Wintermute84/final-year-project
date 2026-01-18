<?php
include __DIR__ . '/../config/db_connect.php';
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['aid'], $input['session'], $input['edate'])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing date or session"]);
  exit;
}

$edate = $input['edate'];
$session = $input['session'];
$aid = $input['aid'];

$sql = "
SELECT distinct room,Rid,Capacity,Type,edate,aid,session FROM seating_allocation_data sad JOIN rooms on sad.room=rooms.Room_no where aid = ? and edate = ? and session = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $aid, $edate, $session);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

  $rid    = $row['Rid'];
  $capacity = $row['Capacity'];
  $type   = $row['Type'];
  $aid   = $row['aid'];
  $edate = $row['edate'];
  $session = $row['session'];
  $room = $row['room'];
  $data[$rid] = ["room" => $room, "capacity" => $capacity, "type" => $type, "aid" => $aid, "edate" => $edate, "session" => $session];
}

echo json_encode(["success" => true, "roomData" => $data]);
exit;
