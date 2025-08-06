<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Login | Home Service Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
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
              url('https://img.freepik.com/premium-vector/australia-landscape-silhouette-rocks-plants-purple-vector-landscape_624728-1362.jpg') no-repeat center center fixed;
  background-size: cover;
  filter: blur(10px); /* blur intensity */
  z-index: -1; /* stay behind content */
}


    .login-container {
      background: #fbecff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h1 {
      font-size: 50px;
      color:rgb(70, 18, 105);
      margin-bottom: 10px;
    }

    .login-container h2 {
      font-size: 24px;
      margin-bottom: 30px;
      color: #333;
    }

    .input-group {
      text-align: left;
      margin-bottom: 20px;
      position: relative;
    }

    .input-group label {
      display: block;
      margin-bottom: 5px;
      color: #444;
    }

    .input-group input {
      width: 100%;
      padding: 10px 12px;
      padding-right: 40px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      transition: 0.3s;
    }

    .input-group input:focus {
      border-color:rgb(69, 29, 96);
      box-shadow: 0 0 5px rgba(198, 99, 255, 0.5);
    }

    #togglePassword {
      position: absolute;
      top: 36px;
      right: 15px;
      cursor: pointer;
      color:rgb(81, 31, 110);
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background:rgb(106, 38, 155);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-login:hover {
      background:rgb(68, 17, 81);
    }

    .register-link {
      margin-top: 20px;
      color: #333;
    }

    .register-link a {
      color:rgb(179, 81, 221);
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .login-container {
        padding: 20px;
        margin: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h1><i class="fas fa-user-circle"></i></h1>
    <h2>Customer Login</h2>
    <form action="CustomerLoginProcess1.php" method="POST">
      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <span id="togglePassword">
          <i class="fas fa-eye"></i>
        </span>
        <small id="passwordHelp"></small>
      </div>

      <button type="submit" class="btn-login">Login</button>

      <p class="register-link">
        Don't have an account? <a href="CustomerRegister1.php">Register here</a>
      </p>
    </form>
  </div>

  <!-- JavaScript for show/hide password -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      this.querySelector('i').classList.toggle('fa-eye');
      this.querySelector('i').classList.toggle('fa-eye-slash');
    });
  </script>

</body>
</html>
