<?php
include __DIR__ . '/../config/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['edate'], $input['session'], $input['aid'], $input['ename'], $input['etype'])) {
  http_response_code(400);
  echo "Invalid request";
  exit;
}

function formatRegRangesPHP($regNos)
{
  if (empty($regNos)) return "";

  sort($regNos);

  $groups = [];

  foreach ($regNos as $r) {
    $prefix = substr($r, 0, -3);
    $num = intval(substr($r, -3));

    if (!isset($groups[$prefix])) {
      $groups[$prefix] = [];
    }

    $groups[$prefix][] = $num;
  }

  $result = [];

  foreach ($groups as $prefix => $nums) {

    sort($nums);

    $start = $nums[0];
    $prev  = $nums[0];

    for ($i = 1; $i <= count($nums); $i++) {

      if (!isset($nums[$i]) || $nums[$i] != $prev + 1) {

        if ($start == $prev) {
          $result[] = $prefix . str_pad($start, 3, '0', STR_PAD_LEFT);
        } else {
          $result[] =
            $prefix . str_pad($start, 3, '0', STR_PAD_LEFT) .
            "-" .
            $prefix . str_pad($prev, 3, '0', STR_PAD_LEFT);
        }

        if (isset($nums[$i])) {
          $start = $nums[$i];
        }
      }

      if (isset($nums[$i])) {
        $prev = $nums[$i];
      }
    }
  }

  return implode(", ", $result);
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
$etype = $input['etype'];
if ($etype == 1) {
  $sql = "
SELECT s.rollno, sad.room, s.branch, s.semester, sad.electiveCourseId as course
FROM seating_allocation_data sad
JOIN students s ON sad.reg_no = s.reg_no
WHERE sad.edate=? AND sad.session=? AND sad.aid=?
ORDER BY s.semester, s.branch, s.rollno
";
} elseif ($etype == 2) {
  $sql = "
SELECT sad.reg_no as rollno, sad.room, a.branch,sad.seat,sad.electiveCourseId as course FROM seating_allocation_data sad JOIN ( SELECT DISTINCT student, branch FROM appearing_list ) a  ON sad.reg_no = a.student
    where sad.edate= ? and sad.session= ? and sad.aid=? ORDER BY sad.electiveCourseId,a.branch,sad.reg_no";
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $edate, $session, $aid);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$tableHtml = "";

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



  foreach ($data as $sem => $roomInfo) {

    $tableHtml .= "
    <table border=1 style='width:90%; margin: 15px auto; border-collapse:collapse;' class='report pdf-page'>
      <tr>
        <th colspan='5'>Muthoot Institute of Technology and Science (Autonomous)</th>
      </tr>
      <tr>
        <th colspan='5'>Semester {$sem}, {$ename}</th>
      </tr>
      <tr>
        <th colspan='5'>Hall Allotment Plan</th>
      </tr>
      <tr>
        <th colspan='5'>Date of Exam: {$edate} &nbsp;&nbsp; Session: {$session}</th>
      </tr>
      <tr>
        <th>Branch</th>
        <th>Hall</th>
        <th>Course</th>
        <th>Roll No.</th>
        <th>Total no of students</th>
      </tr>
    ";

    foreach ($roomInfo as $branch => $rinfo) {

      $rowspan = 0;

      foreach ($rinfo as $roomRolls) {
        $coursesInRoom = [];

        foreach ($roomRolls as [$roll, $course]) {
          $course = str_replace(' ', '', $course);
          $coursesInRoom[$course] = true;
        }

        $rowspan += count($coursesInRoom);
      }

      $rowspan += 1;

      $tableHtml .= "
      <tr>
        <th rowspan='{$rowspan}'>{$branch}</th>
      </tr>
    ";

      foreach ($rinfo as $room => $rolls) {

        $groupedByCourse = [];

        foreach ($rolls as [$roll, $course]) {
          $course = str_replace(' ', '', $course);
          $groupedByCourse[$course][] = $roll;
        }

        foreach ($groupedByCourse as $course => $courseRolls) {

          $range = formatRangesPHP($courseRolls);
          $count = count($courseRolls);

          $tableHtml .= "
              <tr>
                <td align='center' style='min-width:100px;'>{$room}</td>
                <td align='center' class='secondary'>{$course}</td>
                <td align='center' class='secondary'>{$range}</td>
                <td align='center' class='secondary'>{$count}</td>
              </tr>
            ";
        }
      }
    }

    $tableHtml .= "</table>";
  }
} elseif ($etype == 2) {
  while ($row = $result->fetch_assoc()) {

    $branch = $row['branch'];
    $room   = $row['room'];
    $roll   = $row['rollno'];
    $course = $row['course'];


    if (!isset($data[$branch])) {
      $data[$branch] = [];
    }

    if (!isset($data[$branch][$room])) {
      $data[$branch][$room] = [];
    }

    $data[$branch][$room][] = [$roll, $course];
  }


  $tableHtml .= "
    <table border=1 style='width:90%; margin: 15px auto; border-collapse:collapse;' class='report pdf-page'>
      <tr>
        <th colspan='5'>Muthoot Institute of Technology and Science (Autonomous)</th>
      </tr>
      <tr>
        <th colspan='5'>{$ename}</th>
      </tr>
      <tr>
        <th colspan='5'>Hall Allotment Plan</th>
      </tr>
      <tr>
        <th colspan='5'>Date of Exam: {$edate} &nbsp;&nbsp; Session: {$session}</th>
      </tr>
      <tr>
        <th>Branch</th>
        <th>Hall</th>
        <th>Course</th>
        <th>Roll No.</th>
        <th>Total no of students</th>
      </tr>
    ";


  foreach ($data as $branch => $rinfo) {

    $rowspan = 0;

    foreach ($rinfo as $roomRolls) {
      $coursesInRoom = [];

      foreach ($roomRolls as [$roll, $course]) {
        $course = str_replace(' ', '', $course);
        $coursesInRoom[$course] = true;
      }

      $rowspan += count($coursesInRoom);
    }

    $rowspan += 1;

    $tableHtml .= "
      <tr>
        <th rowspan='{$rowspan}'>{$branch}</th>
      </tr>
    ";

    foreach ($rinfo as $room => $rolls) {

      $groupedByCourse = [];

      foreach ($rolls as [$roll, $course]) {
        $course = str_replace(' ', '', $course);
        $groupedByCourse[$course][] = $roll;
      }

      foreach ($groupedByCourse as $course => $courseRolls) {

        $range = formatRegRangesPHP($courseRolls);
        $count = count($courseRolls);

        $tableHtml .= "
              <tr>
                <td align='center' style='min-width:100px;'>{$room}</td>
                <td align='center' class='secondary'>{$course}</td>
                <td align='center' class='secondary'>{$range}</td>
                <td align='center' class='secondary'>{$count}</td>
              </tr>
            ";
      }
    }
  }

  $tableHtml .= "</table>";
}

$html = "
<html>
<head>
<style>
@page {
  size: A3;
  margin: 12mm;
}

/* Table layout */
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: auto;           /* FIX: never use fixed */
  page-break-after: always;
  font-size: 20px;
}

/* Prevent rows from splitting */
tr {
  page-break-inside: avoid !important;
  break-inside: avoid !important;
}

td, th {
  padding: 8px;
  page-break-inside: avoid !important;
  break-inside: avoid !important;
  vertical-align: middle;
  font-weight:600;
}



/* Wrap long roll number ranges safely */
td {
  word-break: normal;           /* FIX: do NOT use break-all */
  overflow-wrap: anywhere;     /* allows wrapping only when needed */
}

/* Kill Tailwind / flex / layout bugs */
.report, .pdf-page, body, html {
  display: block !important;
  position: static !important;
  overflow: visible !important;
}



</style>
</head>

<body>
{$tableHtml}
</body>

</html>
";

file_put_contents("temp.html", $html);


exec('"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe" --enable-local-file-access --page-size A3 temp.html report.pdf');


header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=$edate-$session-Seating_Report.pdf");

readfile("report.pdf");

unlink("temp.html");
unlink("report.pdf");

exit;
