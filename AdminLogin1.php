<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: AdminDashboard1.php");
    exit();
}

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

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ✅ Change 'id' to 'admin_id' here
    $stmt = $conn->prepare("SELECT admin_id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_id'] = $admin_id;
            header("Location: AdminDashboard1.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Invalid credentials.";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Background and container setup */
body {
    margin: 0;
    padding: 0;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://www.hdwallpapers.in/download/purple_scene_landscape_minimal_4k-HD.jpg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Login container */
.login-container {
    background: #fbecff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
}

/* Heading */
.login-container h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    color: #333;
}

/* Input groups */
.input-group {
    margin-bottom: 20px;
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #444;
}

.input-group input {
    width: 96%;
    padding: 10px 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
    outline: none;
    transition: border 0.3s ease;
}

.input-group input:focus {
    border-color:rgb(81, 21, 109);
}

/* Toggle eye icon */
#togglePassword {
    position: absolute;
    top: 36px;
    right: 14px;
    cursor: pointer;
    font-size: 16px;
}

/* Password help */
#passwordHelp {
    font-size: 12px;
    margin-top: 6px;
    display: block;
}

/* Error message */
.error {
    background-color: #ffdddd;
    border-left: 5px solid #f44336;
    padding: 10px 15px;
    margin-bottom: 20px;
    color: #a94442;
    border-radius: 4px;
}

/* Submit button */
button[type="submit"] {
    width: 100%;
    background-color:rgb(99, 37, 139);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #5b21b6;
}

/* Responsive design */
@media (max-width: 480px) {
    .login-container {
        padding: 25px 20px;
    }

    .login-container h2 {
        font-size: 24px;
    }

    #togglePassword {
        top: 34px;
        right: 12px;
    }
}

    </style>
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="AdminLogin1.php">
        <div class="input-group">
      <label for="name">Username</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group" style="position: relative;">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" required;">
      <span id="togglePassword" style="position: absolute; right: 15px; top: 40px; cursor: pointer; color: purple">
        <i class="fas fa-eye"></i>
      </span>
      <small id="passwordHelp"></small>
    </div>
        <button type="submit">Login</button>
    </form>
</div>

<!-- JavaScript Section -->
<script>
// Password Strength Checker
function checkPasswordStrength() {
    var password = document.getElementById("password").value;
    var helpText = document.getElementById("passwordHelp");

    var strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (strongPassword.test(password)) {
        helpText.style.color = "green";
        helpText.innerHTML = "Strong Password!";
    } else {
        helpText.style.color = "red";
        helpText.innerHTML = "Password must be 8+ characters, with uppercase, lowercase, number, and special character.";
    }
}

// Password Visibility Toggle
const togglePassword = document.querySelector('#togglePassword');
const passwordField = document.querySelector('#password');

togglePassword.addEventListener('click', function () {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
});
</script>

</body>
</html>