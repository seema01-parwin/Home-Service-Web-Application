<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      background: url('https://th.bing.com/th/id/OIP.0Y8BB2Yycm5Q4XGlv40YfwHaE7?rs=1&pid=ImgDetMain') no-repeat center center fixed;
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
      max-width: 420px;
      margin: 5% auto;
      padding: 30px;
      background: #fbecff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .register-container h1 i {
      color:rgb(93, 34, 132);
      font-size: 40px;
    }

    .register-container h2 {
      margin: 10px 0 30px;
      color: #333;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .input-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .input-group input,
    .input-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: border 0.3s;
    }

    .input-group input:focus,
    .input-group textarea:focus {
      border-color:rgb(112, 28, 145);
    }

    #togglePassword {
      position: absolute;
      right: 15px;
      top: 40px;
      cursor: pointer;
      color: purple;
    }

    small#passwordHelp {
      display: block;
      margin-top: 5px;
      font-size: 13px;
    }

    .btn-register {
      width: 100%;
      padding: 12px;
      background-color:rgb(178, 72, 249);
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-register:hover {
      background-color:rgb(106, 33, 141);
    }

    .login-link {
      margin-top: 20px;
      font-size: 14px;
    }

    .login-link a {
      color:rgb(193, 99, 255);
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .register-container {
        margin: 20px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="register-container">
  <h1><i class="fas fa-user-plus"></i></h1>
  <h2>Customer Registration</h2>
  
  <form action="CustomerRegisterProcess1.php" method="POST">
    <div class="input-group">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>
    </div>

    <div class="input-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required>
    </div>

    <div class="input-group">
      <label for="phone">Phone Number</label>
      <input type="text" id="phone" name="phone" required>
    </div>

    <div class="input-group" style="position: relative;">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required onkeyup="checkPasswordStrength();">
      <span id="togglePassword">
        <i class="fas fa-eye"></i>
      </span>
      <small id="passwordHelp"></small>
    </div>

    <div class="input-group">
      <label for="address">Address</label>
      <textarea id="address" name="address" rows="3" required></textarea>
    </div>

    <button type="submit" class="btn-register">Register</button>

    <p class="login-link">
      Already have an account? <a href="CustomerLogin1.php">Login here</a>
    </p>
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
