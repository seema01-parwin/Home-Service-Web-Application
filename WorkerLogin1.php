<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['worker_id'])) {
    header("Location: WorkerDashboard1.php");
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM workers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $worker = $result->fetch_assoc();
        if ($worker["status"] !== "Approved") {
            $error = "Your account is pending approval.";
        } elseif (password_verify($password, $worker["password"])) {
            $_SESSION["worker_id"] = $worker["worker_id"];
            $_SESSION["worker_name"] = $worker["fullname"];
            header("Location: WorkerDashboard1.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: WorkerLogin1.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Worker Login</title>
    <style>
        * {
    font-family: 'Poppins', sans-serif;
  }
       body {
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  overflow: hidden;
}

/* Background image container */
body::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(rgba(159, 148, 148, 0), rgba(0, 0, 0, 0.03)),
              url('https://static.vecteezy.com/system/resources/previews/022/154/955/non_2x/illustration-of-natural-scenery-background-with-monochromatic-colors-free-vector.jpg') no-repeat center center fixed;
  background-size: cover;
  filter: blur(10px); /* blur intensity */
  z-index: -1; /* stay behind content */
}


        .login-form h1 {
    font-size: 50px;
    text-align: center;
    margin-bottom: 1px;
    margin-top: 1px;
    color: #000000;
  }

        .login-form {
            width: 400px;
            margin: 60px auto;
            background: #fbecff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color:rgb(79, 18, 120);
        }
        .login-form h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-form input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .login-form i {
            color:rgb(79, 18, 120);
        }
        .checkbox-wrapper {
        display: flex;
        margin: 10px 0;
        font-size: 14px;
        color: #333;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
        transform: scale(1.2);
        }

        .login-form button {
            width: 100%;
            background:rgb(168, 41, 185);
            color: white;
            border: none;
            padding: 8px;
            font-size: 16px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .login-form button:hover {
            background: rgb(118, 29, 129); 
        }

        .message {
            text-align: center;
            font-weight: bold;
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-form">
    <h1><i class="fas fa-user-circle"></i></h1>
    <h2>Worker Login</h2>

    <?php if ($error): ?>
        <div class="message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <div style="position: relative;">
  <input type="password" id="password" name="password" placeholder="Password" required>
  <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: purple;"></i>
</div>


    
    <button type="submit">Login</button>
</form>


</div>

<script>
  const togglePassword = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  togglePassword.addEventListener("click", function () {
    // Toggle password field type
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);

    // Toggle icon between eye and eye-slash
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
</script>



</body>
</html>