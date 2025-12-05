<?php 
  include "db_connect.php";
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['user-name']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        die("Both fields are required.");
    }

    $stmt = $conn->prepare("SELECT uid FROM users WHERE username = ? and password = ?");
    $stmt->bind_param("ss", $username,$password);
    $stmt->execute();
    $stmt->bind_result($uid);

    if ($stmt->fetch()) {
        session_start();
        $_SESSION['uid']   = $uid;
        header("Location: overview.php");
        exit();
    } else {
        echo "<script>alert('No such user registered'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>