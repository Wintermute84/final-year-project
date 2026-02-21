<?php
include 'config/db_connect.php';
include 'config/functions.php';
if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}

if (isset($_GET['step'])) {
  $_SESSION['step'] = (int) $_GET['step'];
}

$examType = $_SESSION['examType'] ?? null;
$result = getExams($conn, 'All');
$rooms = getRooms($conn, 'All');
//$blocks = getBlocks($conn);
if (isset($_SESSION['eid']) && isset($_SESSION['examType'])) {
  if ($_SESSION['examType'] == 1) {
    $examInfo = getExamInfo($conn, $_SESSION['eid']);
  } elseif ($_SESSION['examType'] == 2) {
    $uniExamInfo = getUniversityExamInfo($conn, $_SESSION['eid']);
  }
}


$step = $_SESSION['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step != 2) {
  $examId = $_POST['selected_exam_id'] ?? null;
  $examType = $_POST['selected_exam_type'] ?? null;
  if (!$examId) {
    die("exam not selected");
  }
  if (!$examType) {
    die("exam type error!");
  }
  $_SESSION['step'] = 2;
  $_SESSION['eid'] = (int) $examId;
  $_SESSION['examType'] = $examType;
  header("Location: seating_plan.php");
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

  <header class="border-b-2 min-h-[100px] h-fit border-[#FFFFFF] flex relative mb-2">
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
      <p class="secondary cursor-pointer" onclick="window.location.href='overview.php'">Overview</p>
      <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='seating_plan.php?step=1'">Seating Plan</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='exams.php'">Exams</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='view_rooms.php'">Rooms</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='students.php'">Students</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='programmes.php'">Programmes</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex items-center justify-center ">
    <?php if ($step === 1): ?>
      <form class="w-[95%] h-[90%] bg-[#0F0E0E]  border-2 border-white rounded-xl" method="POST" enctype="multipart/form-data">
        <div class="m-2 p-2">
          <p class="text-2xl">Generate Seating Plan</p>
          <p class="text-sm ">Step 1 of 4</p>
          <div class="flex gap-2 mt-2">
            <div class="w-[25px] h-[25px] rounded-full flex items-center justify-center text-sm bg-[#55A648]">1</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">2</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">3</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">4</div>
          </div>
          <div class="mt-4">
            <p class="secondary mb-2">Select Exam</p>
            <div class="overflow-auto max-h-[300px] w-[50%] flex flex-col gap-2">
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <div
                    class="py-4 w-[80%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-exam-div"
                    data-eid="<?= $row['eid'] ?>" data-etype="<?= $row['etype'] ?>">
                    <div class="w-fit flex flex-col ml-2">
                      <p class="text-md select-none">Exam Name - <?= $row['ename'] ?></p>
                      <p class="text-md select-none">Exam Type - <?= $row['etype'] == "1" ? "Internal Exam" : "University Exam" ?></p>
                      <p class="text-md select-none">Start Date - <?= $row['sdate'] ?></p>
                      <p class="text-md select-none">End Date - <?= $row['edate'] ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No Data found.</p>
              <?php endif; ?>
            </div>
            <input type="hidden" name="selected_exam_id" id="selectedExamId">
            <input type="hidden" name="selected_exam_type" id="selectedExamType">
            <div class="absolute bottom-13 flex gap-6">
              <button class="h-10 w-[100px] border-white bg-white rounded-md" type="submit">Proceed</button>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>
    <?php if ($step === 2): ?>
      <form class="w-[95%] h-[90%] bg-[#0F0E0E]  border-2 border-white rounded-xl" method="POST" enctype="multipart/form-data">
        <div class="m-2 p-2">
          <p class="text-2xl">Generate Seating Plan</p>
          <p class="text-sm">Step 2 of 4</p>
          <div class="flex gap-2 mt-2">
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">1</div>
            <div class="w-[25px] h-[25px] bg-[#55A648] rounded-full flex items-center justify-center text-sm">2</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">3</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">4</div>
          </div>
          <div class="mt-4">
            <p class="secondary mb-2">Select Rooms</p>
            <div>
              <label for="select-all-blocks" class="secondary flex items-center gap-2 mb-2">
                <input type="checkbox" name="select-all-blocks" class="h-4 w-4 cursor-pointer" id="selectAllRooms">
                Select All
              </label>

            </div>
            <div class="overflow-auto max-h-[300px] w-[100%] flex flex-wrap gap-2">
              <?php if ($rooms->num_rows > 0): ?>
                <?php while ($row = $rooms->fetch_assoc()): ?>
                  <div
                    class="py-4 min-w-[45%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-room-select-div"
                    data-eid="<?= $row['Rid'] ?>"
                    data-capacity="<?= $row['Capacity'] ?>">
                    <div class="w-fit flex flex-col ml-2">
                      <p class="text-md select-none">Block - <?= $row['Block'] ?></p>
                      <p class="text-md select-none">Room No - <?= $row['Room_no'] ?></p>
                      <p class="text-md select-none">Capacity - <?= $row['Capacity'] ?></p>
                      <p class="text-md select-none">Type - <?= $row['Type'] ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No Data found.</p>
              <?php endif; ?>
            </div>
            <input type="hidden" name="selected_exam_id" id="selectedExamId">
            <div class="absolute bottom-13 flex gap-6">
              <a href="seating_plan.php?step=1" class="h-10 w-[100px] border-white bg-[#252323] button-secondary rounded-md flex items-center justify-center">Back</a>
              <button class="h-10 w-[100px] border-white bg-white rounded-md" type="button" id="proceedBtn">Proceed</button>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>
    <?php if ($step === 3): ?>
      <form class="w-[95%] h-[90%] bg-[#0F0E0E]  border-2 border-white rounded-xl" method="POST" enctype="multipart/form-data">
        <div class="m-2 p-2">
          <p class="text-2xl">Generate Seating Plan</p>
          <p class="text-sm">Step 3 of 4</p>
          <div class="flex gap-2 mt-2">
            <div class="w-[25px] h-[25px] rounded-full flex items-center justify-center text-sm bg-[#2C2F2C]">1</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">2</div>
            <div class="w-[25px] h-[25px] bg-[#55A648] rounded-full flex items-center justify-center text-sm">3</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">4</div>
          </div>
          <p class="secondary mt-2">Select Grouping</p>
          <div class="mt-4 flex">
            <div class="overflow-auto max-h-[300px] w-[30%] flex flex-col gap-2">
              <?php if ($examInfo->num_rows > 0): ?>
                <?php while ($row = $examInfo->fetch_assoc()): ?>
                  <div
                    class="py-4 min-w-[45%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-slot-div"
                    data-edate="<?= $row['edate'] ?>" data-session="<?= $row['session'] ?>" data-eid="<?= $_SESSION['eid'] ?>">
                    <div class="w-fit flex flex-col ml-2">
                      <p class="text-md select-none">Exam Date - <?= $row['edate'] ?></p>
                      <p class="text-md select-none">Session - <?= $row['session'] ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No Data found.</p>
              <?php endif; ?>
            </div>
            <div class="flex flex-1 m-1 ml-3 flex-col">
              <p class="secondary">Select and Group Sems (1-2 sems per group)</p>
              <p class="secondary">Grouped Slots will be highlighted in green.</p>
              <div id="numberContainer" class="flex gap-2 mt-2">

              </div>

              <div>
                <button id="createGroupBtn" class="button-secondary w-[120px] p-2 hover:bg-gray-700 rounded-[8px] mt-3" type="button">Create Group</button>
                <button id="deleteGroupBtn" class="button-secondary w-[120px] p-2 hover:bg-red-700 rounded-[8px] mt-3" type="button">Delete Group</button>
              </div>

              <div id="groupPreview" class="mt-2">

              </div>
            </div>
            <div class="absolute bottom-13 flex gap-6">
              <a href="seating_plan.php?step=2" class="h-10 w-[100px] border-white bg-[#252323] button-secondary rounded-md flex items-center justify-center">Back</a>
              <button id="proceedfBtn" class="h-10 w-[100px] border-white bg-white rounded-md" type="button">Proceed</button>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>
    <?php if ($step === 4 && $examType == 1): ?>
      <form class="w-[95%] h-fit bg-[#0F0E0E] relative  border-2 border-white rounded-xl" method="POST" enctype="multipart/form-data">
        <a href="help.php" target="_blank" class="gap-1 h-10 w-20 bg-white absolute right-5 top-4 help-button flex items-center justify-center rounded-[8px] hover:opacity-80">
          <p class="secondary-font">Help</p>
          <img src="assets/help.png" class="h-4" alt="help-icon">
        </a>
        <div class="m-2 p-2">
          <p class="text-2xl">Generate Seating Plan</p>
          <p class="text-sm">Step 4 of 4</p>
          <div class="flex gap-2 mt-2">
            <div class="w-[25px] h-[25px] rounded-full flex items-center justify-center text-sm bg-[#2C2F2C]">1</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">2</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">3</div>
            <div class="w-[25px] h-[25px] bg-[#55A648] rounded-full flex items-center justify-center text-sm">4</div>
          </div>
          <p class="secondary mt-2">Select Shuffle Type.</p>
          <p class="secondary">Grouped Slots will be marked in green.</p>
          <div class="mt-4 flex">
            <div class="overflow-auto max-h-[300px] w-[30%] flex flex-col gap-2">
              <?php if ($examInfo->num_rows > 0): ?>
                <?php while ($row = $examInfo->fetch_assoc()): ?>
                  <div
                    class="<?= "S" . $_SESSION['eid'] . "_" . $row['edate'] . "_" . $row['session'] ?> py-4 min-w-[45%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-shuffle-div"
                    data-edate="<?= $row['edate'] ?>" data-session="<?= $row['session'] ?>" data-eid="<?= $_SESSION['eid'] ?>">
                    <div class="w-fit flex flex-col ml-2">
                      <p class="text-md select-none">Exam Date - <?= $row['edate'] ?></p>
                      <p class="text-md select-none">Session - <?= $row['session'] ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No Data found.</p>
              <?php endif; ?>
            </div>
            <div class="absolute bottom-13 flex gap-6">
              <a href="seating_plan.php?step=3" class="h-10 w-[100px] border-white bg-[#252323] button-secondary rounded-md flex items-center justify-center">Back</a>
              <button id="proceeddBtn" class="h-10 w-[100px] border-white bg-white rounded-md" type="button">Proceed</button>
            </div>
            <div class="flex flex-1 mx-4 gap-2">
              <div class="w-[350px] h-[400px] bg-[#201d1d] border border-2-white p-2 rounded-md relative ">
                <p class="secondary absolute -top-6 left-0">Available Branches</p>
                <div class="w-full h-full overflow-auto available-branches-div flex flex-col gap-3 pr-3">

                </div>
              </div>
              <div class="w-full h-full flex flex-wrap gap-8 ml-2">
                <div class="bg-[#292929] w-[320px] h-[200px] relative border border-2-white rounded-[4px]">
                  <p class="secondary absolute -top-6 left-0 js-shuffle-one shuffle-div"></p>
                  <div class="w-full h-full overflow-auto js-shuffle-one-div flex flex-col gap-2 p-3">

                  </div>
                </div>
                <div class="bg-[#292929] w-[320px] h-[200px] relative border border-2-white rounded-[4px]">
                  <p class="secondary absolute -top-6 left-0 js-shuffle-two  shuffle-div"></p>
                  <div class="w-full h-full overflow-auto js-shuffle-two-div flex flex-col gap-2 p-3">

                  </div>
                </div>
                <div class="bg-[#292929] w-[320px] h-[200px] relative border border-2-white rounded-[4px]">
                  <p class="secondary absolute -top-6 left-0 js-shuffle-three shuffle-div"></p>
                  <div class="w-full h-full overflow-auto js-shuffle-three-div flex flex-col gap-2 p-3">

                  </div>
                </div>
                <div class="bg-[#292929] w-[320px] h-[200px] relative border border-2-white rounded-[4px]">
                  <p class="secondary absolute -top-6 left-0 js-shuffle-four shuffle-div"></p>
                  <div class="w-full h-full overflow-auto js-shuffle-four-div flex flex-col gap-2 p-3">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </form>
    <?php endif; ?>
    <?php if ($step === 4 && $examType == 2): ?>
      <form class="w-[95%] h-fit bg-[#0F0E0E] relative  border-2 border-white rounded-xl" method="POST" enctype="multipart/form-data">
        <a href="help.php" target="_blank" class="gap-1 h-10 w-20 bg-white absolute right-5 top-4 help-button flex items-center justify-center rounded-[8px] hover:opacity-80">
          <p class="secondary-font">Help</p>
          <img src="assets/help.png" class="h-4" alt="help-icon">
        </a>
        <div class="m-2 p-2">
          <p class="text-2xl">Generate Seating Plan</p>
          <p class="text-sm">Step 4 of 4</p>
          <div class="flex gap-2 mt-2">
            <div class="w-[25px] h-[25px] rounded-full flex items-center justify-center text-sm bg-[#2C2F2C]">1</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">2</div>
            <div class="w-[25px] h-[25px] bg-[#2C2F2C] rounded-full flex items-center justify-center text-sm">3</div>
            <div class="w-[25px] h-[25px] bg-[#55A648] rounded-full flex items-center justify-center text-sm">4</div>
          </div>
          <p class="secondary mt-2">Select Shuffle Type.</p>
          <p class="secondary">Grouped Slots will be marked in green.</p>
          <div class="mt-4 flex">
            <div class="overflow-auto max-h-[300px] w-[30%] flex flex-col gap-2">
              <?php if ($uniExamInfo->num_rows > 0): ?>
                <?php while ($row = $uniExamInfo->fetch_assoc()): ?>
                  <div
                    class="<?= "S" . $_SESSION['eid'] . "_" . $row['edate'] . "_" . $row['session'] ?> py-4 min-w-[45%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-uni-shuffle-div"
                    data-edate="<?= $row['edate'] ?>" data-session="<?= $row['session'] ?>" data-eid="<?= $_SESSION['eid'] ?>">
                    <div class="w-fit flex flex-col ml-2">
                      <p class="text-md select-none">Exam Date - <?= $row['edate'] ?></p>
                      <p class="text-md select-none">Session - <?= $row['session'] ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No Data found.</p>
              <?php endif; ?>
            </div>
            <div class="absolute bottom-13 flex gap-6">
              <a href="seating_plan.php?step=3" class="h-10 w-[100px] border-white bg-[#252323] button-secondary rounded-md flex items-center justify-center">Back</a>
              <button id="uniProceedButton" class="h-10 w-[100px] border-white bg-white rounded-md" type="button">Proceed</button>
            </div>
            <div class="flex flex-1 mx-4 gap-2">
              <div class="w-[350px] h-[400px] bg-[#201d1d] border border-2-white p-2 rounded-md relative ">
                <p class="secondary absolute -top-6 left-0">Available Branches</p>
                <div class="w-full h-full overflow-auto available-branches-div flex flex-col gap-3 pr-3">

                </div>
              </div>
              <div class="w-full h-full flex flex-wrap gap-8 ml-2">
                <div class="bg-[#292929] w-[360px] h-[300px] relative border border-2-white rounded-[4px]">
                  <p class="secondary absolute -top-6 left-0 js-shuffle-one shuffle-div">University Shuffle Order</p>
                  <div class="w-full h-full overflow-auto js-shuffle-one-div flex flex-col gap-2 p-3">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </form>
    <?php endif; ?>
  </main>
  <script type="module" defer src="./scripts/app.js"></script>
</body>

</html>