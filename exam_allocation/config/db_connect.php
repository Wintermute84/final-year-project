<?php
session_start();
$servername = "localhost";
$username = "root";   // default username in XAMPP
$password = "";       // default password is empty
$dbname = "exam";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
