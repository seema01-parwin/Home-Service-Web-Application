<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=customers.csv');

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Status', 'Registered On']);

$result = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['customer_id'],
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['status'],
        $row['created_at']
    ]);
}
fclose($output);
exit;
?>
