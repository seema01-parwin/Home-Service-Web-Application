<?php 
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin1.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Input Validation & Sanitization
$service_id = isset($_POST['service']) ? intval($_POST['service']) : 0;
$worker_id = isset($_POST['worker']) ? intval($_POST['worker']) : 0;
$date = isset($_POST['date']) ? $_POST['date'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

$service_address = isset($_POST['service_address']) ? trim($_POST['service_address']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
$customer_note = isset($_POST['customer_note']) ? trim($_POST['customer_note']) : '';

if (!$service_id || !$worker_id || !$date || !$time || empty($service_address) || empty($city) || empty($postal_code) || empty($payment_method)) {
    die("<script>alert('All required fields must be filled.'); window.history.back();</script>");
}

// Validate date and time format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
    die("<script>alert('Invalid date or time format.'); window.history.back();</script>");
}

// Combine date and time into datetime
$booking_datetime = date('Y-m-d H:i:s', strtotime("$date $time"));

// Check for existing booking (same customer, service, worker, date, time)
$check_sql = "SELECT * FROM bookings 
              WHERE customer_id = ? AND service_id = ? AND worker_id = ? 
              AND date = ? AND time = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("iiiss", $customer_id, $service_id, $worker_id, $date, $time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('You have already booked this service with the selected worker at the same date and time.'); window.history.back();</script>";
    exit();
}

// Insert booking with new fields
$insert_sql = "INSERT INTO bookings 
    (customer_id, service_id, worker_id, date, time, booking_datetime,  service_address, city, postal_code, customer_note, payment_method, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";

$stmt = $conn->prepare($insert_sql);

if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("iiisssssssss", 
    $customer_id, $service_id, $worker_id, $date, $time, $booking_datetime, 
     $service_address, $city, $postal_code, $customer_note, $payment_method);

if ($stmt->execute()) {
    echo "<script>alert('Service booked successfully!'); window.location.href='customer_dashboard.php';</script>";
} else {
    echo "<script>alert('Booking failed: " . addslashes($stmt->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
