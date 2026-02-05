<!DOCTYPE html>
<html>

<head>
  <title>Exam Allocation</title>
  <link rel="stylesheet" href="./styles/output.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bg-black min-h-screen flex flex-col">

  <header class="border-b-2 h-[100px] border-[#FFFFFF] flex items-center">
    <div class="w-[25px] h-[25px] bg-[#9E9B9B] border-3 rounded-sm border-[#FFFEFE] ml-3">
    </div>
  </header>

  <section class="flex-1 flex flex-col items-center gap-2 justify-evenly lg:flex-row ">
    <div class="text-white max-w-[50%] text-center flex flex-col items-center justify-evenly sm:mt-4 lg:mt-0">
      <h1 class="font-medium text-4xl mb-[54px]">The Foundation for all your seating requirements</h1>
      <h4 class="text-2xl">An all in one solution to fulfil all your seating requirements at without all the hassle</h4>
    </div>
    <div class="sm:w-[60%] min-w-[30%] md:w-[30%] lg:w-[20%]  h-[550px] p-3 bg-[#0A0A0A] border-2 border-[#474444] rounded-3xl">
      <form class="flex flex-col justify-evenly h-[100%]" action="signin.php" method="post">
        <div class="ml-6 mt-5">
          <h2 class="text-3xl">Sign In</h2>
          <h6 class="text-white secondary">Enter your credentials to sign in</h6>
        </div>
        <label class="ml-6 flex flex-col mb-5">
          Username
          <input name="user-name" type="text" class="border-2 border-[#605F5F] px-3 rounded-[15px] h-[50px] w-[80%] bg-[#2E2E2E]" placeholder="john doe">
        </label>
        <label class="ml-6 flex flex-col mb-5">
          Password
          <input type="password" name="password" class="border-2 border-[#605F5F] px-3 rounded-[15px] h-[50px] w-[80%] bg-[#2E2E2E]">
        </label>
        <button type="submit" class="bg-[#E5E5E5] text-lg w-[200px] mx-auto p-3 rounded-[10px]">Sign In</button>
      </form>
    </div>
  </section>

</body>

</html>