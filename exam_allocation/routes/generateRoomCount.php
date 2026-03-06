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

$sql = "SELECT electiveCourseId as course,room,COUNT(*) AS student_count FROM seating_allocation_data WHERE aid = ? and edate = ? and session = ? GROUP BY electiveCourseId,room ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $aid, $edate, $session);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

$html = "<div class='pdf-page ding'>
  <table border='1' style='width:100%;'>
    <tr><th colspan='3'>Muthoot Institute of Technology and Science (Autonomous)</th></tr>
    <tr><th colspan='3'>{$ename}</th></tr>
    <tr><th colspan='3'>Date of Exam: $edate &nbsp;&nbsp; Session: $session</th></tr>
    <tr>
      <th>Hall</th>
      <th>Course Code</th>
      <th>Count</th>
    </tr>
";

foreach ($data as $s) {
  $room = $s['room'];
  $course = $s['course'];
  $count = $s['student_count'];
  $html .=
    "<tr>
    <td align='center'>$room</td>                
    <td align='center'>$course</td>
    <td align='center'>$count</td>
   </tr> 
  ";
}

$html .= "</table>";

$tablehtml = "
<html>
<head>
<style>
.pdf-page {
  page-break-after: always;
}

table {
  border-collapse: collapse;
  page-break-inside: avoid;
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

$uploadDirectory = __DIR__ . "/../Reports/{$edate}_{$session}/";

if (!is_dir($uploadDirectory)) {
  mkdir($uploadDirectory, 0777, true);
}

$fileName = "course_count_{$roomId}_{$edate}_{$session}.pdf";

$tempHtmlPath = $uploadDirectory . "temp.html";
$pdfPath      = $uploadDirectory . $fileName;

file_put_contents($tempHtmlPath, $tablehtml);

exec('"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe" --enable-local-file-access --page-size A3 "' . $tempHtmlPath . '" "' . $pdfPath . '"');

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$fileName\"");

readfile($pdfPath);
unlink($tempHtmlPath);


exit;
