<?php
$conn = new mysqli("localhost", "root", "", "home_service_db");
$customer_id = $_GET['customer_id'];

$result = $conn->query("SELECT status FROM customers WHERE customer_id = $customer_id");
$row = $result->fetch_assoc();
$newStatus = $row['status'] == 'active' ? 'inactive' : 'active';

$conn->query("UPDATE customers SET status = '$newStatus' WHERE customer_id = $customer_id");
header("Location: ManageCustomer.php");
exit();
?>
