<?php
include 'config/db_connect.php';
include 'config/functions.php';

if (!isset($_SESSION["uid"])) {
  header("Location: index.php");
}

$selectedFid = isset($_GET['fid']) ? $_GET['fid'] : null;
$selectedFname = isset($_GET['fname']) ? $_GET['fname'] : null;

if (isset($_POST["upload-timetable-csv"])) {
  if (isset($_FILES["timetable-file"]) && $_FILES["timetable-file"]["error"] == 0) {
    $fileTmpPath = $_FILES["timetable-file"]["tmp_name"];
    if (!importFacultyTimeTableCSV($conn, $fileTmpPath)) {
      die("Unable to process faculty timetable");
    }
  } else {
    die("Invalid faculty timetable file !");
  }
}

if (isset($_POST["upload-faculty-list"])) {
  if (isset($_FILES["faculty-list"]) && $_FILES["faculty-list"]["error"] == 0) {
    $fileTmpPath = $_FILES["faculty-list"]["tmp_name"];
    if (!importFacultyDataCSV($conn, $fileTmpPath)) {
      die("Unable to process faculty list");
    }
  } else {
    die("Invalid faculty list file !");
  }
}

if (isset($_POST["upload-timetable-pdf"])) {
  if (isset($_FILES["timetable-pdf"]) && $_FILES["timetable-pdf"]["error"] == 0) {
    $fileTmpPath = $_FILES["timetable-pdf"]["tmp_name"];
    $originalName = $_FILES["timetable-pdf"]["name"];
    if (!importFacultyTimeTablePDF($conn, $fileTmpPath, $originalName)) {
      die("Unable to process timetable");
    }
  } else {
    die("Invalid PDF File !");
  }
}

$faculty = getFaculty($conn);
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

<body x-data="{on: false, display: false, uploadFaculty: false}" class="bg-black h-screen flex flex-col relative">

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
          class="absolute top-8 right-1 z-50 h-fit  flex-col gap-2 w-[120px] bg-[#373737] border p-3 flex items-center rounded-md shadow-lg">
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
      <p class="secondary cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
      <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='faculty.php'">Faculty</p>
      <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
    </div>
  </header>
  <main class="flex-1 flex">
    <section class="relative flex-1 flex items-center justify-center">
      <div class=" absolute bg-white w-[0.5px] h-[95%] right-0"></div>
      <div class="w-[99%] h-[99%]">
        <div class="w-[55vw] h-[70vh] overflow-auto mt-[20px]">
          <?php
          if (isset($_GET['fid']) && isset($_GET['fname']) && isset($_GET['day'])) {
            $timeTableData = getFacultyTimeTable($conn, $selectedFid, $_GET['day']);
          } ?>
          <?php if (isset($timeTableData) and $timeTableData->num_rows > 0): ?>
            <table border="1" class="m-auto w-[95%]">
              <tr>
                <th>Hour Start</th>
                <th>Hour End</th>
                <th>Branch</th>
                <th>Semester</th>
              </tr>
              <?php while ($row = $timeTableData->fetch_assoc()): ?>
                <tr>
                  <td><?= $row["start_time"] ?></td>
                  <td><?= $row["end_time"] ?></td>
                  <td><?= $row["branch"] ?></td>
                  <td><?= $row["sem"] > 0 ? $row["sem"] : "" ?></td>
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
        <div x-data="{ open: false }" class="flex flex-col items-center justify-between m-2 gap-3">
          <div class="w-full flex gap-2">
            <button @click="on = true" class="h-[50px] w-[200px] bg-[#E5E5E5] rounded-sm cursor-pointer">Upload Timetable</button>
            <button @click="uploadFaculty = true" class="h-[50px] w-[200px] bg-[#E5E5E5] rounded-sm cursor-pointer">Upload Faculty</button>
            <button @click="display = true" class="h-[50px] w-[200px] bg-[#E5E5E5] rounded-sm cursor-pointer">Convert CSV</button>
          </div>
          <div class="w-full flex justify-end">
            <div @click="open = !open"
              class="flex items-center justify-between relative border h-[35px] w-[250px] px-2 rounded-md bg-[#373737] cursor-pointer">
              <p><?= $selectedFname ? $selectedFname : "Select Faculty" ?></p>
              <img class="h-[16px]" src="assets/dropdown.png" alt="dropdown icon">
              <div x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-[#373737] absolute top-full mt-3 z-40 -left-1 border w-[250px] h-[200px] overflow-auto flex flex-col items-start p-4 rounded-md gap-1">
                <?php if ($faculty->num_rows > 0): ?>
                  <?php while ($row = $faculty->fetch_assoc()): ?>
                    <p @click="window.location.href='faculty.php?fid=<?= $row['fid'] ?>&fname=<?= $row['faculty'] ?>'" class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md"><?= $row['faculty'] ?></p>
                  <?php endwhile; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>
        <div class="flex flex-col w-full items-end mt-10 gap-2 overflow-auto h-[50vh]">
          <?php if (isset($selectedFid)): ?>
            <?php
            $result = getFacultyTimetableDays($conn, $selectedFid);
            $rowCount = $result->num_rows;
            if ($result->num_rows > 0):
              while ($row = $result->fetch_assoc()): ?>
                <a href="faculty.php?fid=<?= $selectedFid ?>&day=<?= $row['day'] ?>&fname=<?= $selectedFname ?>" class=" <?= (isset($_GET['day']) && ($_GET['day'] == $row['day'])) ? "active" : "" ?> w-[80%] min-h-[80px] max-h-[85px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out">
                  <div class="w-fit flex flex-col ml-2">
                    <p class="text-md">Faculty - <?= $selectedFname ?></p>
                    <p class="text-md">Day - <?= $row['day'] ?></p>
                  </div>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No data found.</p>
            <?php endif; ?>
          <?php else: ?>
            <p>No data found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>
  <div class="absolute inset-0 flex items-center justify-center bg-black z-40 opacity-96"
    x-show="on">
    <div class="w-[500px] h-[250px] bg-black z-50 border-white border-2  rounded-[3px]">
      <div class="relative flex w-full h-[50px] items-center justify-center select-none">
        <p>Upload Faculty Timetable(.csv)</p>
        <img @click="on = false" src="./assets/close.png" alt="close icon" class="absolute right-0 mr-4 h-[20px] cursor-pointer">
      </div>
      <form method="post" enctype="multipart/form-data" class="flex justify-center gap-3 mt-16">
        <label class="bg-white p-2 border rounded-[3px] w-[112px] h-fit cursor-pointer" id="file-label" for="file">Choose File</label>
        <input type="file" id="file" name="timetable-file" accept=".csv" required>
        <button type="submit" name="upload-timetable-csv" class="upload-button bg-white border rounded-[3px] w-[112px] h-[41px]">Upload</button>
      </form>
    </div>
  </div>
  <div class="absolute inset-0 flex items-center justify-center bg-black z-40 opacity-96"
    x-show="uploadFaculty">
    <div class="w-[500px] h-[250px] bg-black z-50 border-white border-2  rounded-[3px]">
      <div class="relative flex w-full h-[50px] items-center justify-center select-none">
        <p>Upload Faculty List(.csv)</p>
        <img @click="uploadFaculty = false" src="./assets/close.png" alt="close icon" class="absolute right-0 mr-4 h-[20px] cursor-pointer">
      </div>
      <form method="post" enctype="multipart/form-data" class="flex justify-center gap-3 mt-16">
        <label class="bg-white p-2 border rounded-[3px] w-[112px] h-fit cursor-pointer" id="file-label" for="filess">Choose File</label>
        <input type="file" id="filess" class="file" name="faculty-list" accept=".csv" required>
        <button type="submit" name="upload-faculty-list" class="upload-button bg-white border rounded-[3px] w-[112px] h-[41px]">Upload</button>
      </form>
    </div>
  </div>
  <div class="absolute inset-0 flex items-center justify-center bg-black z-40 opacity-96"
    x-show="display">
    <div class="w-[500px] h-[250px] bg-black z-50 border-white border-2  rounded-[3px]">
      <div class="relative flex w-full h-[50px] items-center justify-center select-none">
        <p>Upload Timetable to be Converted(.pdf)</p>
        <img @click="display = false" src="./assets/close.png" alt="close icon" class="absolute right-0 mr-4 h-[20px] cursor-pointer">
      </div>
      <form method="post" enctype="multipart/form-data" class="flex justify-center gap-3 mt-16">
        <label class="bg-white p-2 border rounded-[3px] w-[112px] h-fit cursor-pointer" id="file-label" for="files">Choose File</label>
        <input type="file" id="files" name="timetable-pdf" accept=".pdf" required>
        <button type="submit" name="upload-timetable-pdf" class="upload-button bg-white border rounded-[3px] w-[112px] h-[41px]">Upload</button>
      </form>
      <?php if (isset($_GET['import']) && $_GET['import'] === 'success'): ?>
        <p class="secondary text-center mt-8">CSV data imported successfully!</p>
      <?php endif; ?>
    </div>
  </div>
  <div class="absolute bottom-8 right-3 flex gap-2">
    <button @click="on = true" class="bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center  cursor-pointer"><img class="h-[25px]" src="assets/add.png" alt="add icon"></button>
    <button class="bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center cursor-pointer" onclick="return confirm('Delete entire faculty timetable data?');"><img class="h-[25px]" src="assets/delete.png" alt="delete icon"></button>
  </div>

  <script type="module" src="./scripts/app.js"></script>
</body>

</html>