<?php
include __DIR__ . '/../config/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);
$aid = $input['aid'];
$ename = $input['ename'];
$edate = $input['edate'];
$session = $input['session'];

if (!isset($aid, $ename, $session, $edate)) {
  die("Input not set! Cannot generate room-wise count!");
}

$sql = "SELECT electiveCourseId AS course, room, COUNT(*) AS student_count
        FROM seating_allocation_data
        WHERE aid = ? AND edate = ? AND session = ?
        GROUP BY electiveCourseId, room
        ORDER BY electiveCourseId, room";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $aid, $edate, $session);
$stmt->execute();
$result = $stmt->get_result();


$courses = [];

while ($row = $result->fetch_assoc()) {
  $courses[$row['course']][] = [
    'room' => $row['room'],
    'count' => $row['student_count']
  ];
}


$html = "<div class='pdf-page'>
<table border='1' style='width:100%;'>

<tr><th colspan='3'>Muthoot Institute of Technology and Science (Autonomous)</th></tr>
<tr><th colspan='3'>{$ename}</th></tr>
<tr><th colspan='3'>Date of Exam: $edate &nbsp;&nbsp; Session: $session</th></tr>
<tr><th>Course</th><th>Room</th><th>Count</th></tr>
";



foreach ($courses as $course => $rooms) {
  $html .= "<tr>";
  $rowspan = count($rooms) + 1;
  $html .= "<th rowspan='{$rowspan}' align='center'>{$course}</th>";
  foreach ($rooms as $r) {
    $html .= "<tr><td align='center'>{$r['room']}</td>";
    $html .= "<td align='center'>{$r['count']}</td></tr>";
  }
  $html .= "</tr>";
}


$html .= "</table></div>";


$tablehtml = "
<html>
<head>
<style>

.pdf-page {
  page-break-after: always;
}

table {
  border-collapse: collapse;
  margin: 0 auto;
}

th, td {
  padding: 4px;
}

td{
  font-weight:bold;
}

</style>
</head>

<body>
{$html}
</body>
</html>
";

$uploadDirectory = __DIR__ . "/../Reports/{$edate}_{$session}_{$aid}/";

if (!is_dir($uploadDirectory)) {
  mkdir($uploadDirectory, 0777, true);
}

$fileName = "course_count_{$edate}_{$session}.pdf";

$uid = uniqid();
$tempHtmlPath = $uploadDirectory . "temp_$uid.html";
$pdfPath = $uploadDirectory . $fileName;


file_put_contents($tempHtmlPath, $tablehtml);

exec('"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe" --enable-local-file-access --page-size A3 "' . $tempHtmlPath . '" "' . $pdfPath . '"');

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$fileName\"");

readfile($pdfPath);

unlink($tempHtmlPath);

exit;
