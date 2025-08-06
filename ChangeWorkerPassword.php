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

if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Check if new passwords match
if ($new_password !== $confirm_password) {
    die("New passwords do not match.");
}

// Get current hashed password
$query = "SELECT password FROM workers WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

// Verify old password
if (!password_verify($old_password, $hashed_password)) {
    die("Old password is incorrect.");
}

// Hash new password
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$update = $conn->prepare("UPDATE workers SET password=? WHERE id=?");
$update->bind_param("si", $new_hashed, $worker_id);
if ($update->execute()) {
    header("Location: WorkerProfile.php?password_changed=1");
} else {
    echo "Error updating password: " . $conn->error;
}
?>
