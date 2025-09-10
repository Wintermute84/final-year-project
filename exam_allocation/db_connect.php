<?php
  $servername = "localhost";
  $username = "root";   // default username in XAMPP
  $password = "mits@123";       // default password is empty
  $dbname = "exam_db";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
