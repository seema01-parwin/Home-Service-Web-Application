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

// Check if worker is logged in
if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

// Fetch worker data
$sql = "SELECT * FROM workers WHERE worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

// Fetch average rating
$ratingQuery = "SELECT ROUND(AVG(rating),1) as avg_rating FROM reviews WHERE worker_id = $worker_id";
$ratingResult = mysqli_query($conn, $ratingQuery);
$ratingData = mysqli_fetch_assoc($ratingResult);
$avg_rating = $ratingData['avg_rating'] ?? 'No ratings';

// Fetch notifications

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="WorkerDashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="Image/Logo_Home.png" alt="Logo">
            <h1>SPI Home Services</h1>
        </div>
        <nav>
            <ul>
                <li><a href="WorkerLogout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">

      <section id="profile" class="profile-section">
        <h2>Welcome, <?php echo htmlspecialchars($worker['fullname']); ?>!</h2>
        <div class="profile-pic">
        <img src="<?php echo htmlspecialchars($worker['profile_picture'] ?? 'Image/Icons_default.png'); ?>" width="150" height="150">

          
        </div><br>
          <p><strong>Email:</strong> <?php echo $worker['email']; ?></p>
          <p><strong>Phone:</strong> <?php echo $worker['phone']; ?></p>
          <p><strong>Average Rating:</strong> <?php echo $avg_rating; ?> ⭐</p>
          <p><strong>Availability:</strong> <?php echo htmlspecialchars($worker['availability']); ?></p>
          <p><strong>Working Hours:</strong> <?php echo htmlspecialchars($worker['working_hours']); ?></p>

      </section>

        <div class="dashboard-cards">
            <div class="card"><a href="WorkerProfile.php"><i class="fas fa-user"></i>  My Profile</a></div>
            <div class="card"><a href="WorkerCurrentBookings.php"><i class="fas fa-calendar-check"></i>  Current Bookings</a></div>
            <div class="card"><a href="WorkerBookings.php"><i class="fas fa-tools"></i>  Manage Bookings</a></div>
            <div class="card"><a href="WorkProofs.php"><i class="fas fa-image"></i>  Work Proof</a></div>
            <div class="card"><a href="WorkerReviews.php"><i class="fas fa-star"></i>  Ratings & Reviews</a></div>
            <div class="card"><a href="WorkerEarnings.php"><i class="fas fa-wallet"></i>  Earnings & Payments</a></div>
            <div class="card"><a href="WorkerServiceHistory.php"><i class="fas fa-history"></i>  Service History</a></div>
            <div class="card"><a href="WorkerHelpFAQ.php"><i class="fas fa-question-circle"></i>  Help & FAQs</a></div>
        </div>

        
        <section class="notifications">
            <h3><i class="fas fa-envelope"></i> Notifications</h3>
          <ul>
  <div class="view-all-link" style="margin-top: 10px;">
        <a href="WorkerNotification.php" style="text-decoration: none; color: #007bff; font-weight: 600;">
            View All Notifications &rarr;
        </a>
    </div>          </ul>
      </section>


    </div>

    <footer>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> Home Service Management System. All Rights Reserved.
        </div>
    </footer>
</body>
</html>
