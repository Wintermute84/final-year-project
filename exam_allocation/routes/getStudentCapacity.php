<?php

include __DIR__ . '/../config/db_connect.php';
include __DIR__ . '/../config/functions.php';

$eid = $_SESSION['eid'];
$etype = $_SESSION['examType'];

if (!isset($eid)) {
  echo json_encode(["success" => false, "error" => "exam id not in session var!!!!!!!!!!"]);
  exit;
}



$courses = [];

if ($etype == 1) {
  $sql = "SELECT DISTINCT ett.edate, ett.session, ett.ccode, ett.sem, c.is_elective 
        FROM exam_time_table ett 
        JOIN courses c ON ett.ccode = c.ccode 
        WHERE ett.eid = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $sem = $row['sem'];
    $ccode = $row['ccode'];
    $is_elective = $row['is_elective'];

    if ($is_elective == '0') {

      $sql2 = "SELECT COUNT(*) as stud_count 
                 FROM students s 
                 WHERE s.semester = ? 
                 AND s.branch IN (
                     SELECT DISTINCT branch 
                     FROM courses 
                     WHERE ccode = ?
                 )";

      $stmt2 = $conn->prepare($sql2);
      $stmt2->bind_param("is", $sem, $ccode);
      $stmt2->execute();
      $res = $stmt2->get_result()->fetch_assoc();
    } elseif ($is_elective == 1) {
      $sql2 = "SELECT COUNT(*) as stud_count
        FROM students s
        WHERE s.semester = ?
        AND ? IN (s.elective_1, s.elective_2, s.elective_3, s.minor)";

      $stmt2 = $conn->prepare($sql2);
      $stmt2->bind_param("is", $sem, $ccode);
      $stmt2->execute();
      $res = $stmt2->get_result()->fetch_assoc();
    } else {
      die("Error while returning student count!");
    }

    $key = $row['edate'] . "_" . $row['session'];

    if (!isset($courses[$key])) {
      $courses[$key] = 0;
    }

    $courses[$key] += $res['stud_count'];
  }
} elseif ($etype == 2) {

  $sql = "SELECT count(*) as stud_count,edate,session 
        FROM appearing_list WHERE eid = ? GROUP BY edate,session";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $stud_count = $row['stud_count'];
    $edate = $row['edate'];
    $session = $row['session'];
    $key = $row['edate'] . "_" . $row['session'];
    if (!isset($courses[$key])) {
      $courses[$key] = 0;
    }
    $courses[$key] += $row['stud_count'];
  }
}

echo json_encode(["success" => true, "count" => $courses]);
exit;
