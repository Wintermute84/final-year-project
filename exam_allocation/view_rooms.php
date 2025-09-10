<?php
include 'db_connect.php'; // your DB connection

$sql = "SELECT * FROM rooms ORDER BY Rid ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms</title>
    <link rel="stylesheet" href="./styles/output.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/output.css">
  </head>

  <body class="bg-black h-screen flex flex-col relative">

    <header class="border-b-2 h-[100px] border-[#FFFFFF] flex relative">
      <div class="flex items-center justify-between w-full">
        <div class="w-[25px] h-[25px] bg-[#9E9B9B] border-3 rounded-sm border-[#FFFEFE] ml-3"></div>  
        <div class="relative group inline-block">
          <div class="cursor-pointer w-[25px] h-[25px] bg-gradient-to-b from-[#18C088] via-[#10855E] via-70% via-[#0D6D4D] to-[#0B5A40] rounded-xl border-2 border-[#828282] mr-3"></div>
          <div class="absolute top-6 right-1 opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-300 ease-in-out h-9 w-[120px] bg-[#373737] border p-3 flex items-center rounded-md shadow-lg">
            <div class="flex items-center justify-center w-full gap-2 hover:bg-[#5C5555] rounded-sm transition-colors duration-200 cursor-pointer select-none">
              <img src="./assets/logout.png" alt="logout img">
              <p class="text-sm">Log out</p>
            </div>
          </div>
        </div>
      </div>
      <div class="flex absolute -bottom-0 w-[70%] justify-evenly text-[18px] ml-5 select-none">
        <img src="./assets/ham_menu.png" alt="hamburger menu" class="hidden">
        <p class="secondary cursor-pointer">Overview</p>
        <p class="secondary cursor-pointer">Seating Plan</p>
        <p class="secondary cursor-pointer">Exams</p>
        <p class="border-b-2 pb-1 cursor-pointer">Rooms</p>
        <p class="secondary cursor-pointer">Students</p>
        <p class="secondary cursor-pointer">Invigilation</p>
        <p class="secondary cursor-pointer">Programmes</p>
      </div>
    </header>
    
    <main class="flex-1 flex">
      <section class="relative flex-1 flex items-center justify-center">
        <div class=" absolute bg-white w-[0.5px] h-[95%] right-0"></div>
        <div class="w-[60%] h-[60%] grid grid-rows-5 grid-flow-col ">
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
          <div class="w-[35px] h-[35px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>  
        </div>
      </section>
      <section class="flex-1 flex items-start justify-center mt-5">
        <div  class="w-[80%] h-[80%]">
          <div x-data="{ open: false }" class="flex items-center justify-between m-2">
            <a href="upload_rooms.php"><button class="h-[50px] w-[200px] bg-[#E5E5E5] rounded-sm cursor-pointer">Upload CSV</button></a>
            <div @click="open = !open"  
                class="flex items-center justify-between relative border h-[35px] w-[200px] px-2 rounded-md bg-[#373737] cursor-pointer">
              <p>M George Block</p>
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
                <p class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md">M George Block</p>
                <p class="hover:bg-[#5C5555] w-full px-2 py-1 rounded-md">Rameshwaram Block</p>                              
              </div>
            </div>
          </div>

          <div class="flex flex-col w-full items-end mt-10 gap-2 overflow-auto h-[400px]">
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="w-[80%] min-h-[80px] max-h-[85px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between">
                <div class="w-fit flex flex-col ml-2">
                  <p class="text-sm">No - <?= $row['Room_no'] ?></p>
                  <p class="text-sm">Capacity - <?= $row['Capacity'] ?></p>
                  <p class="text-sm">Room Type - <?= $row['Type'] ?></p>
                </div>
                <div class="flex gap-2 mr-4">
                  <button class="h-[35px] w-[35px] bg-white flex items-center justify-center border rounded-md">
                    <img class="h-[20px]" src="./assets/delete.png" alt="delete icon">
                  </button>
                  <button class="h-[35px] w-[35px] bg-white flex items-center justify-center  border rounded-md">
                    <img class="h-[20px]" src="./assets/edit.png" alt="edit icon">
                  </button>
                </div>
              </div>    
            <?php endwhile; ?>
            <?php else: ?>
                <p>No rooms found.</p>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>  

    <button class="absolute bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center bottom-8 right-3 cursor-pointer"><img class="h-[25px]" src="assets/add.png" alt="add icon"></button>
    <script src="https://unpkg.com/alpinejs" defer></script>
  </body>
</html>