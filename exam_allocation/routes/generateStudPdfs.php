<?php
include __DIR__ . '/../config/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['edate'], $input['session'], $input['aid'], $input['ename'],  $input['roomId'], $input['roomType'], $input['aSlots'], $input['bSlots'])) {
  http_response_code(400);
  echo "Invalid request";
  exit;
}



$edate = $input['edate'];
$session = $input['session'];
$aid = $input['aid'];
$ename = $input['ename'];
$roomType = $input['roomType'];
$roomId = $input['roomId'];
$aSlots = $input['aSlots'];
$bSlots = $input['bSlots'];

$tableHtml = "";

if ($roomType == 'Drawing') {

  // ---- DRAWING ROOM (SINGLE COLUMN) ----
  $tableHtml .= "
  <div class='pdf-page ding'>
    <table border='1' style='width:100%;'>
    <tr>
      <th colspan='2'>Muthoot Institute of Technology and Science (Autonomous)</th>
    </tr>
    <tr>
      <th colspan='2'>$ename</th>
    </tr>
    <tr>
      <th colspan='2'>Date of Exam: $edate &nbsp;&nbsp; Session: $session</th>
    </tr>
    <tr>
      <th colspan='2'>Hall Seating Arrangement</th>
    </tr>
    <tr>
      <th colspan='2'>Hall No: $roomId</th>
    </tr>
  </table>
  <table width='100%' border='0'>
    <tr>

      <!-- LEFT SIDE (A SLOTS) -->
      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
  ";

  foreach ($aSlots as $student) {

    $semester = $student['semester'];
    $branch   = $student['branch'];
    $rollno   = $student['rollno'];
    $seat     = $student['seat'];

    $tableHtml .= "
      <tr>
        <td align='center'>S{$semester} {$branch}</td>
        <td align='center'>{$rollno}</td>
        <td align='center'>{$seat}</td>
      </tr>
    ";
  }

  $tableHtml .= "
        </table>
      </td>

      <!-- RIGHT SIDE (B SLOTS) -->
      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
  ";

  foreach ($bSlots as $student) {

    $semester = $student['semester'];
    $branch   = $student['branch'];
    $rollno   = $student['rollno'];
    $seat     = $student['seat'];

    $tableHtml .= "
      <tr>
        <td align='center'>S{$semester} {$branch}</td>
        <td align='center'>{$rollno}</td>
        <td align='center'>{$seat}</td>
      </tr>
    ";
  }

  $tableHtml .= "
        </table>

      </td>
    </tr>
  </table>
  </div>
  ";
} else {

  // ---- NORMAL ROOM (TWO COLUMN LAYOUT WITHOUT FLEX) ----

  $tableHtml .= "
  <div class='pdf-page ding'>
    <table border='1' style='width:100%;'>
    <tr>
      <th colspan='2'>Muthoot Institute of Technology and Science (Autonomous)</th>
    </tr>
    <tr>
      <th colspan='2'>$ename</th>
    </tr>
    <tr>
      <th colspan='2'>Date of Exam: $edate &nbsp;&nbsp; Session: $session</th>
    </tr>
    <tr>
      <th colspan='2'>Hall Seating Arrangement</th>
    </tr>
    <tr>
      <th colspan='2'>Hall No: $roomId</th>
    </tr>
  </table>
  <table width='100%' border='0'>
    <tr>

      <!-- LEFT SIDE (A SLOTS) -->
      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
  ";

  foreach ($aSlots as $student) {

    $semester = $student['semester'];
    $branch   = $student['branch'];
    $rollno   = $student['rollno'];
    $seat     = $student['seat'];

    $tableHtml .= "
      <tr>
        <td align='center'>S{$semester} {$branch}</td>
        <td align='center'>{$rollno}</td>
        <td align='center'>{$seat}</td>
      </tr>
    ";
  }

  $tableHtml .= "
        </table>
      </td>

      <!-- RIGHT SIDE (B SLOTS) -->
      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
  ";

  foreach ($bSlots as $student) {

    $semester = $student['semester'];
    $branch   = $student['branch'];
    $rollno   = $student['rollno'];
    $seat     = $student['seat'];

    $tableHtml .= "
      <tr>
        <td align='center'>S{$semester} {$branch}</td>
        <td align='center'>{$rollno}</td>
        <td align='center'>{$seat}</td>
      </tr>
    ";
  }

  $tableHtml .= "
        </table>

      </td>
    </tr>
  </table>
  </div>
  ";
}

// ---- FINAL HTML WRAPPER ----

$html = "
<html>
<head>
<style>
.pdf-page {
  page-break-after: always;
}

table {
  border-collapse: collapse;
  page-break-inside: avoid;
}

.ding{
border:1px solid black;
}

th, td {
  padding: 4px;
}
</style>
</head>

<body>
{$tableHtml}
</body>

</html>
";

file_put_contents("temp.html", $html);

exec('"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe" --enable-local-file-access temp.html report.pdf');

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=Hall_Report.pdf");

readfile("report.pdf");

unlink("temp.html");
unlink("report.pdf");

exit;
