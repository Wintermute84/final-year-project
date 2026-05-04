<?php
include 'config/db_connect.php';
include 'config/functions.php';

if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}

$selected_eid = isset($_GET['eid']) ? $_GET['eid'] : (isset($_POST['eid']) ? $_POST['eid'] : null);

if (isset($_POST['download_csv'])) {
  $facs = getGlobalFacultyMatrix($conn, $selected_eid);

  header('Content-Type: text/csv');
  $filename = $selected_eid ? "faculty_matrix_eid_{$selected_eid}.csv" : "global_availability_matrix.csv";
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  $output = fopen('php://output', 'w');
  if (!empty($facs)) {
    $first = reset($facs);
    $headers = ['S.No', 'Faculty Name', 'Designation', 'Total Free Slots'];
    $matrix_slots = array_keys($first['matrix']);
    foreach ($matrix_slots as $m)
      $headers[] = $m;
    fputcsv($output, $headers);

    $sno = 1;
    foreach ($facs as $f) {
      $row = [$sno++, $f['faculty'], $f['designation'], $f['total_free_slots']];
      foreach ($matrix_slots as $m) {
        $row[] = $f['matrix'][$m];
      }
      fputcsv($output, $row);
    }
  } else {
    fputcsv($output, ['No faculties available.']);
  }
  fclose($output);
  exit;
}

if (isset($_POST['generate_allocation']) && isset($selected_eid)) {
  $eid = intval($selected_eid);
  $max_associate_dutycap = intval($_POST['max_cap'] ?? 2);
  try {
    $result_data = allocateGlobalInvigilation($conn, $eid, $max_associate_dutycap);
    $csv_matrix  = $result_data['csv_matrix'];
    $matrix_slots = $result_data['slot_keys'];
    $shortfalls  = $result_data['shortfalls'] ?? [];

    $csv_content = buildCsvContent($csv_matrix, $matrix_slots, $eid);
    $filename    = "final_assigned_duty_matrix_eid_{$eid}.csv";

    if (empty($shortfalls)) {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '"');
      echo $csv_content;
      exit;
    }

    $_SESSION['pending_csv'] = ['content' => $csv_content, 'filename' => $filename];
    $_SESSION['pending_eid'] = $eid;
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}

$ename = isset($_GET['ename']) ? $_GET['ename'] : null;
$exam = getExams($conn, 'all');
$eid = isset($_GET['eid']) ? $_GET['eid'] : null;
$examType = isset($_GET['examtype']) ? $_GET['examtype'] : null;
$examSlots = null;
if (isset($examType) and isset($eid)) {
  $examSlots = getExamSlots($conn, $eid, $examType);
}
if (isset($_POST['download_csv'])) {
  $facs = getGlobalFacultyMatrix($conn, $selected_eid);

  header('Content-Type: text/csv');
  $filename = $selected_eid ? "faculty_matrix_eid_{$selected_eid}.csv" : "global_availability_matrix.csv";
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  $output = fopen('php://output', 'w');
  if (!empty($facs)) {
    $first = reset($facs);
    $headers = ['S.No', 'Faculty Name', 'Designation', 'Total Free Slots'];
    $matrix_slots = array_keys($first['matrix']);
    foreach ($matrix_slots as $m)
      $headers[] = $m;
    fputcsv($output, $headers);

    $sno = 1;
    foreach ($facs as $f) {
      $row = [$sno++, $f['faculty'], $f['designation'], $f['total_free_slots']];
      foreach ($matrix_slots as $m) {
        $row[] = $f['matrix'][$m];
      }
      fputcsv($output, $row);
    }
  } else {
    fputcsv($output, ['No faculties available.']);
  }
  fclose($output);
  exit;
}



$exams_res = getExams($conn, 'All');
$all_exams = [];
if ($exams_res) {
  while ($row = $exams_res->fetch_assoc()) {
    $all_exams[] = $row;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overview</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./styles/output.css">
</head>

<body x-data="{on: false}" class="bg-black h-screen flex flex-col relative text-white">

  <header class="border-b-2 min-h-[100px] h-fit border-[#FFFFFF] flex relative">
    <div class="flex items-center justify-between w-full">
      <div class="w-[25px] h-[25px] bg-[#9E9B9B] border-3 rounded-sm border-[#FFFEFE] ml-3"></div>
      <div x-data="{ open: false }" class="relative inline-block">
        <div @click="open = !open"
          class="cursor-pointer w-[25px] h-[25px] bg-gradient-to-b from-[#18C088] via-[#10855E] via-70% via-[#0D6D4D] to-[#0B5A40] rounded-xl border-2 border-[#828282] mr-3">
        </div>
        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          class="absolute top-8 right-1 z-50 h-fit  w-[120px] bg-[#373737] border p-3 flex items-center shadow-lg rounded-md  flex-col gap-2">
          <a href="logout.php" class="flex items-center justify-center w-full gap-2 hover:bg-[#5C5555] rounded-sm transition-colors duration-200 cursor-pointer select-none">
            <img src="./assets/logout.png" alt="logout img">
            <p class="text-sm">Log out</p>
          </a>
          <a target="_blank" class="flex items-center justify-center w-full gap-2 hover:bg-[#5C5555] rounded-sm transition-colors duration-200 cursor-pointer select-none" href="https://wintermute84.github.io/desks-directory/">
            <img src="./assets/help.png" alt="faq img">
            <p class="text-sm">Help</p>
          </a>
        </div>
      </div>
    </div>
    <div class="flex absolute -bottom-0 w-[70%] justify-evenly text-[18px] ml-5 select-none nav-bar">
      <img src="./assets/ham_menu.png" alt="hamburger menu" class="hidden">
      <p class="secondary cursor-pointer" onclick="window.location.href='overview.php'">Overview</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='seating_plan.php?step=1'">Seating Plan</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='exams.php'">Exams</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='view_rooms.php'">Rooms</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='students.php'">Students</p>
      <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='faculty.php'">Faculty</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex overflow-hidden">
    <section class="relative flex-1 flex items-center justify-center">
      <div class=" absolute bg-white w-[0.5px] h-[95%] right-0"></div>
      <div class="w-[95%] h-[94%]">
        <p class="text-md mt-1">Select Exam for Invigilation</p>
        <p class="secondary">Invigilation can only be provided for exam whose seating plan has already been generated!</p>
        <div class="w-[100%] h-[500px] bg-[#1c1919] rounded-xl border-2 mt-3 flex flex-col gap-2">
          <div class="w-[95%] h-[100%] overflow-auto m-2">
            <?php if ($exam->num_rows > 0): ?>
              <?php while ($row = $exam->fetch_assoc()): ?>
                <div class="<?= (isset($_GET['eid']) && ($_GET['eid'] == $row['eid'])) ? "active" : "bg-[#151515]" ?> py-4 px-2 w-[full] min-h-[110px] max-h-[120px] cursor-pointer  m-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out">
                  <a href="invigilation.php?eid=<?= $row['eid'] ?>&ename=<?= $row['ename'] ?>&examtype=<?= $row['etype'] ?>" class="w-[70%] flex flex-col ml-2">
                    <p class="text-md truncate text-">Exam Name - <?= $row['ename'] ?></p>
                    <p class="text-md">Exam Type - <?= $row['etype'] == "1" ? "Internal Exam" : "University Exam" ?></p>
                    <p class="text-md">Start Date - <?= $row['sdate'] ?></p>
                    <p class="text-md">End Date - <?= $row['edate'] ?></p>
                  </a>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No Data found.</p>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </section>
    <section class="flex-1 flex items-start justify-center mt-5">
      <div class="w-[100%] h-[100%] flex flex-col ">
        <div class="w-[90%] h-[100px] mx-auto flex items-center justify-center gap-2">
          <form class="flex w-[fit] h-[100%] justify-between items-center gap-2 border border-white p-2 rounded-md" method="POST" action="invigilation.php?eid=<?= isset($selected_eid) ? $selected_eid : null ?>">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
              <label style="color:#ccc; font-size:14px; white-space:nowrap;">Max Duties for Associates:</label>
              <input type="number" name="max_cap" value="2" min="1"
                style="background:#333; color:white; border:1px solid #444; border-radius:6px; padding:8px 10px; width:80px; outline:none;">
            </div>
            <button name="generate_allocation" type="submit" class="w-fit h-[fit] p-2 bg-[#1F1B1B] border border-white  button-secondary rounded-md js-allocate-faculty">Allocate Faculty</button>

          </form>
          <form method="POST" action="invigilation.php"
            onsubmit="return confirm('Are you sure you want to completely RESET the global odometer? This physically drops all duties to 0.');">
            <button class="bg-[#1F1B1B] border border-white w-fit h-[fit] p-2 button-secondary rounded-md" name="reset_duty">Reset Duties</button>
          </form>
          <form method="POST" action="invigilation.php?eid=<?= isset($selected_eid) ? $selected_eid : null ?>">
            <button name="download_csv" class="w-fit h-[fit] p-2 bg-[#1F1B1B] border border-white button-secondary rounded-md">Download Allocation</button>
          </form>

        </div>
    </section>

  </main>
  <script type="module" src="./scripts/app.js"></script>
</body>

</html>