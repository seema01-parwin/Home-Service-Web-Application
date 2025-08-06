<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get bookings per month for the last 6 months
$sql = "
    SELECT DATE_FORMAT(booking_date, '%Y-%m') AS month, COUNT(*) AS count
    FROM bookings
    WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month ASC
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
<body>
    <script src="<script src="AdScript1.js"></script>
</body>
