<?php
include 'config/db_connect.php';
include 'config/functions.php';
if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}
$etype = isset($_GET['etype']) ? $_GET['etype'] : 'All';
$result = getExams($conn, $etype);
if (isset($_POST['exam-upload'])) {
  if (isset($_FILES['time-table-upload-file'])) {
    $filename = $_FILES['time-table-upload-file']['tmp_name'];
    $a = addExamDefinition($conn, $filename);
  }
  header("Location: exams.php");
  exit;
}

if (isset($_GET['delete_id'])) {
  deleteExam($conn, $_GET['delete_id']);
  header("Location: exams.php");
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

  <header class="border-b-2 h-[100px] border-[#FFFFFF] flex relative">
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
    <div class="flex absolute -bottom-0 w-[70%] justify-evenly text-[18px] ml-5 select-none">
      <img src="./assets/ham_menu.png" alt="hamburger menu" class="hidden">
      <p class="secondary cursor-pointer" onclick="window.location.href='overview.php'">Overview</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='seating_plan.php?step=1'">Seating Plan</p>
      <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='exams.php'">Exams</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='view_rooms.php'">Rooms</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='students.php'">Students</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='programmes.php'">Programmes</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex">
    <section class="relative flex-1 flex items-center justify-center">
      <div class=" absolute bg-white w-[0.5px] h-[95%] right-0"></div>
      <div class="w-[99%] h-[99%]">
        <?php if (isset($_GET['eid'])): ?>
          <h3 class="m-2 text-md"><?= $_GET['ename'] ?></h3>
        <?php endif; ?>
        <div class="w-[840px] h-[600px] overflow-auto mt-[20px]">
          <?php
          if (isset($_GET['eid']) && isset($_GET['ename'])) {
            $eid = $_GET['eid'];
            $timeTableData = getExamTimeTableData($conn, $eid);
          } ?>
          <?php if (isset($timeTableData) and $timeTableData->num_rows > 0): ?>
            <table border="1" class="m-auto w-[99%]">
              <tr>
                <th>Exam Date</th>
                <th>Session</th>
                <th>Course Code</th>
                <th>Semester</th>
                <th>Branch</th>
              </tr>
              <?php while ($row = $timeTableData->fetch_assoc()): ?>
                <tr>
                  <td><?= $row["edate"] ?></td>
                  <td><?= $row["session"] ?></td>
                  <td><?= $row["ccode"] ?></td>
                  <td><?= $row["sem"] ?></td>
                  <td><?= $row["branch"] ?></td>
                </tr>
              <?php endwhile; ?>
            </table>
          <?php endif; ?>
        </div>
      </div>
      </div>
    </section>
    <section class="flex-1 flex items-start justify-center mt-5">
      <div class="w-[80%] h-[80%]">
        <div x-data="{ open: false }" class="flex items-center justify-between m-2">
          <button @click="on = true" class="h-[50px] w-[200px] bg-[#E5E5E5] rounded-sm cursor-pointer">Upload CSV</button>
          <div @click="open = !open"
            class="flex items-center justify-between relative border h-[35px] w-[200px] px-2 rounded-md bg-[#373737] cursor-pointer">
            <p><?= $etype ? $etype : "All" ?></p>
            <img class="h-[16px]" src="assets/dropdown.png" alt="dropdown icon">
            <div x-show="open"
              @click.outside="open = false"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95"
              class="bg-[#373737] absolute top-full mt-2 z-40 -left-10 border w-[238px] h-fit flex flex-col items-start p-3 rounded-md gap-1">
              <p @click="window.location.href='exams.php?etype=All'" class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md">All</p>
              <p @click="window.location.href='exams.php?etype=Internal%20Exam'" class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md">Internal Exam</p>
              <p @click="window.location.href='exams.php?etype=University%20Exam'" class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md">University Exam</p>
            </div>
          </div>
        </div>

        <div class="flex flex-col w-full items-end mt-10 gap-2 overflow-auto h-[400px]">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="py-4 w-[80%] min-h-[110px] max-h-[120px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-room-div">
                <a href="exams.php?eid=<?= $row['eid'] ?>&ename=<?= $row['ename'] ?>" class="w-fit flex flex-col ml-2">
                  <p class="text-md overflow-ellipsis text-nowrap">Exam Name - <?= $row['ename'] ?></p>
                  <p class="text-md">Exam Type - <?= $row['etype'] == "1" ? "Internal Exam" : "University Exam" ?></p>
                  <p class="text-md">Start Date - <?= $row['sdate'] ?></p>
                  <p class="text-md">End Date - <?= $row['edate'] ?></p>
                </a>
                <div class="flex gap-2 mr-4">
                  <a href="exams.php?delete_id=<?= $row['eid'] ?>"
                    onclick="return confirm('Delete this Exam and related data?');"
                    class="h-[35px] w-[35px] bg-white flex items-center justify-center border rounded-md">
                    <img class="h-[20px]" src="./assets/delete.png" alt="delete icon">
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No Data found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>
  <div class="absolute inset-0 flex items-center justify-center bg-black z-40 opacity-96" x-show="on">
    <div class="w-[600px] h-[700px] bg-[#131313] z-50 border-[#D9D9D9] border-2  rounded-[3px] relative">
      <img @click="on = false" src="./assets/close.png" alt="close icon" class="absolute top-3 right-0 mr-4 h-[20px] cursor-pointer">
      <div class="relative flex w-full h-[50px] items-center justify-center select-none mt-6">
        <p class="text-2xl">Enter Exam Details</p>
      </div>
      <form method="post" enctype="multipart/form-data" class="flex flex-col justify-center gap-4 mt-8">
        <label for="examNameInput" class="flex flex-col ml-16">
          Exam Name
          <input type="text" id="examNameInput" name="examNameInput" class="border-2 px-3 border-[#605F5F] bg-[#323232] h-10 rounded-[8px] w-[400px]">
        </label>
        <label for="examTypeInput" class="flex flex-col ml-16">
          Exam Type
          <select id="examTypeInput" class="px-3 border-2 border-[#605F5F] bg-[#323232] h-10 rounded-[8px] w-[400px]" name="examTypeInput">
            <option value="1">Internal</option>
            <option value="2">University</option>
          </select>
        </label>
        <label for="startDateInput" class="flex flex-col ml-16">
          Start Date
          <input type="date" id="startDateInput" name="startDateInput" class="px-3 border-2 border-[#605F5F] bg-[#323232] h-10 rounded-[8px] w-[400px]">
        </label>
        <label for="endDateInput" class="flex flex-col ml-16">
          End Date
          <input type="date" name="endDateInput" id="endDateInput" class="px-3 border-2 border-[#605F5F] bg-[#323232] h-10 rounded-[8px] w-[400px]">
        </label>
        <div class="flex flex-col ml-16 gap-2">
          <p>Exam Time Table Upload</p>
          <div class="flex gap-2 items-center justify-start">
            <label class="bg-white p-2 border rounded-[3px] w-[112px] h-fit cursor-pointer" id="file-label" for="file">Choose File</label>
            <input type="file" id="file" name="time-table-upload-file" accept=".csv" required>
          </div>
        </div>
        <button type="submit" name="exam-upload" class="upload-button bg-white border rounded-[3px] w-[112px] h-[50px] mx-auto mt-12">Submit</button>
      </form>
    </div>
  </div>
  <button @click="on=true" class="bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center cursor-pointer absolute bottom-8 right-3"><img class="h-[25px]" src="assets/add.png" alt="add icon"></button>
  <script type="module" src="./scripts/app.js"></script>
</body>

</html>