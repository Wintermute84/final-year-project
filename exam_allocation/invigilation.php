<?php
include 'config/db_connect.php';
include 'config/functions.php';

$selected_eid = isset($_POST['eid']) && $_POST['eid'] !== '' ? intval($_POST['eid']) : (isset($_GET['eid']) && $_GET['eid'] !== '' ? intval($_GET['eid']) : null);

if (isset($_POST['download_csv'])) {
  $facs = getGlobalFacultyMatrix($conn, $selected_eid);

  header('Content-Type: text/csv');
  $filename = $selected_eid ? "faculty_matrix_eid_{$selected_eid}.csv" : "global_availability_matrix.csv";
  header('Content-Disposition: attachment; filename="'.$filename.'"');

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
    while($row = $exams_res->fetch_assoc()) {
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
          class="absolute top-8 right-1 h-9 w-[120px] bg-[#373737] border p-3 flex items-center shadow-lg rounded-md z-50">
          <a href="logout.php"
            class="flex items-center justify-center w-full gap-2 hover:bg-[#5C5555] rounded-sm transition-colors duration-200 cursor-pointer select-none">
            <img src="./assets/logout.png" alt="logout img">
            <p class="text-sm">Log out</p>
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
      <p class="secondary cursor-pointer" onclick="window.location.href='programmes.php'">Programmes</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex overflow-hidden">
    <section class="w-full h-full overflow-y-auto p-6 bg-black relative">
      <div
        style="display: flex; flex-direction: column; gap: 1rem; border-bottom: 1px solid #333; padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div>
          <h2 class="text-2xl font-bold mb-2 text-[#18C088]">Global Availability Matrix</h2>
          <p class="text-gray-400">
            <?= $selected_eid ? "Viewing availability for Exam ID: " . $selected_eid : "Viewing availability across all upcoming exam slots." ?>
          </p>
        </div>
        
        <form method="GET" action="invigilation.php" id="examFilterForm" class="flex items-center gap-3 bg-[#222] p-3 rounded-lg border border-[#333]">
          <label class="text-gray-300 font-medium">Filter by Exam:</label>
          <select name="eid" onchange="document.getElementById('examFilterForm').submit()" class="bg-[#333] text-white p-2 rounded-md border border-[#444] outline-none focus:border-[#18C088]">
            <option value="">-- All Exams --</option>
            <?php foreach($all_exams as $ex): ?>
              <option value="<?= $ex['eid'] ?>" <?= $selected_eid === intval($ex['eid']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ex['ename']) ?> (EID: <?= $ex['eid'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </form>

        <form method="POST" action="invigilation.php" style="width: 100%;">
          <?php if($selected_eid): ?>
            <input type="hidden" name="eid" value="<?= htmlspecialchars($selected_eid) ?>">
          <?php endif; ?>
          <button type="submit" name="download_csv"
            style="width: 100%; background-color: #18C088; color: black; padding: 16px 24px; border-radius: 8px; font-weight: bold; font-size: 18px; text-align: center; cursor: pointer; border: none; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0px 4px 15px rgba(24,192,136,0.4);"
            onmouseover="this.style.backgroundColor='#10855E'" onmouseout="this.style.backgroundColor='#18C088'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            CLICK HERE TO DOWNLOAD CSV MATRIX
          </button>
        </form>
      </div>


    </section>
  </main>
  <button
    class="absolute bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center bottom-8 right-3 cursor-pointer"><img
      class="h-[25px]" src="assets/add.png" alt="add icon"></button>
  <script type="module" src="./scripts/app.js"></script>
</body>

</html>
