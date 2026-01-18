<?php

function checkIfElective($conn, $edate, $session, $semester, $eid)
{
  $stmt = $conn->prepare("SELECT * from exam_time_table as ett join courses as c on c.ccode=ett.ccode and c.sem=? and ett.session=? and ett.edate = ? and ett.eid = ? and c.is_elective=1");
  $stmt->bind_param("issi", $semester, $session, $edate, $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  $rowCount = $result->num_rows;
  return $rowCount > 0;
}

function getElectiveSubjects($conn, $edate, $session, $semester, $eid)
{
  $stmt = $conn->prepare("SELECT distinct ett.ccode from exam_time_table as ett join courses as c on c.ccode=ett.ccode and c.sem=? and ett.session=? and ett.edate = ? and ett.eid = ? and c.is_elective=1");
  $stmt->bind_param("issi", $semester, $session, $edate, $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = $result->fetch_all(MYSQLI_ASSOC);
  return array_column($rows, 'ccode');
}


function returnElectiveBranches($conn, $edate, $session, $semester, $eid)
{
  $stmt = $conn->prepare("SELECT  distinct ett.branch from exam_time_table as ett join courses as c on c.ccode=ett.ccode and c.sem=? and ett.session=? and ett.edate = ? and ett.eid = ? and c.is_elective=1");
  $stmt->bind_param("issi", $semester, $session, $edate, $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = $result->fetch_all(MYSQLI_ASSOC);
  return array_column($rows, 'branch');
}

function returnElectiveData($conn, $edate, $session, $semester, $orderfield, $eid)
{
  $electiveBranches = returnElectiveBranches($conn, $edate, $session, $semester, $eid);
  $nonElectiveBranches = array_diff($orderfield, $electiveBranches);
  $electiveSubs = getElectiveSubjects($conn, $edate, $session, $semester, $eid);
  $group = getElectiveStudentData($conn, $electiveSubs, $electiveBranches);
  $nonElectiveStudents = getStudData($conn, $semester, $nonElectiveBranches, $edate, $session, $eid);
  $group = array_merge($group, $nonElectiveStudents);
  return $group;
}

function getElectiveStudentData($conn, $electiveSubs, $orderfield)
{
  $orderfield = implode(",", array_map(fn($b) => "'" . $conn->real_escape_string($b) . "'", $orderfield));
  $branches = implode(",", array_map(fn($b) => "'" . $conn->real_escape_string($b) . "'", $electiveSubs));
  $stmt = $conn->prepare("SELECT
    s.reg_no,
    s.rollno,
    s.name,
    s.branch,
    CASE
        WHEN s.elective_1 IN ($branches) THEN s.elective_1
        WHEN s.elective_2 IN ($branches) THEN s.elective_2
        WHEN s.elective_3 IN ($branches) THEN s.elective_3
    END AS COURSE
FROM students s
WHERE (s.elective_1 IN ($branches)
   OR s.elective_2 IN ($branches)
   OR s.elective_3 IN ($branches))
AND s.branch in ($orderfield)
ORDER BY COURSE, s.branch, s.rollno;");
  $stmt->execute();
  $result = $stmt->get_result();
  return iterator_to_array($result);
}

function getDrawingRoomData($conn, $rooms)
{
  $stmt = $conn->prepare("SELECT * from rooms where type='Drawing' AND Rid in ($rooms)");
  $stmt->execute();
  $result = $stmt->get_result();
  return $result;
}

function getStudData($conn, $semester, $orderfield, $edate, $session, $eid)
{
  if (empty($orderfield)) {
    $sql = "SELECT * FROM students WHERE 1 = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return iterator_to_array($stmt->get_result());
  }
  $orderfield = implode(",", array_map(fn($b) => "'" . $conn->real_escape_string($b) . "'", $orderfield));
  $stmt = $conn->prepare("SELECT s.rollno, s.reg_no, c.ccode as COURSE 
  FROM students s 
  JOIN exam_time_table ett ON s.branch = ett.branch 
  AND s.semester = ett.sem 
  JOIN courses c ON ett.ccode = c.ccode 
  AND ett.branch=c.branch 
  AND ett.sem=c.sem 
  WHERE s.semester = ? 
  AND s.branch IN ($orderfield) 
  AND c.is_elective = 0 
  AND ett.edate = ? 
  AND ett.session = ?
  AND ett.eid = ? 
  ORDER BY FIELD(s.branch, $orderfield), s.rollno;
  ");
  $stmt->bind_param("issi", $semester, $edate, $session, $eid);
  $stmt->execute();
  $result = $stmt->get_result();
  return iterator_to_array($result);
}

function getNormalRoomData($conn, $rooms)
{
  $stmt = $conn->prepare("SELECT * from rooms where Rid IN ($rooms) and type='Normal'");
  $stmt->execute();
  $result = $stmt->get_result();
  return $result;
}


function algoOne($conn, $semester, $orderfield1, $orderfield2, $rooms, $edate, $session, $eid)
{
  if (checkIfElective($conn, $edate, $session, $semester, $eid)) {
    $orderfield = array_merge($orderfield1, $orderfield2);
    $grp1 = returnElectiveData($conn, $edate, $session, $semester, $orderfield, $eid);
    $grp2 = [];
  } else {
    $grp1 = getStudData($conn, $semester, $orderfield1, $edate, $session, $eid);
    $grp2 = getStudData($conn, $semester, $orderfield2, $edate, $session, $eid);
  }


  $x = count($grp1);
  $y = count($grp2);

  $drawingRoomData = [];

  $g = getDrawingRoomData($conn, $rooms);
  while ($row = $g->fetch_assoc()) {
    $drawingRoomData[] = $row;
  }

  $dIndex = 0;
  $drawingAllocations = [];
  foreach ($drawingRoomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    for ($i = 0; $i < $n && $dIndex < $x; $i++) {
      $drawingAllocations[] = [
        'reg_no' => $grp1[$dIndex]['reg_no'],
        'rollno' => $grp1[$dIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . ($i + 1),
        'edate'  => $edate,
        'session' => $session,
        'elective' => $grp1[$dIndex]['COURSE'] ?? null
      ];
      $dIndex++;
    }
  }
  $grp1 = array_slice($grp1, $dIndex);
  $x = count($grp1);
  $y = count($grp2);
  if ($x >= $y) {
    $dominant = $grp1;
    $weaker   = $grp2;
  } else {
    $dominant = $grp2;
    $weaker   = $grp1;
  }

  $z = abs($x - $y);

  if ($z % 2 == 0) {
    $p = $q = $z / 2;
  } else {
    $p = ($z + 1) / 2;
    $q = ($z - 1) / 2;
  }

  $roomData = [];
  $normalRoomData = getNormalRoomData($conn, $rooms);
  while ($row = $normalRoomData->fetch_assoc()) {
    $roomData[] = $row;
  }


  $dominant_A = array_slice($dominant, 0, count($dominant) - $p);
  $dominant_B = array_slice($dominant, count($dominant) - $p);

  $sideA_students = $dominant_A;
  $sideB_students = array_merge($dominant_B, $weaker);


  $finalAllocation = [];
  $aIndex = 0;
  $bIndex = 0;

  foreach ($roomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    $A_slots = intdiv($n, 2);
    $B_slots = intdiv($n, 2);

    for ($i = 1; $i <= $A_slots && $aIndex < count($sideA_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideA_students[$aIndex]['reg_no'],
        'rollno' => $sideA_students[$aIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideA_students[$aIndex]['COURSE'] ?? null
      ];
      $aIndex++;
    }

    for ($i = 1; $i <= $B_slots && $bIndex < count($sideB_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideB_students[$bIndex]['reg_no'],
        'rollno' => $sideB_students[$bIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'B' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideB_students[$bIndex]['COURSE'] ?? null
      ];
      $bIndex++;
    }
  }

  $result = array_merge($drawingAllocations, $finalAllocation);
  return $result;
}

function algoTwo($conn, $semester1, $semester2, $orderfield1, $orderfield2, $rooms, $edate, $session, $eid)
{

  if (checkIfElective($conn, $edate, $session, $semester1, $eid)) {
    $grp1 = returnElectiveData($conn, $edate, $session, $semester1, $orderfield1, $eid);
  } else {
    $grp1 = getStudData($conn, $semester1, $orderfield1, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester2, $eid)) {
    $grp2 = returnElectiveData($conn, $edate, $session, $semester2, $orderfield2, $eid);
  } else {
    $grp2 = getStudData($conn, $semester2, $orderfield2, $edate, $session, $eid);
  }

  $x = count($grp1);
  $y = count($grp2);

  $drawingRoomData = [];

  $g = getDrawingRoomData($conn, $rooms);
  while ($row = $g->fetch_assoc()) {
    $drawingRoomData[] = $row;
  }

  $dIndex = 0;
  $drawingAllocations = [];
  foreach ($drawingRoomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    for ($i = 0; $i < $n && $dIndex < $x; $i++) {
      $drawingAllocations[] = [
        'reg_no' => $grp1[$dIndex]['reg_no'],
        'rollno' => $grp1[$dIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . ($i + 1),
        'edate'  => $edate,
        'session' => $session,
        'elective' => $grp1[$dIndex]['COURSE'] ?? null
      ];
      $dIndex++;
    }
  }

  $grp1 = array_slice($grp1, $dIndex);
  $x = count($grp1);
  $y = count($grp2);
  if ($x >= $y) {
    $dominant = $grp1;
    $weaker   = $grp2;
  } else {
    $dominant = $grp2;
    $weaker   = $grp1;
  }

  $z = abs($x - $y);

  if ($z % 2 == 0) {
    $p = $q = $z / 2;
  } else {
    $p = ($z + 1) / 2;
    $q = ($z - 1) / 2;
  }

  $roomData = [];
  $normalRoomData = getNormalRoomData($conn, $rooms);
  while ($row = $normalRoomData->fetch_assoc()) {
    $roomData[] = $row;
  }


  $dominant_A = array_slice($dominant, 0, count($dominant) - $p);
  $dominant_B = array_slice($dominant, count($dominant) - $p);

  $sideA_students = $dominant_A;
  $sideB_students = array_merge($dominant_B, $weaker);


  $finalAllocation = [];
  $aIndex = 0;
  $bIndex = 0;

  foreach ($roomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    $A_slots = intdiv($n, 2);
    $B_slots = intdiv($n, 2);

    for ($i = 1; $i <= $A_slots && $aIndex < count($sideA_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideA_students[$aIndex]['reg_no'],
        'rollno' => $sideA_students[$aIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideA_students[$aIndex]['COURSE'] ?? null
      ];
      $aIndex++;
    }

    for ($i = 1; $i <= $B_slots && $bIndex < count($sideB_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideB_students[$bIndex]['reg_no'],
        'rollno' => $sideB_students[$bIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'B' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideB_students[$bIndex]['COURSE'] ?? null
      ];
      $bIndex++;
    }
  }

  $result = array_merge($drawingAllocations, $finalAllocation);
  return $result;
}

function algoThree($conn, $semester1, $semester2, $semester3, $orderfield1, $orderfield2, $orderfield3, $rooms, $edate, $session, $eid)
{

  if (checkIfElective($conn, $edate, $session, $semester1, $eid)) {
    $grp1 = returnElectiveData($conn, $edate, $session, $semester1, $orderfield2, $eid);
  } else {
    $grp1 = getStudData($conn, $semester1, $orderfield1, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester2, $eid)) {
    $grp2 = returnElectiveData($conn, $edate, $session, $semester2, $orderfield2, $eid);
  } else {
    $grp2 = getStudData($conn, $semester2, $orderfield2, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester3, $eid)) {
    $grp3 = returnElectiveData($conn, $edate, $session, $semester3, $orderfield3, $eid);
  } else {
    $grp3 = getStudData($conn, $semester3, $orderfield3, $edate, $session, $eid);
  }

  $x = count($grp1);
  $y = count($grp2);
  $l = count($grp3);

  $drawingRoomData = [];

  $g = getDrawingRoomData($conn, $rooms);
  while ($row = $g->fetch_assoc()) {
    $drawingRoomData[] = $row;
  }

  $dIndex = 0;
  $drawingAllocations = [];
  foreach ($drawingRoomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    for ($i = 0; $i < $n && $dIndex < $l; $i++) {
      $drawingAllocations[] = [
        'reg_no' => $grp3[$dIndex]['reg_no'],
        'rollno' => $grp3[$dIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . ($i + 1),
        'edate'  => $edate,
        'session' => $session,
        'elective' => $grp3[$dIndex]['COURSE'] ?? null
      ];
      $dIndex++;
    }
  }

  $grp3 = array_slice($grp3, $dIndex);
  $grp1 = array_merge($grp1, $grp3);
  $x = count($grp1);
  $y = count($grp2);
  if ($x >= $y) {
    $dominant = $grp1;
    $weaker   = $grp2;
  } else {
    $dominant = $grp2;
    $weaker   = $grp1;
  }

  $z = abs($x - $y);

  if ($z % 2 == 0) {
    $p = $q = $z / 2;
  } else {
    $p = ($z + 1) / 2;
    $q = ($z - 1) / 2;
  }

  $roomData = [];
  $normalRoomData = getNormalRoomData($conn, $rooms);
  while ($row = $normalRoomData->fetch_assoc()) {
    $roomData[] = $row;
  }


  $dominant_A = array_slice($dominant, 0, count($dominant) - $p);
  $dominant_B = array_slice($dominant, count($dominant) - $p);

  $sideA_students = $dominant_A;
  $sideB_students = array_merge($dominant_B, $weaker);


  $finalAllocation = [];
  $aIndex = 0;
  $bIndex = 0;

  foreach ($roomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    $A_slots = intdiv($n, 2);
    $B_slots = intdiv($n, 2);

    for ($i = 1; $i <= $A_slots && $aIndex < count($sideA_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideA_students[$aIndex]['reg_no'],
        'rollno' => $sideA_students[$aIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideA_students[$aIndex]['COURSE'] ?? null
      ];
      $aIndex++;
    }

    for ($i = 1; $i <= $B_slots && $bIndex < count($sideB_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideB_students[$bIndex]['reg_no'],
        'rollno' => $sideB_students[$bIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'B' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideB_students[$bIndex]['COURSE'] ?? null
      ];
      $bIndex++;
    }
  }

  $result = array_merge($drawingAllocations, $finalAllocation);
  return $result;
}



function algoFour($conn, $semester1, $semester2, $semester3, $semester4, $orderfield1, $orderfield2, $orderfield3, $orderfield4, $rooms, $edate, $session, $eid)
{
  if (checkIfElective($conn, $edate, $session, $semester1, $eid)) {
    $grp1 = returnElectiveData($conn, $edate, $session, $semester1, $orderfield1, $edate, $session, $eid);
  } else {
    $grp1 = getStudData($conn, $semester1, $orderfield1, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester2, $eid)) {
    $grp2 = returnElectiveData($conn, $edate, $session, $semester2, $orderfield2, $eid);;
  } else {
    $grp2 = getStudData($conn, $semester2, $orderfield2, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester3, $eid)) {
    $grp3 = returnElectiveData($conn, $edate, $session, $semester3, $orderfield3, $eid);
  } else {
    $grp3 = getStudData($conn, $semester3, $orderfield3, $edate, $session, $eid);
  }
  if (checkIfElective($conn, $edate, $session, $semester4, $eid)) {
    $grp4 = returnElectiveData($conn, $edate, $session, $semester4, $orderfield4, $eid);
  } else {
    $grp4 = getStudData($conn, $semester4, $orderfield4, $edate, $session, $eid);
  }

  $x = count($grp1);
  $y = count($grp2);

  $drawingRoomData = [];

  $g = getDrawingRoomData($conn, $rooms);
  while ($row = $g->fetch_assoc()) {
    $drawingRoomData[] = $row;
  }


  $dIndex = 0;
  $drawingAllocations = [];
  foreach ($drawingRoomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    for ($i = 0; $i < $n && $dIndex < $x; $i++) {
      $drawingAllocations[] = [
        'reg_no' => $grp1[$dIndex]['reg_no'],
        'rollno' => $grp1[$dIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . ($i + 1),
        'edate'  => $edate,
        'session' => $session,
        'elective' => $grp1[$dIndex]['COURSE'] ?? null
      ];
      $dIndex++;
    }
  }
  $grp1 = array_slice($grp1, $dIndex);
  $grp1 = array_merge($grp1, $grp3);
  $grp2 = array_merge($grp2, $grp4);
  $x = count($grp1);
  $y = count($grp2);
  if ($x >= $y) {
    $dominant = $grp1;
    $weaker   = $grp2;
  } else {
    $dominant = $grp2;
    $weaker   = $grp1;
  }

  $z = abs($x - $y);

  if ($z % 2 == 0) {
    $p = $q = $z / 2;
  } else {
    $p = ($z + 1) / 2;
    $q = ($z - 1) / 2;
  }

  $roomData = [];
  $normalRoomData = getNormalRoomData($conn, $rooms);
  while ($row = $normalRoomData->fetch_assoc()) {
    $roomData[] = $row;
  }


  $dominant_A = array_slice($dominant, 0, count($dominant) - $p);
  $dominant_B = array_slice($dominant, count($dominant) - $p);

  $sideA_students = $dominant_A;
  $sideB_students = array_merge($dominant_B, $weaker);

  $finalAllocation = [];
  $aIndex = 0;
  $bIndex = 0;

  foreach ($roomData as $room) {

    $roomName = $room['Room_no'];
    $n = $room['Capacity'];

    $A_slots = intdiv($n, 2);
    $B_slots = intdiv($n, 2);

    for ($i = 1; $i <= $A_slots && $aIndex < count($sideA_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideA_students[$aIndex]['reg_no'],
        'rollno' => $sideA_students[$aIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'A' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideA_students[$aIndex]['COURSE'] ?? null
      ];
      $aIndex++;
    }

    for ($i = 1; $i <= $B_slots && $bIndex < count($sideB_students); $i++) {
      $finalAllocation[] = [
        'reg_no' => $sideB_students[$bIndex]['reg_no'],
        'rollno' => $sideB_students[$bIndex]['rollno'],
        'room'   => $roomName,
        'seat'   => 'B' . $i,
        'edate'  => $edate,
        'session' => $session,
        'elective' => $sideB_students[$bIndex]['COURSE'] ?? null
      ];
      $bIndex++;
    }
  }

  $result = array_merge($drawingAllocations, $finalAllocation);
  return $result;
}
