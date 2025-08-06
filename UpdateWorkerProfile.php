<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if worker is logged in
if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$skill = $_POST['skill'];

$sql = "UPDATE workers SET fullname=?, email=?, phone=?, address=?, skill=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $fullname, $email, $phone, $address, $skill, $worker_id);

if ($stmt->execute()) {
    header("Location: WorkerProfile.php?success=1");
} else {
    echo "Error updating profile: " . $conn->error;
}
?>

