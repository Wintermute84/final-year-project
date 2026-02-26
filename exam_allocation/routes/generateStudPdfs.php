<?php
include __DIR__ . '/../config/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['edate'], $input['session'], $input['aid'], $input['ename'],  $input['roomId'], $input['roomType'], $input['aSlots'], $input['bSlots'])) {
  http_response_code(400);
  echo "Invalid request" . $input['edate'] . $input['session'] . $input['aid'] . $input['ename'] . $input['roomId'] . $input['roomType'] . serialize($input['aSlots']) . serialize($input['bSlots']);
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
$etype = $input['etype'];

$aCounts = [];
foreach ($aSlots as $s) {
  $sem    = $s['semester'] ?? '';
  $key    = $sem . '|' . $s['branch'] . '|' . $s['course'];
  $aCounts[$key] = ($aCounts[$key] ?? 0) + 1;
}

$aPrinted = [];
foreach ($aSlots as &$s) {

  $sem = $s['semester'] ?? '';
  $key = $sem . '|' . $s['branch'] . '|' . $s['course'];

  $s['rowspan']    = 0;
  $s['printGroup'] = false;

  if (!isset($aPrinted[$key])) {
    $s['rowspan']    = $aCounts[$key];
    $s['printGroup'] = true;
    $aPrinted[$key]  = true;
  }
}
unset($s);


$bCounts = [];
foreach ($bSlots as $s) {
  $sem    = $s['semester'] ?? '';
  $key    = $sem . '|' . $s['branch'] . '|' . $s['course'];
  $bCounts[$key] = ($bCounts[$key] ?? 0) + 1;
}

$bPrinted = [];
foreach ($bSlots as &$s) {

  $sem = $s['semester'] ?? '';
  $key = $sem . '|' . $s['branch'] . '|' . $s['course'];

  $s['rowspan']    = 0;
  $s['printGroup'] = false;

  if (!isset($bPrinted[$key])) {
    $s['rowspan']    = $bCounts[$key];
    $s['printGroup'] = true;
    $bPrinted[$key]  = true;
  }
}
unset($s);

$tableHtml = "
<div class='pdf-page ding'>
  <table border='1' style='width:100%;'>
    <tr><th colspan='2'>Muthoot Institute of Technology and Science (Autonomous)</th></tr>
    <tr><th colspan='2'>$ename</th></tr>
    <tr><th colspan='2'>Date of Exam: $edate &nbsp;&nbsp; Session: $session</th></tr>
    <tr><th colspan='2'>Hall Seating Arrangement</th></tr>
    <tr><th colspan='2'>Hall No: $roomId</th></tr>
  </table>

  <table width='100%' border='0'>
    <tr>

      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th style='width:40%;'>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
";


foreach ($aSlots as $student) {

  $semester = $student['semester'] ?? null;
  $branch   = $student['branch'];
  $rollno   = $etype == 1 ? $student['rollno'] : $student['reg_no'];
  $seat     = $student['seat'];
  $course   = $student['course'];

  $tableHtml .= "<tr>";

  if ($student['printGroup']) {

    $label   = "
      <div>
        <div>" . ($semester ? "S$semester" : "") . " $branch</div>
        <div style='font-size:16px;'>($course)</div>
      </div>
    ";

    $rowspan = $student['rowspan'];

    $tableHtml .= "
      <td align='center' rowspan='{$rowspan}' style='vertical-align:middle; width:40%; padding:4px;'>
        {$label}
      </td>
    ";
  }

  $tableHtml .= "
      <td align='center'>{$rollno}</td>
      <td align='center'>{$seat}</td>
    </tr>
  ";
}

$tableHtml .= "
        </table>
      </td>

      <td width='50%' valign='top'>

        <table border='1' width='100%' style='border-collapse:collapse;'>
          <tr>
            <th style='width:40%;'>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
";


foreach ($bSlots as $student) {

  $semester = $student['semester'] ?? null;
  $branch   = $student['branch'];
  $rollno   = $etype == 1 ? $student['rollno'] : $student['reg_no'];
  $seat     = $student['seat'];
  $course   = $student['course'];

  $tableHtml .= "<tr>";

  if ($student['printGroup']) {

    $label   = "
      <div>
        <div>" . ($semester ? "S$semester" : "") . " $branch</div>
        <div style='font-size:16px;'>($course)</div>
      </div>
    ";

    $rowspan = $student['rowspan'];

    $tableHtml .= "
      <td align='center' rowspan='{$rowspan}' style='vertical-align:middle;'>
        {$label}
      </td>
    ";
  }

  $tableHtml .= "
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

$uploadDirectory = __DIR__ . "/../Reports/{$edate}_{$session}/";

if (!is_dir($uploadDirectory)) {
  mkdir($uploadDirectory, 0777, true);
}

$fileName = "{$roomId}_{$edate}_{$session}.pdf";

$tempHtmlPath = $uploadDirectory . "temp.html";
$pdfPath      = $uploadDirectory . $fileName;

file_put_contents($tempHtmlPath, $html);

exec('"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe" --enable-local-file-access --page-size A3 "' . $tempHtmlPath . '" "' . $pdfPath . '"');

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$fileName\"");

readfile($pdfPath);
unlink($tempHtmlPath);


exit;
