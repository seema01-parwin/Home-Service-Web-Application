<?php

// Prevent Browser from Caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home Service Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
  font-family: 'Poppins', sans-serif;
}

/* Global styles */
body {
  margin: 0;
  background: #fbecff;
  color: #333;
}

/* Header */
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
  width: 300px;
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
  display: flex;
  align-items: center;
  gap: 5px;
}

nav ul li a:hover {
  color: #f3b2ff;
}

/* Modal Common Style */
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: #630675;
  margin: 15% auto;
  padding: 30px;
  border-radius: 8px;
  width: 300px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-content button {
  margin: 10px auto;
  padding: 12px;
  width: 80%;
  font-size: 16px;
  cursor: pointer;
  background-color: #edc1f8;
  color: white;
  border: none;
  border-radius: 4px;
}

.modal-content button:hover {
  background-color: #9436b1;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  cursor: pointer;
}

/* Hero */
.hero {
  position: relative;
  width: 100%;
  height: 80vh;
  overflow: hidden;
  
}

.hero-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1;
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  color: white;
  padding-top: 130px;
  padding-bottom: 150px;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.507);
  width: 100%;
  object-fit: cover;
}

.hero-content h2 {
  font-size: 2.5em;
}

.hero-content p {
  font-size: 1.2em;
}

.hero-content button {
  font-size: 1em;
  margin-top: 15px;
  padding: 15px 40px;
  background-color: #9d2cbf;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  overflow: hidden;
  transition: 0.3s;
}

.hero-content button:hover {
  transform: scale(1.05);
  color: #f4a4ff;
}

.heading{
  text-align: center;
  padding: 5px;
  font-size: 3rem;
  color: #4f145e;
}

.heading span{
  background: #651e6e;
  color: #ffffff;
  display: inline-block;
  padding: .5rem 3rem;
  clip-path: polygon(100% 0, 93% 50%, 100% 99%, 0% 100%, 7% 50%, 0% 0%);
}

/* Features */
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(245px, 1fr));
  gap: 30px;
  padding: 10px 30px;
  padding-bottom: 5px;
}

.feature-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


.feature-box i {
  font-size: 50px;
  color:rgb(234, 115, 245);
}

.feature-box h3 {
  margin: 10px 0;
  font-size: 1.2em;
}

.stars i {
  color: gold;
  font-size: 20px;
}

/* how-it-works */
.how-it-works {
  padding: 20px 30px;
  background: #fbecff;
  text-align: center;
}

.how-it-works .heading {
  font-size: 3em;
  margin-bottom: 50px;
}

.how-it-works img {
  width: 200px;
  transition: transform 0.3s ease;
}

.how-it-works img:hover  {
  transform: rotate(5deg) scale(1.1);
}

.steps-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
  max-width: 1400px;
  margin: 0 auto;
  margin-bottom: 20px;
}

.step-box {
  background: #fff;
  padding: 30px 20px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.step-box i {
  font-size: 30px;
  color: #e100ff;
  margin-bottom: 15px;
}

.step-box h3 {
  font-size: 1.2em;
  margin-bottom: 10px;
}

.step-box p {
  font-size: 0.95em;
  color: #555;
}

.step-box:hover {
  transform: translateY(-5px);
}

/* Popular */
.popular-section {
  padding: 20px 30px;
  background-color: #fbecff;
  text-align: center;
}

.popular-section .heading {
  font-size: 3em;
  margin-bottom: 50px;
}

.popular-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 30px;
  max-width: 1280px;
  margin: 0 auto;
}

.popular-box {
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.3s ease;
}

.popular-box:hover {
  transform: translateY(-5px);
}

.popular-box img {
  width: 65%;
  height: 140px;
  object-fit: cover;
  border-radius: 100px;
  transition: transform 0.3s ease;
}

.popular-box img:hover {
  transform: scale(1.1);
}

.popular-box h3 {
  margin-bottom: 1px;
  font-size: 1.2em;
}

.popular-box p {
  font-size: 0.95em;
  color: #555;
  margin-bottom: 3px;
}

.stars i {
  color: #ffc107;
  font-size: 18px;
}

/* why-choose */
.why-choose-section {
  padding: 20px 30px;
  background: #fbecff;
  text-align: center;
  font-weight: 3em;
  overflow-y: auto;
}

.benefits-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
  max-width: 1400px;
  margin: 0 auto;
  margin-bottom: 20px;
}

.benefit-box {
  background: #fff;
  margin-top: 20px;
  margin-bottom: 30px;
  padding: 25px;
  width: 240px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  opacity: 0;
  transform: translateY(30px);
}

.benefit-box:hover {
  transform: scale(1.05);
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.benefit-box img {
  width: 200px;
  margin-bottom: 10px;
  transition: transform 0.3s ease;
}

.benefit-box:hover img {
  transform: rotate(5deg) scale(1.1);
}

.benefit-box h3 {
  font-size: 20px;
  margin-bottom: 12px;
  color: #333;
}

.benefit-box p {
  font-size: 15px;
  color: #666;
}

/* Animation */
.animate-up {
  animation: fadeUp 0.8s ease forwards;
}

.animate-up:nth-child(1) { animation-delay: 0.2s; }
.animate-up:nth-child(2) { animation-delay: 0.4s; }
.animate-up:nth-child(3) { animation-delay: 0.6s; }
.animate-up:nth-child(4) { animation-delay: 0.8s; }

@keyframes fadeUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Footer */
footer {
  background: #630675;
  color: white;
  padding: 40px 20px 20px;
  align-items: center;
  
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  margin-bottom: 20px;
  padding-left: 130px; 
}

.footer-section h3 {
  margin-bottom: 10px;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 6px;
}

.footer-section a {
  color: white;
  text-decoration: none;
}

.footer-section a:hover {
  text-decoration: underline;
  color: #ea00ff;
}

.footer-section i {
  margin-right: 8px;
}

.footer-bottom {
  text-align: center;
  padding-top: 10px;
  font-size: 0.9em;
  border-top: 1px solid #555;
}

/* Mobile Styles */
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
/* Loader Styles */
.loader-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(36, 0, 70, 0.8); /* dark purple transparent */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 6px solid #e0b3ff;
  border-top: 6px solid #5a189a;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Spinner Animation */
@keyframes spin {
  0% {transform: rotate(0deg);}
  100% {transform: rotate(360deg);}
  
}

/* Back to Top Button */
#backToTopBtn {
  display: none;
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 999;
  font-size: 24px;
  border: none;
  outline: none;
  background-color: #9d2cbf;
  color: white;
  cursor: pointer;
  padding: 12px 16px;
  border-radius: 50%;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
  transition: background 0.3s ease;
}

#backToTopBtn:hover {
  background-color: #5a189a;
}

/* Scroll Progress Bar */
#scrollProgressContainer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: rgba(255, 255, 255, 0.1);
  z-index: 9999;
}

#scrollProgressBar {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #a22cbf, #c85ee2);
  transition: width 0.25s ease;
}

/* Scroll Reveal Animation */
.reveal {
  opacity: 0;
  transform: translateY(40px);
  transition: all 0.8s ease;
}

.reveal.active {
  opacity: 1;
  transform: translateY(0);
}


  </style>
</head>

<!-- Back to Top Button -->
<button onclick="scrollToTop()" id="backToTopBtn" title="Go to top">
  ↑
</button>

<body>

  <!-- Scroll Progress Bar -->
<div id="scrollProgressContainer">
  <div id="scrollProgressBar"></div>
</div>

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
      <li><a href="#"><i class="fas fa-home"></i> <span class="nav-text">Home</span></a></li>
      <li><a href="#services"><i class="fas fa-tools"></i> <span class="nav-text">Services</span></a></li>
      <li><a href="AboutUs.php"><i class="fas fa-users"></i> <span class="nav-text">About Us</span></a></li>
      <li><a href="#"><i class="fas fa-sign-in-alt" id="loginBtn"></i> <span class="nav-text" id="ltn">Login</span></a></li>
      <li><a href="#"><i class="fas fa-user-plus" id="registerBtn"></i> <span class="nav-text" id="reg">Register</span></a></li>
    </ul>

    <!-- Login Selection Modal -->
    <div id="loginModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal('loginModal')">&times;</span>
        <h2><i class="fas fa-sign-in-alt"></i>  Login as</h2>
        <button onclick="redirectToLogin('customer')">Customer</button>
        <button onclick="redirectToLogin('worker')">Worker</button>
      </div>
    </div>

    <!-- Register Selection Modal -->
    <div id="registerModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal('registerModal')">&times;</span>
        <h2><i class="fas fa-user-plus"></i>  Register as</h2>
        <button onclick="redirectToRegister('customer')">Customer</button>
        <button onclick="redirectToRegister('worker')">Worker</button>
      </div>
    </div>
  </nav>
</header>

<!-- Hero Selection -->
<section class="hero">
  <img src="Image/Home_Banner - Copy.jpg" alt="Hero Background" class="hero-img">
  <div class="hero-content">
    <h2>Your Trusted Home Service Partner</h2>
    <p>Fast, affordable, and reliable home services right at your doorstep.</p>
    <button onclick="redirectToServices()">Book Now</button>
  </div>
</section>

<!-- Features Selection -->
<h1 class="heading"> Our <span>Services</span></h1>
<section class="features">
  <div class="feature-box">
    <div class="feature-box reveal">  
      <i class="fas fa-bolt"></i><h3>Electrical</h3>
      <p>Professional & Expert electrical solutions</p>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box" id="services">
    <div class="feature-box reveal">
      <i class="fas fa-broom"></i><h3>Cleaning</h3>
      <p>Deep home & office cleaning</p>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
      <i class="fas fa-water"></i><h3>Plumbing</h3>
      <p>Solutions Leak repairs & installation</p>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
    <i class="fas fa-paint-roller"></i><h3>Painting</h3>
    <p>Interior & exterior painting to beautify your space.</p>
    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
    <i class="fas fa-snowflake"></i><h3>AC Services</h3>
    <p>Reliable air conditioning installation</p>
    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
      <i class="fas fa-hammer"></i><h3>Carpentry</h3>
      <p>Woodwork, and furniture installation services.</p>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
      <i class="fas fa-seedling"></i><h3>Gardening</h3>
      <p>Plant installation and tree trimming.</p>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
    </div>
  </div>

  <div class="feature-box">
    <div class="feature-box reveal">
    <i class="fas fa-ellipsis-h"></i> 
      <p>Explore All Our Services</p>
      <h3>Click Here</h3>
      <a href="search1.php"><i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works">
  <h1 class="heading">How <span>It Works</span></h1>
  <div class="steps-container">
    <div class="step-box">
    <img src="Image/search.jpg" alt="Verified Professionals" />
      <h3>1. Choose a Service</h3>
      <p>Browse our wide range of home services tailored to your needs.</p>
    </div>
    <div class="step-box">
    <img src="Image/schedule.jpg" alt="Verified Professionals" />
      <h3>2. Book and Schedule</h3>
      <p>Select a convenient date and time that works best for you.</p>
    </div>
    <div class="step-box">
    <img src="Image/service.jpg" alt="Verified Professionals" />
      <h3>3. Service Completed</h3>
      <p>Our professional arrives on time and gets the job done efficiently.</p>
    </div>
    <div class="step-box">
    <img src="Image/rating.jpg" alt="Verified Professionals" />
      <h3>4. Rate Your Experience</h3>
      <p>Provide feedback to help us maintain high service quality.</p>
    </div>
  </div>
</section>

<!-- Popular Selection -->
<section class="popular-section">
  <h1 class="heading">Top-Rated <span>Workers</span></h1>
  <div class="popular-container">

  <?php
  // DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

  $query = "SELECT * FROM workers WHERE status = 'approved' ORDER BY created_at DESC LIMIT 4";
  $result = mysqli_query($conn, $query);

  while ($row = mysqli_fetch_assoc($result)) {
      $image = !empty($row['profile_picture']) ? $row['profile_picture'] : 'Image/default_user.jpg';
      echo '
      <div class="popular-box">
        <img src="' . $image . '" alt="' . htmlspecialchars($row['fullname']) . '">
        <h3>' . htmlspecialchars($row['fullname']) . '</h3>
        <p>Skill: ' . htmlspecialchars($row['skill']) . '</p>
        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
      </div>';
  }

  mysqli_close($conn);
  ?>

  </div>
</section>
</section>

<!-- Why-Choose Selection -->
<section class="why-choose-section">
  <h1 class="heading">Why <span>Choose Us</span></h1>
  <div class="benefits-container">

    <div class="benefit-box animate-up">
      <img src="Image/verified.jpg" alt="Verified Professionals" />
      <h3>Verified Professionals</h3>
      <p>All our workers are background-checked and approved for your safety.</p>
    </div>

    <div class="benefit-box animate-up">
      <img src="Image/affordable_1.jpg" alt="Affordable Prices" />
      <h3>Affordable Prices</h3>
      <p>High-quality services at prices that won’t break the bank.</p>
    </div>

    <div class="benefit-box animate-up">
      <img src="Image/easy_booking.jpg" alt="Easy Booking" />
      <h3>Easy Booking</h3>
      <p>Book any service with just a few clicks. Simple and quick!</p>
    </div>

    <div class="benefit-box animate-up">
      <img src="Image/support.jpg" alt="Customer Support" />
      <h3>Customer Support</h3>
      <p>Dedicated support team ready to assist you anytime.</p>
    </div>

  </div>
</section>

<footer>
  <div class="footer-container">
    <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="privacy.php"><i class="fas fa-user-shield"></i> Privacy Policy</a></li>
          <li><a href="terms.php"><i class="fas fa-file-contract"></i> Terms & Conditions</a></li>
          <li><a href="CommonFAQ.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
        </ul>
      </div>
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="#"><i class="fas fa-home"></i>Home</a></li>
        <li><a href="#services"><i class="fas fa-tools"></i>Services</a></li>
        <li><a href="#"><i class="fas fa-sign-in-alt"></i>Login</a></li>
        <li><a href="#"><i class="fas fa-user-plus"></i>Register</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Contact</h3>
      <p><i class="fas fa-envelope"></i>spi@homeservices.com</p>
      <p><i class="fas fa-phone"></i>+947-123-4567</p>
      <p><i class="fas fa-map-marker-alt"></i>New Town, Ratnapura.</p>
      <p><i class="fas fa-clock"></i>Mon-Sun 9am–7pm</p>
    </div>
    <div class="footer-section">
      <h3>Follow Us</h3>
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
  </div>
  <p class="footer-bottom">&copy; 2025 Home Service Management System. All rights reserved.</p>
</footer>

<script src="Home1.js"></script>
</body>
</html>