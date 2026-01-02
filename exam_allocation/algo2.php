<?php
include "config/db_connect.php";
function getStudentData($conn, $semester)
{
  $stmt = $conn->prepare("SELECT *
FROM students
WHERE semester IN (5)
ORDER BY
  CASE branch
    WHEN 'CSE A'  THEN 1
    WHEN 'CSE B'  THEN 2
    WHEN 'CSE AI' THEN 3
    WHEN 'AIDS'   THEN 4
    WHEN 'CY'     THEN 5
    WHEN 'EEE'     THEN 6
    WHEN 'ECE'     THEN 7
    WHEN 'ME'     THEN 8
    WHEN 'CE'     THEN 9
  END,
  CAST(SUBSTRING(semester, 2) AS UNSIGNED),rollno;
");
  $stmt->execute();
  $result = $stmt->get_result();
  return $result;
}
function getStudentDatas($conn, $semester)
{
  $stmt = $conn->prepare("SELECT *
FROM students
WHERE semester IN (7)
ORDER BY
  CASE branch
    WHEN 'CSE A'  THEN 1
    WHEN 'CSE B'  THEN 2
    WHEN 'CSE AI' THEN 3
    WHEN 'AIDS'   THEN 4
    WHEN 'CY'     THEN 5
    WHEN 'EEE'     THEN 6
    WHEN 'ECE'     THEN 7
    WHEN 'ME'     THEN 8
    WHEN 'CE'     THEN 9

  END,
  CAST(SUBSTRING(semester, 2) AS UNSIGNED),rollno;
");
  $stmt->execute();
  $result = $stmt->get_result();
  return $result;
}

function getRoomData($conn)
{
  $stmt = $conn->prepare("SELECT *
FROM rooms");
  $stmt->execute();
  $result = $stmt->get_result();
  return $result;
}
$rooms = getRoomData($conn);
/*$b = getStudentDatas($conn,7);
  echo mysqli_num_rows($a);
  if(mysqli_num_rows($a) > mysqli_num_rows($b)){
    $p = $a;
    $q = $b;
  }
  else{
    $p = $b;
    $q = $a;
  }
  $x = $p->num_rows;
  $y = $q->num_rows;
  while ($row = $p->fetch_assoc()) {
    echo $row['reg_no'] . "<br>";
}

  function getDrawingCount($conn){
    $stmt = $conn->prepare("SELECT sum(Capacity) as total from rooms where type='Drawing'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int)$row['total'];
  }
  $drawingCount = getDrawingCount($conn);
  echo $x.$y.$drawingCount;
  $x -= $drawingCount;
  echo $x;
  echo $drawingCount . "<br>";
  $z = abs($x-$y);
  if($z % 2 == 0){
    $x += $z/2;
  }
  echo ($z)%2 . "<br>";
  
  while ($row = $rooms->fetch_assoc()) {
    echo $row['Capacity'] . "<br>";
}*/
function getDrawingCount($conn)
{
  $stmt = $conn->prepare("SELECT sum(Capacity) as total from rooms where type='Drawing'");
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return (int)$row['total'];
}

$csStudents    = getStudentData($conn, 7);
$nonCSStudents = getStudentDatas($conn, 7);

$cs = iterator_to_array($csStudents);
$noncs = iterator_to_array($nonCSStudents);

$x = count($cs);
$y = count($noncs);

$g = getDrawingCount($conn);   // total drawing room capacity

$drawingAllocations = [];
for ($i = 0; $i < $g && $i < $x; $i++) {
  $drawingAllocations[] = [
    'reg_no' => $cs[$i]['reg_no'],
    'room'   => 'DRAWING',
    'seat'   => 'D' . ($i + 1)
  ];
}

// remove assigned CS students
$cs = array_slice($cs, $g);
$x = count($cs);
$y = count($noncs);
if ($x >= $y) {
  $dominant = $cs;
  $weaker   = $noncs;
} else {
  $dominant = $noncs;
  $weaker   = $cs;
}

$z = abs($x - $y);

if ($z % 2 == 0) {
  $p = $q = $z / 2;
} else {
  $p = ($z + 1) / 2;
  $q = ($z - 1) / 2;
}

$A_total = $y + $p;
$B_total = $y + $q;

echo $A_total . $B_total;

$rooms = [];
$res = $conn->query("
    SELECT *
    FROM rooms
    WHERE type = 'Normal'
");

while ($row = $res->fetch_assoc()) {
  $rooms[] = $row;
}


$dominant_A = array_slice($dominant, 0, count($dominant) - $p);
echo serialize($dominant_A);
$dominant_B = array_slice($dominant, count($dominant) - $p);
echo serialize($dominant_B);

$sideA_students = $dominant_A;
$sideB_students = array_merge($dominant_B, $weaker);

echo count($sideA_students) . count($sideB_students);

$finalAllocation = [];
$aIndex = 0;
$bIndex = 0;

foreach ($rooms as $room) {

  $roomName = $room['Room_no'];
  $n = $room['Capacity'];

  $A_slots = intdiv($n, 2);
  $B_slots = intdiv($n, 2);

  // -------- SIDE A --------
  for ($i = 1; $i <= $A_slots && $aIndex < count($sideA_students); $i++) {
    $finalAllocation[] = [
      'reg_no' => $sideA_students[$aIndex]['reg_no'],
      'room'   => $roomName,
      'seat'   => 'A' . $i
    ];
    $aIndex++;
  }

  // -------- SIDE B --------
  for ($i = 1; $i <= $B_slots && $bIndex < count($sideB_students); $i++) {
    $finalAllocation[] = [
      'reg_no' => $sideB_students[$bIndex]['reg_no'],
      'room'   => $roomName,
      'seat'   => 'B' . $i
    ];
    $bIndex++;
  }
}

echo "<pre>";
foreach ($drawingAllocations as $d) {
  echo "{$d['reg_no']} → {$d['seat']} → {$d['room']}\n";
}

foreach ($finalAllocation as $f) {
  echo "{$f['reg_no']} → {$f['seat']} → {$f['room']}\n";
}
echo "</pre>";





?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

</body>

</html>