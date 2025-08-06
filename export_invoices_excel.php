<?php
session_start();
$conn = new mysqli("localhost", "root", "", "home_service_db");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=invoices_" . date('Y-m-d') . ".xls");

$customer_id = intval($_SESSION['customer_id']);

$where = "WHERE p.customer_id = ?";
$params = [$customer_id];
$types = "i";

if (!empty($_POST['from']) && !empty($_POST['to'])) {
    $where .= " AND DATE(p.payment_date) BETWEEN ? AND ?";
    $params[] = $_POST['from'];
    $params[] = $_POST['to'];
    $types .= "ss";
}

if (!empty($_POST['status'])) {
    $where .= " AND p.payment_status = ?";
    $params[] = $_POST['status'];
    $types .= "s";
}

$sql = "SELECT p.*, b.booking_datetime, b.booking_status, w.fullname AS worker_name
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN workers w ON p.worker_id = w.worker_id
        $where
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr>
        <th>Invoice ID</th>
        <th>Payment Date</th>
        <th>Worker</th>
        <th>Booking Date</th>
        <th>Booking Status</th>
        <th>Payment Method</th>
        <th>Payment Status</th>
        <th>Amount</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['payment_id']}</td>
            <td>{$row['payment_date']}</td>
            <td>{$row['worker_name']}</td>
            <td>{$row['booking_datetime']}</td>
            <td>{$row['booking_status']}</td>
            <td>{$row['payment_method']}</td>
            <td>{$row['payment_status']}</td>
            <td>Rs. " . number_format($row['amount'], 2) . "</td>
          </tr>";
}
echo "</table>";
?>
