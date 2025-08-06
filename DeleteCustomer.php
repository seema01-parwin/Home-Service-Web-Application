<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "home_service_db";
$conn = new mysqli($host, $user, $pass, $db);

// Validate ID
if (!isset($_GET['customer_id']) || !is_numeric($_GET['customer_id'])) {
    header("Location: ManageCustomer.php?error=invalid_customer_id");
    exit();
}

$customer_id = intval($_GET['customer_id']);

// Check if customer exists
$stmt = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: ManageCustomer.php?error=not_found");
    exit();
}
$stmt->close();

// Delete the customer
$stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
if ($stmt->execute()) {
    header("Location: ManageCustomer.php?success=deleted");
} else {
    header("Location: ManageCustomer.php?error=delete_failed");
}
$stmt->close();
?>
