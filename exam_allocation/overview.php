<?php
include 'config/db_connect.php';
include 'config/functions.php';
if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}

$result = getSeatingAllocations($conn);
$ename = $_SESSION['ename'] ?? null;
$aid = $_SESSION['aid'] ?? null;

if (isset($_GET['ename'])) {
  $_SESSION['ename'] = $_GET['ename'];
}

if (isset($_GET['aid'])) {
  $_SESSION['aid'] = $_GET['aid'];
  $examData = getSeatingExamData($conn, $_GET['aid']);
}

if (isset($_GET['deleteId'])) {
  $deleteId = $_GET['deleteId'];
  deleteSeatingData($conn, $deleteId);
  header("Location: overview.php");
  exit;
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
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./styles/output.css">
</head>

<body x-data="{on: false}" class="bg-black h-screen flex flex-col relative">

  <header class="border-b-2 min-h-[100px] h-fit border-[#FFFFFF] flex relative">
    <div class="flex items-center justify-between w-full">
      <div class="w-[25px] h-[25px] bg-[#9E9B9B] border-3 rounded-sm border-[#FFFEFE] ml-3"></div>
      <div x-data="{ open: false }" class="relative inline-block">
        <div @click="open = !open" class="cursor-pointer w-[25px] h-[25px] bg-gradient-to-b from-[#18C088] via-[#10855E] via-70% via-[#0D6D4D] to-[#0B5A40] rounded-xl border-2 border-[#828282] mr-3"></div>
        <div x-show="open"
          @click.outside="open = false"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          class="absolute top-8 right-1 h-9 w-[120px] bg-[#373737] border p-3 flex items-center rounded-md shadow-lg">
          <a href="logout.php" class="flex items-center justify-center w-full gap-2 hover:bg-[#5C5555] rounded-sm transition-colors duration-200 cursor-pointer select-none">
            <img src="./assets/logout.png" alt="logout img">
            <p class="text-sm">Log out</p>
          </a>
        </div>
      </div>
    </div>
    <div class="flex absolute -bottom-0 w-[70%] justify-evenly text-[18px] ml-5 select-none nav-bar">
      <img src="./assets/ham_menu.png" alt="hamburger menu" class="hidden">
      <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='overview.php'">Overview</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='seating_plan.php?step=1'">Seating Plan</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='exams.php'">Exams</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='view_rooms.php'">Rooms</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='students.php'">Students</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='programmes.php'">Programmes</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex">
    <section class="relative flex-1 flex items-center justify-center js-seating-section">
      <div class="w-[100%] h-[600px] overflow-auto flex flex-wrap">
        <?php if ($result->num_rows > 0 && !isset($_GET['aid'])): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="min-w-[300px] max-w-[390px] bg-black h-[250px] m-4 rounded-[8px] border border-gray-500 flex flex-col relative">
              <div class="h-[50px] border-b border-b-gray-500  flex items-center justify-between gap-3">
                <p class="ml-2">
                  <?= $row['ename'] ?>
                </p>
                <a onclick="return confirm('Delete this seating related data?');" href="overview.php?deleteId=<?= $row['aid'] ?>" class="bg-white h-[25px] w-[25px] mr-4 rounded-[4px] flex items-center justify-center">
                  <img src="assets/delete.png" class="w-[20px]" alt="deleteIcon">
                </a>
              </div>
              <div class="flex flex-1 flex-col justify-center">
                <p class="m-2">Created at : <?= $row['created_at'] ?></p>
                <p class="m-2">Exam Start Date : <?= $row['sdate'] ?></p>
                <p class="m-2">Exam End Date : <?= $row['edate'] ?></p>
              </div>
              <a onclick="window.location.href='overview.php?aid=<?= $row['aid'] ?>&ename=<?= $row['ename'] ?>'" data-sal-id="<?= $row['aid'] ?>" class="secondary-font cursor-pointer js-view-seating-plan w-fit h-[20px] bg-white m-auto mb-3 p-3 rounded-[4px] flex items-center justify-center">View</a>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
        <?php if (isset($_GET['aid'])): ?>
          <section class="flex-1 flex-col">
            <div class="w-[70%] mt-6 p-2 relative border rounded-[10px] ml-2">
              <p class="secondary absolute -top-6 left-2">Exam Dates</p>
              <div class="flex flex-col w-[95%] m-auto  gap-2 overflow-auto h-[280px] items-start  py-4 relative">
                <?php if ($examData->num_rows > 0): ?>
                  <?php while ($row = $examData->fetch_assoc()): ?>
                    <div
                      class="py-4 min-w-[95%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-start hover:opacity-80 transition-all ease-in-out js-seating-blocks"
                      data-edate="<?= $row['edate'] ?>" data-etype="<?= $row['etype'] ?>" data-session="<?= $row['session'] ?>" data-aid="<?= $_GET['aid'] ?>" data-ename="<?= htmlspecialchars($row['ename'], ENT_QUOTES) ?>">
                      <div class="w-fit flex flex-col ml-2">
                        <p class="text-md select-none">Exam Date - <?= $row['edate'] ?></p>
                        <p class="text-md select-none">Session - <?= $row['session'] ?></p>
                      </div>
                    </div>
                  <?php endwhile; ?>
                <?php else: ?>
                  <p>No data found.</p>
                <?php endif; ?>
              </div>
            </div>
            <div class="w-[70%] mt-8 p-2 relative border rounded-[10px] ml-2">
              <p class="secondary absolute -top-6 left-2 ">Rooms</p>
              <div class="flex flex-col w-full gap-2 overflow-y-auto h-[220px] items-start js-seated-rooms-div">

              </div>
            </div>
          </section>
          <section class="flex-1 flex-col">
            <div class="w-[300px] h-[60px] bg-[#000000] mx-auto mb-5 rounded-[8px] border border-white flex items-center justify-center gap-2">
              <button @click="on=true" class="tooltip w-[45px] h-[45px] bg-white rounded-full border-2 border-gray-500 flex items-center justify-center relative">
                <p class="absolute -bottom-8 right-0 text-nowrap p-1 text-sm bg-[#686868] rounded-[4px] tooltiptext">View Semester Wise Reports</p>
                <img src="./assets/home.png" alt="home-icon">
              </button>
              <button class="tooltip w-[45px] h-[45px] bg-white rounded-full border-2 border-gray-500 flex items-center justify-center js-download-room-report relative">
                <img src="./assets/download_2.png" alt="download-icon">
                <p class="absolute -bottom-8 right-0 text-nowrap p-1 text-sm bg-[#686868] rounded-[4px] tooltiptext">Download Hall Report (.pdf)</p>
              </button>
              <button class="tooltip w-[45px] h-[45px] bg-white rounded-full border-2 border-gray-500 flex items-center justify-center js-download-room-xls-report relative">
                <img src="./assets/export_xls.png" alt="download-icon">
                <p class="absolute -bottom-8 right-0 text-nowrap p-1 text-sm bg-[#686868] rounded-[4px] tooltiptext">Download Hall Report (.xls)</p>
              </button>
            </div>
            <div class="w-full h-[500px] overflow-auto  js-seating-data-container">

            </div>
          </section>
        <?php endif; ?>
      </div>
    </section>
    <div class="absolute inset-0 flex flex-col items-center justify-start bg-black z-40 opacity-100"
      x-show="on">
      <div class="w-full h-[20px] my-5 flex items-center justify-end">
        <button class="tooltip cursor-pointer relative z-50" id="download-hall-reports"><img src="./assets/download.png" alt="download icon" class="mr-4 h-[20px]">
          <p class="absolute -bottom-8 right-0 text-nowrap p-1 text-sm z-50 bg-[#686868] rounded-[4px] tooltiptext">Download Report (.pdf)</p>
        </button>
        <button class="tooltip cursor-pointer relative z-50" id="download-hall-xls-reports"><img src="./assets/xls-download.png" alt="download icon" class="mr-4 h-[20px]">
          <p class="absolute -bottom-8 right-0 text-nowrap p-1 text-sm z-50 bg-[#686868] rounded-[4px] tooltiptext">Download Report (.xls)</p>
        </button>
        <img @click="on = false" src="./assets/close.png" alt="close icon" class="mr-4 h-[20px] cursor-pointer">
      </div>

      <div class="flex-1 w-full bg-black z-40 flex flex-col items-center justify-start  rounded-[3px] relative overflow-auto js-hall-reports-div">

      </div>
    </div>
  </main>
  <script type="module" src="./scripts/app.js"></script>
</body>

</html>