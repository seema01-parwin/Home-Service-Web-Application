<?php
$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$service = $_GET['service'];
$stmt = $conn->prepare("SELECT worker_id, name FROM workers WHERE skill = ? AND account_status = 'active'");
$stmt->bind_param("s", $service);
$stmt->execute();
$result = $stmt->get_result();

echo "<option value=''>-- Select Worker --</option>";
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['worker_id']}'>{$row['name']}</option>";
}
