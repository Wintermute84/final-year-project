<?php
include __DIR__ . '/../config/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['edate'], $input['session'], $input['aid'], $input['ename'])) {
  http_response_code(400);
  echo "Invalid request";
  exit;
}

function formatRangesPHP($numbers)
{
  sort($numbers);

  $ranges = [];
  $start = $numbers[0];
  $prev = $numbers[0];

  for ($i = 1; $i < count($numbers); $i++) {

    if ($numbers[$i] == $prev + 1) {
      $prev = $numbers[$i];
    } else {

      if ($start == $prev)
        $ranges[] = $start;
      else
        $ranges[] = $start . "-" . $prev;

      $start = $numbers[$i];
      $prev = $numbers[$i];
    }
  }

  if ($start == $prev)
    $ranges[] = $start;
  else
    $ranges[] = $start . "-" . $prev;

  return implode(", ", $ranges);
}

$edate = $input['edate'];
$session = $input['session'];
$aid = $input['aid'];
$ename = $input['ename'];

$sql = "
SELECT s.rollno, sad.room, s.branch, s.semester
FROM seating_allocation_data sad
JOIN students s ON sad.reg_no = s.reg_no
WHERE sad.edate=? AND sad.session=? AND sad.aid=?
ORDER BY s.semester, s.branch, s.rollno
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

  if (!isset($data[$sem])) {
    $data[$sem] = [];
  }

  if (!isset($data[$sem][$branch])) {
    $data[$sem][$branch] = [];
  }

  if (!isset($data[$sem][$branch][$room])) {
    $data[$sem][$branch][$room] = [];
  }

  $data[$sem][$branch][$room][] = $roll;
}


$tableHtml = "";

foreach ($data as $sem => $roomInfo) {

  $tableHtml .= "
    <table border=1 style='width:88%; margin:15px; border-collapse:collapse;' class='report pdf-page'>
      <tr>
        <th colspan='4'>Muthoot Institute of Technology and Science (Autonomous)</th>
      </tr>
      <tr>
        <th colspan='4'>Semester {$sem}, {$ename}</th>
      </tr>
      <tr>
        <th colspan='4'>Hall Allotment Plan</th>
      </tr>
      <tr>
        <th colspan='4'>Date of Exam: {$edate} &nbsp;&nbsp; Session: {$session}</th>
      </tr>
      <tr>
        <th>Branch</th>
        <th>Hall</th>
        <th>Roll No.</th>
        <th>Total no of students</th>
      </tr>
    ";

  foreach ($roomInfo as $branch => $rinfo) {

    $rowspan = count($rinfo) + 1;

    $tableHtml .= "
        <tr>
          <th rowspan='{$rowspan}'>{$branch}</th>
        ";

    foreach ($rinfo as $room => $rolls) {

      $range = formatRangesPHP($rolls);
      $count = count($rolls);

      $tableHtml .= "
            <tr>
              <td>{$room}</td>
              <td>{$range}</td>
              <td>{$count}</td>
            </tr>
            ";
    }

    $tableHtml .= "</tr>";
  }

  $tableHtml .= "</table>";
}


$html = "
<html>
<head>
<style>
table { width:100%; border-collapse: collapse; page-break-after: always; }
th, td { padding:6px; }
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
header("Content-Disposition: attachment; filename=$edate-$session-Seating_Report.pdf");

readfile("report.pdf");

unlink("temp.html");
unlink("report.pdf");

exit;
