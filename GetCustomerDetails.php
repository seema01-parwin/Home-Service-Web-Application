<?php
// Start session and check admin login
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Check if 'id' parameter exists
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid ID"]);
    exit();
}

$customer_id = (int) $_GET['id'];

// Prepare and execute query
$stmt = $conn->prepare("SELECT name, email, phone, status, created_at, address, profile_picture FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $customer = $result->fetch_assoc();
    echo json_encode($customer);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Customer not found"]);
}

$stmt->close();
$conn->close();
?>
