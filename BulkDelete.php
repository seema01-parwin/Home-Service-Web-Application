<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

// Check if POST has 'ids'
if (!isset($_POST['customer_ids']) || !is_array($_POST['customer_ids'])) {
    header("Location: ManageCustomer.php?error=No customers selected");
    exit();
}

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Sanitize and delete
$customer_ids = array_map('intval', $_POST['customer_ids']);
$customer_idList = implode(',', $customer_ids);

$sql = "DELETE FROM customers WHERE customer_id IN ($idList)";
if ($conn->query($sql) === TRUE) {
    header("Location: ManageCustomer.php?success=Selected customers deleted");
} else {
    header("Location: ManageCustomer.php?error=Error deleting customers");
}

$conn->close();
exit();
