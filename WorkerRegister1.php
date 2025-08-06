<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $skill = mysqli_real_escape_string($conn, $_POST['skill']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validation
    if (empty($fullname) || empty($email) || empty($phone) || empty($address) || empty($skill) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Invalid phone number.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }



    // If valid, insert into DB
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $status = "Pending";

        $stmt = $conn->prepare("INSERT INTO workers (fullname, email, phone, address, skill, profile_picture, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $fullname, $email, $phone, $address, $skill, $profile_pic_name, $hashed_password, $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Await admin approval.";
            header("Location: WorkerRegister1.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: WorkerRegister1.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      background: url('https://img.freepik.com/premium-vector/vivid-purple-pink-sky-with-large-rock-cliff_1120563-10215.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      backdrop-filter: blur(10px);
      background-color: rgba(0, 0, 0, 0.4); /* Optional dark overlay */
      z-index: -1;
    }

.register-container {
      max-width: 500px;
      margin: 5% auto;
      padding: 30px;
      background: #fbecff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      text-align: center;
    }
.register-container h1 {
    color: #6a1b9a;
    font-size: 48px;
    margin-bottom: 10px;
}

.register-container h2 {
    font-size: 26px;
    color:rgb(84, 20, 140);
    margin-bottom: 25px;
}

form input, form select {
    width: 95%;
    padding: 12px 15px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s;
}

form input:focus, form select:focus {
    border-color: #6a1b9a;
    outline: none;
}

button[type="submit"] {
    width: 100%;
    background-color: #6a1b9a;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 15px;
}

button[type="submit"]:hover {
    background-color: #4a148c;
}

.error, .success {
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 6px;
    font-size: 14px;
    text-align: left;
}

.error {
    background-color: #ffe6e6;
    color: #c0392b;
    border-left: 5px solid #e74c3c;
}

.success {
    background-color: #e6ffed;
    color: #2e7d32;
    border-left: 5px solid #43a047;
}

@media (max-width: 500px) {
    .register-container {
        padding: 25px 20px;
    }

    .register-container h2 {
        font-size: 22px;
    }
}
</style>

</head>
<body>
    <div class="register-container">
        <h1><i class="fas fa-user-plus"></i></h1>
        <h2>Worker Registration</h2>

        <?php
        if (isset($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $error) {
                echo "<div class='error'>$error</div>";
            }
            unset($_SESSION['errors']);
        }
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>

        <form action="WorkerRegister1.php" method="post" enctype="multipart/form-data">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Address" required>

            <select name="skill" required>
                <option value="">Select Skill/Category</option>
                <option value="Electrician">Electrician</option>
                <option value="Plumber">Plumber</option>
                <option value="Carpenter">Carpenter</option>
                <option value="Painter">Painter</option>
                <option value="Cleaner">Cleaner</option>
                <option value="AC Technician">AC Technician</option>
                <option value="Pest Control Technician">Pest Control Technician</option>
                <option value="Mover">Mover</option>
                <option value="Gardener">Gardener</option>
                <option value="Security Technician">Security Technician</option>
            </select>

        <div style="position: relative;">
  <input type="password" id="password" name="password" placeholder="Password" required>
  <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: purple;"></i>
</div>

<div id="password-strength" style="margin-bottom: 10px;"></div>

<div style="position: relative;">
  <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
  <i class="fas fa-eye" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: purple;"></i>
</div>

<button type="submit">Register</button>

<script>
// Password Strength Checker
document.getElementById("password").addEventListener("input", function () {
  const strengthText = document.getElementById("password-strength");
  const password = this.value;

  let strength = 0;
  if (password.length >= 6) strength++;
  if (/[a-z]/.test(password)) strength++;
  if (/[A-Z]/.test(password)) strength++;
  if (/[0-9]/.test(password)) strength++;
  if (/[^A-Za-z0-9]/.test(password)) strength++;

  let msg = "";
  let color = "";

  if (strength <= 2) {
    msg = "Weak";
    color = "red";
  } else if (strength <= 4) {
    msg = "Moderate";
    color = "orange";
  } else {
    msg = "Strong";
    color = "green";
  }

  strengthText.textContent = `Strength: ${msg}`;
  strengthText.style.color = color;
});

// Toggle Password Visibility for main password
document.getElementById("togglePassword").addEventListener("click", function () {
  const passwordField = document.getElementById("password");
  const type = passwordField.type === "password" ? "text" : "password";
  passwordField.type = type;
  this.classList.toggle("fa-eye");
  this.classList.toggle("fa-eye-slash");
});

// Toggle Password Visibility for confirm password
document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
  const confirmPasswordField = document.getElementById("confirm_password");
  const type = confirmPasswordField.type === "password" ? "text" : "password";
  confirmPasswordField.type = type;
  this.classList.toggle("fa-eye");
  this.classList.toggle("fa-eye-slash");
});
</script>
