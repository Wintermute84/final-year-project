<?php
  include 'db_connect.php';
  include 'functions.php';
  if(!isset($_SESSION["uid"])){
    header("Location: index.php");
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
          <div @click="open = !open"  class="cursor-pointer w-[25px] h-[25px] bg-gradient-to-b from-[#18C088] via-[#10855E] via-70% via-[#0D6D4D] to-[#0B5A40] rounded-xl border-2 border-[#828282] mr-3"></div>
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
        <p class="border-b-2 pb-1 cursor-pointer" onclick="window.location.href='seating_plan.php'">Seating Plan</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='exams.php'">Exams</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='view_rooms.php'">Rooms</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='students.php'">Students</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='invigilation.php'">Invigilation</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='programmes.php'">Programmes</p>
        <p class="secondary cursor-pointer" onclick="window.location.href='courses.php'">Courses</p>
      </div>
    </header>
    <main  class="flex-1 flex">
    </main>  
    <button class="absolute bg-white w-[50px] h-[50px] rounded-full flex items-center justify-center bottom-8 right-3 cursor-pointer"><img class="h-[25px]" src="assets/add.png" alt="add icon"></button>
    <script type="module" src="./scripts/app.js"></script>
  </body>
</html>