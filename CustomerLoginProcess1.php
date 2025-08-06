<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if user exists using customer_id
    $sql = "SELECT * FROM customers WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        
        $row = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $row['customer_id'];  // changed from 'id' to 'customer_id'
            $_SESSION['customer_name'] = $row['name'];
            header("Location: CustomerDashboard1.php"); // Redirect after login
            exit();
        } else {
            echo "<script>alert('Invalid Email or Password!'); window.location.href='CustomerLogin1.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Email or Password!'); window.location.href='CustomerLogin1.php';</script>";
    }
}
?>
