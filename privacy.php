<!DOCTYPE html>  
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Privacy Policy - HSMS</title>

  <!-- Font Awesome for back button -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #fbecff;
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background: #630675;
      color: white;
      padding: 2rem 4rem;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 24px;
    }

    .container {
      flex: 1;
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
    }

    .back-button {
      display: inline-block;
      margin-bottom: 1.5rem;
      padding: 8px 16px;
      background-color: rgb(185, 99, 255);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .back-button:hover {
      background-color: rgb(160, 79, 214);
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      animation: fadeInUp 0.8s ease forwards;
      opacity: 0;
      transform: translateY(20px);
    }

    .card h2 {
      color: rgb(179, 0, 255);
      margin-top: 0;
    }

    .card p, .card ul {
      margin-bottom: 1rem;
    }

    .card ul {
      padding-left: 1.2rem;
    }

    a {
      color: rgb(149, 0, 255);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    footer {
      background: #630675;
      padding: 2rem;
      text-align: center;
      font-size: 14px;
      color: white;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 600px) {
      header h1 {
        font-size: 18px;
      }

      .card {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>SPI HSMS - Privacy Policy</h1>
  </header>

  <div class="container">
    <a href="javascript:history.back()" class="back-button"><i class="fas fa-arrow-left"></i> Go Back</a>

    <div class="grid">
      <div class="card">
        <h2>1. Information We Collect</h2>
        <p>We collect information to provide better services to our users, including customers and workers:</p>
        <ul>
          <li><strong>Personal Information:</strong> Name, email address, phone number, profile picture, address, and other registration details.</li>
          <li><strong>Service Details:</strong> Bookings, service categories, dates, locations, and feedback/reviews.</li>
          <li><strong>Usage Data:</strong> IP address, browser type, and interaction with the platform.</li>
        </ul>
      </div>

      <div class="card">
        <h2>2. How We Use Your Information</h2>
        <ul>
          <li>Provide, operate, and maintain our services.</li>
          <li>Facilitate bookings between customers and workers.</li>
          <li>Communicate about bookings, support, and updates.</li>
          <li>Improve user experience.</li>
          <li>Ensure security and prevent fraud.</li>
        </ul>
      </div>

      <div class="card">
        <h2>3. Information Sharing</h2>
        <p>We do not sell your information. We may share it with:</p>
        <ul>
          <li>Workers and customers for bookings.</li>
          <li>Service providers under confidentiality.</li>
          <li>Legal authorities when required.</li>
        </ul>
      </div>

      <div class="card">
        <h2>4. Data Security</h2>
        <p>We apply reasonable security measures to protect your data. However, no method of transmission is 100% secure online.</p>
      </div>

      <div class="card">
        <h2>5. Your Rights</h2>
        <ul>
          <li>Access and update your personal data.</li>
          <li>Request account/data deletion.</li>
          <li>Opt out of marketing emails.</li>
        </ul>
        <p>Email us at <a href="mailto:support@hsms.com">support@hsms.com</a> to make a request.</p>
      </div>

      <div class="card">
        <h2>6. Cookies & Tracking</h2>
        <p>We may use cookies and similar tech to analyze usage and enhance experience. You can disable cookies in your browser settings.</p>
      </div>

      <div class="card">
        <h2>7. Third-Party Links</h2>
        <p>Our platform may include links to other websites. We are not responsible for their privacy practices or content.</p>
      </div>

      <div class="card">
        <h2>8. Changes to This Policy</h2>
        <p>We may revise this policy. Changes will be updated on this page. Continued use of the site means you accept the revised policy.</p>
      </div>

      <div class="card">
        <h2>9. Contact Us</h2>
        <p>If you have questions, contact us at <a href="mailto:support@hsms.com">support@hsms.com</a>.</p>
        <p><small>Effective Date: June 22, 2025</small></p>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 HSMS - SPI Home Service Management System. All rights reserved.
  </footer>

</body>
</html>
