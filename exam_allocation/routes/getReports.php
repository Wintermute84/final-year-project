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
$etype = $input['etype'];
if ($etype == 1) {
    $sql = "
SELECT s.reg_no,s.rollno,sad.room,s.branch,s.semester,sad.electiveCourseId as course FROM `seating_allocation_data` sad 
    join students s on sad.reg_no=s.reg_no 
    where sad.edate= ? and session= ? and sad.aid=? ORDER BY s.semester,s.branch,CAST(s.rollno AS UNSIGNED);
";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $edate, $session, $aid);
    $stmt->execute();
} elseif ($etype == 2) {
    $sql = "
SELECT sad.reg_no, sad.room, a.branch,sad.seat,sad.electiveCourseId as course FROM `seating_allocation_data` sad JOIN ( SELECT DISTINCT student, branch FROM appearing_list ) a  ON sad.reg_no = a.student
    where sad.edate= ? and sad.session= ? and sad.aid=? ORDER BY sad.electiveCourseId,a.branch,sad.reg_no;
";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $edate, $session, $aid);
    $stmt->execute();
}
$result = $stmt->get_result();

$data = [];
if ($etype == 1) {
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
} elseif ($etype == 2) {
    while ($row = $result->fetch_assoc()) {

        $sem    = null;
        $branch = $row['branch'];
        $room   = $row['room'];
        $roll   = $row['reg_no'];
        $course = $row['course'];

        if (!isset($data[$branch])) {
            $data[$branch] = [];
        }

        if (!isset($data[$branch][$room])) {
            $data[$branch][$room] = [];
        }

        $data[$branch][$room][] = [$roll, $course];
    }
}
echo json_encode(["success" => true, "reportData" => $data]);
exit;
