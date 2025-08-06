<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Now run your query
$contact_sql = "SELECT email, phone, address, working_hours FROM contacts LIMIT 1";
$contact_result = $mysqli->query($contact_sql);

$contact = null;
if ($contact_result && $contact_result->num_rows > 0) {
    $contact = $contact_result->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>About Us</title>
  <style>
    /* same CSS as before for style, omitted here for brevity */
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #fbecff;
      color: #333;
    }
    header {
      background: #630675;
      color: white;
      padding: 40px 20px;
      text-align: center;
    }
    section {
      padding: 50px 20px;
      max-width: 1100px;
      margin: auto;
    }
    .section-title {
      text-align: center;
      font-size: 2em;
      margin-bottom: 30px;
      color:rgb(166, 0, 255);
    }
    .about-content, .mission-content {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      align-items: center;
    }
    .about-content img, .mission-content img {
      flex: 1;
      max-width: 500px;
      width: 100%;
      border-radius: 10px;
    }
    .about-text, .mission-text {
      flex: 2;
    }
    .info-block {
      background: #fff;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-top: 20px;
    }
    .contact-details {
      background:rgb(238, 181, 255);
      padding: 20px;
      border-left: 5px solidrgb(170, 0, 255);
      border-radius: 8px;
      margin-top: 40px;
    }

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
  background-color: #6c077a
}

    footer {
      text-align: center;
      padding: 40px;
      background: #630675;
      font-size: 14px;
      color: white;
    }
  </style>
</head>
<body>

<header>
  <h1>About Our SPI Home Service Management System</h1>
  <p>Empowering homes with reliable service, one booking at a time.</p>
</header>

<section>
  <h2 class="section-title">Who We Are</h2>
  <div class="about-content">
    <img src="https://img.freepik.com/premium-vector/diverse-group-discusses-ideas-collaboratively-while-using-laptops-vibrant-workspace-environment-customizable-cartoon-illustration-group-discussions_585735-38359.jpg" alt="About Us" />
    <div class="about-text">
      <p>We are a team of passionate developers and service management professionals dedicated to making your home service experience seamless, reliable, and secure. Whether it’s plumbing, electrical work, cleaning, or handyman tasks, our platform connects trusted professionals to the customers who need them.</p>
      <p>We bring together smart technology, skilled workers, and thoughtful customer service to build lasting trust and convenience in every service interaction.</p>
    </div>
  </div>
</section>

<section>
  <h2 class="section-title">Our Mission, Vision & Goals</h2>
  <div class="mission-content">
    <div class="mission-text">
      <div class="info-block">
        <h3>Our Mission</h3>
        <p>To simplify home services by connecting customers with skilled professionals through a reliable and user-friendly platform.</p>
      </div>
      <div class="info-block">
        <h3>Our Vision</h3>
        <p>To become the leading digital platform for home services across the region by ensuring quality, trust, and innovation.</p>
      </div>
      <div class="info-block">
        <h3>Our Goal</h3>
        <p>To ensure customer satisfaction, grow a network of verified professionals, and continuously improve our technology to enhance the service experience.</p>
      </div>
    </div>
    <img src="https://i.pinimg.com/736x/ab/9c/e9/ab9ce9819b7164fd28c302cde32d61a9.jpg" alt="Mission Vision Goal" />
  </div>
</section>

<section>
  <h2 class="section-title">Contact Us</h2>
  <?php if ($contact): ?>
    <div class="contact-details">
      <p><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($contact['phone']) ?></p>
      <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($contact['address'])) ?></p>
      <p><strong>Working Hours:</strong> <?= htmlspecialchars($contact['working_hours']) ?></p>
    </div>
  <?php else: ?>
    <p class="contact-details">Contact details are not available at the moment. Please check back later.</p>
  <?php endif; ?>
</section>

<div class="logout-section">
            <a href="Home.php" class="logout-button"><i class="fas fa-left-arrow">← Back to Home</a>
        </div>

<br>

<footer>
  &copy; <?= date("Y") ?> SPI Home Service Management System. All rights reserved.
</footer>

</body>
</html>
