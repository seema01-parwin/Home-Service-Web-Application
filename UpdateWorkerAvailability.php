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
$availability = $_POST['availability'];       // e.g., "Mon - Sat"
$working_hours = $_POST['working_hours'];     // e.g., "9:00 a.m - 5:00 p.m"

// Update only if worker is approved
$sql = "UPDATE workers SET availability = ?, working_hours = ? WHERE worker_id = ? AND status = 'Approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $availability, $working_hours, $worker_id);

if ($stmt->execute()) {
    header("Location: WorkerProfile.php?availability_updated=1");
    exit();
} else {
    echo "Error updating availability: " . $conn->error;
}
?>
