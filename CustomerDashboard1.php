<?php
// Start session and check if customer is logged in
session_start();
if(!isset($_SESSION['customer_id'])){
    header('Location: CustomerLogin1.php');
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

// Prevent Browser from Caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If customer not logged in, redirect to login page
if (!isset($_SESSION['customer_id'])) {
    header('Location: CustomerLogin1.php');
    exit();
}

// Fetch customer details from database
$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
$result = mysqli_query($conn, $query);
$customer = mysqli_fetch_assoc($result);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard - Home Service Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background:rgb(255, 255, 255);
}

header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    background: #630675;    
    color: white;
    padding: 20px 20px;
  }
  
  .logo {
    display: flex;
    align-items: center;
  }
  
  .logo img {
    width: 40px;
    margin-right: 10px;
  }
  
  .logo h1 {
    font-size: 1.5em;
  }
  
  /* Search Bar */
  .search-bar {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
  }
  
  .search-bar input {
    padding: 6px 10px;
    border-radius: 4px;
    border: none;
    outline: none;
    width: 500px;
  }
  
  .search-bar button {
    padding: 6px 10px;
    background: #c953e0;
    border: none;
    color: white;
    border-radius: 4px;
    cursor: pointer;
  }
  
  /* Navigation */
  nav ul {
    display: flex;
    gap: 20px;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 2em;
  }
  
  nav ul li a:hover {
    color: #5a0c68;
  }

/* Dashboard Container */
.dashboard-container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 20px;
  background: linear-gradient(to top, #f7a3ff, #ffe3ff);
  border-radius: 8px;
}



.profile-section-a {
  background: white;
  border-radius: 10px;
  padding: 10px;
  margin-bottom: 25px;
  text-align: center;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Profile Section */
.profile-section {
  text-align: center;
  margin-bottom: 30px;
}

.profile-pic img {
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
  transition: transform 0.3s ease;
}

.profile-pic img:hover {
  transform: scale(1.1);
}

.profile-pic form {
  margin-top: 10px;
}

.profile-pic input[type="file"] {
  margin-bottom: 10px;
}

.profile-pic button {
  padding: 8px 20px;
  background-color: #c800da;
  color: white;
  border-color: #240046;
  cursor: pointer;
}

.profile-pic button:hover {
  background-color: #9500b3;
}

.profile-details p {
  font-size: 18px;
  margin: 5px 0;
}

/* Dashboard Cards */
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
  gap: 20px;
  margin-top: 30px;
}


.card {
  background: white;
  padding-bottom: 10px;
  padding-top: 20px;
  text-align: center;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);

}

.card a {
  text-decoration: none;
  color: #8f009c;
  font-size: 15px;
  font-weight: bold;
}

.card i {
  color: #8f009c;
  font-size: 2.5em;
}

/* Logout Button */
.logout-section {
  text-align: center;
  margin-top: 40px;
}

.logout-button {
  display: inline-block;
  padding: 10px 25px;
  background: #bd39e6;
  color: white;
  text-decoration: none;
  border-radius: 5px;
}

.logout-button:hover {
  background-color: #6c077a
}

/* Footer */
footer {
  background-color: #630675;
  color: white;
  text-align: center;
  padding: 25px 0;
  margin-top: 40px;
}

footer .social-icons {
  margin-bottom: 10px;
}

footer .social-icons a {
  color: white;
  margin: 0 10px;
  font-size: 20px;
  transition: color 0.2s;
}

footer .social-icons a:hover {
  color: #fed6ff;
}

.footer-bottom {
  font-size: 14px;
  color: #ccc;
}
  /* Responsive Design for Smaller Screens */
  @media (max-width: 768px) {
    header {
      flex-direction: column;
      align-items: center;
      padding: 10px;
    }
  
    nav a {
      margin: 5px 0;
    }
   
    .nav-text {
      display: none;
    }
  
    nav ul {
      gap: 15px;
      padding: 10px 20px;
    }
  
    .search-bar {
      width: 100%;
      justify-content: center;
      margin: 10px 0;
    }
  
    .search-bar input {
      width: 70%;
    }
  }



    </style>
</head>
<body>

    <!-- Header Section -->
    <header> 
  <div class="logo">
    <img src="Image/Logo_Home.png" alt="Logo">
    <h1>SPI Home Services</h1>
  </div>

  <div class="search-bar">
  <form action="search1.php" method="GET">
    <input type="text" name="query" placeholder="Search services..." required>
    <button type="submit"><i class="fas fa-search"></i></button>
  </form>
</div>

  <nav>
    <ul>
      <li><a href="Home.php"><i class="fas fa-home"></i> <span class="nav-text"></span></a></li>
    </ul>
  </nav>
</header>

    <!-- Dashboard Main Section -->
    <div class="dashboard-container">

        <!-- Profile Section -->
        <section id="profile" class="profile-section-a">
        <div class="profile-section">
            <h2>Welcome, <?php echo htmlspecialchars($customer['name']); ?>!</h2>

            <div class="profile-pic">
            <?php
            $profile_picture = !empty($customer['profile_picture']) ? $customer['profile_picture'] : 'c:\xampp\htdocs\HomeServiceManagementSystem\Image\Icons_default.png';
            ?>
                <img src="Image/<?php echo htmlspecialchars($customer['profile_picture']); ?>" alt="" width="150" height="150">
                <form action="UploadProfilePicture1.php" method="POST" enctype="multipart/form-data">
                    
                </form>
            </div>

            <div class="profile-details">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                <!-- Add more fields if needed -->
            </div>
        </div>
        </section>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <a href="CustomerProfile1.php">
                <i class="fas fa-user"></i>
                    <h3>My Profile</h3>
                </a>
            </div>
            <div class="card">
                <a href="CustomerBookings.php">
                <i class="fas fa-calendar-check"></i>
                    <h3>My Bookings</h3>
                </a>
            </div>
            <div class="card">
                <a href="BookServices.php">
                <i class="fas fa-tools"></i>
                    <h3>Book Services</h3>
                </a>
            </div>
            <div class="card">
                <a href="CustomerReview.php">
                <i class="fas fa-star"></i>
                    <h3>Rating & Review</h3>
                </a>
            </div>
            <div class="card">
                <a href="CustomerNotification.php">
                <i class="fas fa-envelope"></i>
                    <h3>Notifications</h3>
                </a>
            </div>
            <div class="card">
                <a href="CustomerInvoice.php">
                <i class="fas fa-file-invoice-dollar"></i>
                    <h3>Invoice</h3>
                </a>
            </div>
            <div class="card">
                <a href="CustomerHelpFAQ.php">
                  <i class="fas fa-question-circle"></i>
                  <h3>Help & FAQs</h3>
                </a>
            </div>
        </div>

        <!-- Log Out Button -->
        <div class="logout-section">
            <a href="CustomerLogout1.php" class="logout-button">Log Out</a>
        </div>

    </div>

    <!-- Footer Section -->
    <footer>
  <div class="footer">
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
  </div>
  <p class="footer-bottom">&copy; 2025 Home Service Management System. All rights reserved.</p>
</footer>
<script src="Dashboard1.js"></script>

</body>
</html>