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

$worker_id = $_SESSION['worker_id'];

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $targetDir = "Image/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName; // prevent filename collision
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedTypes = array("jpg", "jpeg", "png", "gif");

    if (in_array($fileType, $allowedTypes)) {
        if ($_FILES["profile_picture"]["size"] <= 2 * 1024 * 1024) { // Max 2MB
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                // Update database
                $stmt = $conn->prepare("UPDATE workers SET profile_picture = ? WHERE worker_id = ?");
                $stmt->bind_param("si", $targetFilePath, $worker_id);
                if ($stmt->execute()) {
                    header("Location: WorkerDashboard1.php?upload=success");
                    exit();
                } else {
                    echo "Failed to update profile picture in database.";
                }
            } else {
                echo "Failed to upload file.";
            }
        } else {
            echo "File is too large. Max size is 2MB.";
        }
    } else {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
} else {
    echo "No file selected.";
}
?>
