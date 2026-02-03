<?php
include __DIR__ . '/../config/db_connect.php';
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['edate'], $input['session'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing date or session"]);
    exit;
}
$edate = $input['edate'];
$session = $input['session'];
$aid = $input['aid'];

$sql = "
SELECT s.reg_no,s.rollno,sad.room,s.branch,s.semester,sad.electiveCourseId as course FROM `seating_allocation_data` sad 
    join students s on sad.reg_no=s.reg_no 
    where sad.edate= ? and session= ? and sad.aid=? ORDER BY s.semester,s.branch,CAST(s.rollno AS UNSIGNED);
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $edate, $session, $aid);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $sem    = $row['semester'];
    $branch = $row['branch'];
    $room   = $row['room'];
    $roll   = $row['rollno'];
    $course = $row['course'];

    if (!isset($data[$sem])) {
        $data[$sem] = [];
    }

    if (!isset($data[$sem][$branch])) {
        $data[$sem][$branch] = [];
    }

    if (!isset($data[$sem][$branch][$room])) {
        $data[$sem][$branch][$room] = [];
    }

    $data[$sem][$branch][$room][] = [$roll, $course];
}

echo json_encode(["success" => true, "reportData" => $data]);
exit;
