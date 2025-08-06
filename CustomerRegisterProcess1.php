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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'];

    // Password strength validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "<script>alert('Password is not strong enough!'); window.location.href='CustomerRegister1.php';</script>";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO customers (name, email, phone, password, address) 
            VALUES ('$name', '$email', '$phone', '$hashedPassword', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration Successful! Please Login.'); window.location.href='CustomerLogin1.php';</script>";
    } else {
        echo "<script>alert('Error during registration. Please try again.'); window.location.href='CustomerRegister1.php';</script>";
    }
}
?>

