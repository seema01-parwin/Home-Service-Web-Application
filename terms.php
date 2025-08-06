<!DOCTYPE html>  
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Terms and Conditions - HSMS</title>

  <!-- Font Awesome (for back icon) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #fbecff;
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background: #630675;
      color: #fff;
      padding: 2rem 4rem;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 24px;
    }

    .container {
      flex: 1;
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .back-button {
      display: inline-block;
      margin-bottom: 1.5rem;
      padding: 8px 16px;
      background-color: rgb(153, 56, 210);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s ease, transform 0.3s ease;
    }

    .back-button:hover {
      background-color: rgb(116, 0, 179);
      transform: translateX(-3px);
    }

    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeSlideUp 0.8s ease forwards;
    }

    .card:hover {
      transform: scale(1.01);
      transition: transform 0.3s ease;
    }

    .card h2 {
      color: #630675;
      margin-top: 0;
    }

    .card p, .card ul {
      margin-bottom: 1rem;
    }

    ul {
      padding-left: 1.2rem;
    }

    a {
      color: #630675;
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

    @keyframes fadeSlideUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Stagger effect for each card */
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.4s; }
    .card:nth-child(4) { animation-delay: 0.6s; }
    .card:nth-child(5) { animation-delay: 0.8s; }
    .card:nth-child(6) { animation-delay: 1s; }
    .card:nth-child(7) { animation-delay: 1.2s; }
    .card:nth-child(8) { animation-delay: 1.4s; }
    .card:nth-child(9) { animation-delay: 1.6s; }
    .card:nth-child(10) { animation-delay: 1.8s; }
    .card:nth-child(11) { animation-delay: 2s; }
    .card:nth-child(12) { animation-delay: 2.2s; }
    .card:nth-child(13) { animation-delay: 2.4s; }

    @media (max-width: 600px) {
      header h1 {
        font-size: 18px;
      }

      .container {
        padding: 0 0.5rem;
      }

      .card {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>Terms and Conditions - SPI HSMS</h1>
  </header>

  <div class="container">
    <a href="javascript:history.back()" class="back-button"><i class="fas fa-arrow-left"></i> Go Back</a>

    <div class="card">
      <p>Welcome to the Home Service Management System (HSMS). These Terms and Conditions ("Terms") govern your use of our website and services. By accessing or using HSMS, you agree to comply with and be bound by these Terms.</p>
    </div>

    <div class="card">
      <h2>1. Acceptance of Terms</h2>
      <p>By using our platform, you represent that you are at least 18 years old and have the legal capacity to enter into these Terms.</p>
    </div>

    <div class="card">
      <h2>2. Services Description</h2>
      <p>HSMS provides an online platform to connect customers seeking home services with registered workers offering such services. HSMS acts only as a facilitator and does not provide services directly.</p>
    </div>

    <div class="card">
      <h2>3. User Accounts</h2>
      <ul>
        <li>Users must register to access certain features. You agree to provide accurate, current, and complete information during registration.</li>
        <li>You are responsible for maintaining the confidentiality of your login credentials and all activities under your account.</li>
        <li>You agree to notify us immediately of any unauthorized use of your account.</li>
      </ul>
    </div>

    <div class="card">
      <h2>4. Booking and Payment</h2>
      <ul>
        <li>Customers can book services through the platform by selecting service categories, dates, and locations.</li>
        <li>Payments, if applicable, are made between customers and workers as agreed. HSMS does not handle payments directly.</li>
        <li>HSMS is not responsible for any disputes regarding payments or service quality.</li>
      </ul>
    </div>

    <div class="card">
      <h2>5. User Conduct</h2>
      <p>Users agree not to:</p>
      <ul>
        <li>Use the platform for any unlawful or fraudulent activities.</li>
        <li>Harass, abuse, or harm other users or workers.</li>
        <li>Upload any content that is inappropriate, offensive, or infringes on intellectual property rights.</li>
      </ul>
    </div>

    <div class="card">
      <h2>6. Worker Responsibilities</h2>
      <p>Workers are responsible for providing services professionally and timely. Workers must ensure that any proof of work or documents uploaded are accurate and legitimate.</p>
    </div>

    <div class="card">
      <h2>7. Limitation of Liability</h2>
      <p>HSMS provides the platform "as is" without warranties of any kind. We are not liable for any direct, indirect, incidental, or consequential damages arising from the use or inability to use the platform or services.</p>
    </div>

    <div class="card">
      <h2>8. Termination</h2>
      <p>We reserve the right to suspend or terminate accounts that violate these Terms or for any other reason at our discretion.</p>
    </div>

    <div class="card">
      <h2>9. Privacy</h2>
      <p>Your use of the platform is also governed by our <a href="privacy_policy.html">Privacy Policy</a>, which explains how we collect, use, and protect your information.</p>
    </div>

    <div class="card">
      <h2>10. Changes to Terms</h2>
      <p>HSMS reserves the right to modify these Terms at any time. Updated Terms will be posted on this page with the effective date. Continued use of the platform after changes signifies your acceptance.</p>
    </div>

    <div class="card">
      <h2>11. Governing Law</h2>
      <p>These Terms shall be governed and interpreted according to the laws of the country where HSMS operates.</p>
    </div>

    <div class="card">
      <h2>12. Contact Us</h2>
      <p>If you have any questions about these Terms, please contact us at <a href="mailto:support@hsms.com">support@hsms.com</a>.</p>
      <p><small>Effective Date: June 22, 2025</small></p>
    </div>
  </div>

  <footer>
    &copy; 2025 HSMS - SPI Home Service Management System. All rights reserved.
  </footer>

</body>
</html>
