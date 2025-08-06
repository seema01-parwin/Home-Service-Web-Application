<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Help & FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right,rgb(253, 228, 253),rgb(233, 176, 255));
        }

        /* Header Nav */
        .header {
            background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: slideDown 0.6s ease forwards;
        }

        .logo {
            font-size: 22px;
            font-weight: 600;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            position: relative;
            font-weight: 500;
            padding: 6px 0;
            transition: color 0.3s ease;
        }

        nav ul li a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            left: 0;
            bottom: -3px;
            background-color: #fff;
            transition: width 0.3s ease;
        }

        nav ul li a:hover::after {
            width: 100%;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .menu-toggle span {
            background: #fff;
            height: 3px;
            margin: 4px 0;
            width: 25px;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            nav ul {
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background:rgb(118, 16, 181);
                flex-direction: column;
                align-items: flex-start;
                padding: 20px 30px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            nav ul.active {
                max-height: 300px;
            }

            .menu-toggle {
                display: flex;
            }
        }

        /* FAQ Section */
        .faq-box {
            max-width: 800px;
            margin: 100px auto 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
        }

        .accordion-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .accordion-header {
            padding: 15px 20px;
            background-color:rgb(101, 25, 146);
            color: white;
            cursor: pointer;
            font-weight: bold;
            user-select: none;
        }

        .accordion-content {
            padding: 15px 20px;
            display: none;
            background-color: #f9f9f9;
            color: #333;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Contact Form */
        .contact-form {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }

        .contact-form h3 {
            margin-bottom: 15px;
            color:rgb(157, 0, 255);
        }

        .contact-form label {
            display: block;
            margin-top: 10px;
            color: #444;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .contact-form button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
        }

        .contact-form button:hover {
            background-color: #218838;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- HEADER NAVIGATION -->
<header class="header">
    <div class="logo">Help & FAQs</div>
    <nav>
        <ul id="navLinks">
            <li><a href="WorkerDashboard1.php">Dashboard</a></li>
            <li><a href="WorkerLogout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="menu-toggle" id="menuToggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
</header>

<!-- FAQ SECTION -->
<div class="faq-box">
    <h2>Worker Help & Frequently Asked Questions</h2>

    <?php if (isset($_POST['submit_support'])): ?>
        <div class="success-message">Thank you! Your message has been sent.</div>
    <?php endif; ?>

    <?php
    $faqs = [
        "How do I accept a new service booking?" => "Go to your dashboard’s “Bookings” section, view the request, and click “Accept”. The customer will be notified.",
        "Can I update my working hours or availability?" => "Yes, go to “Profile Settings” → “Availability” to update your preferred working hours and days.",
        "Where can I upload proof of completed work?" => "In your dashboard, go to “Work Proofs” and upload images or documents related to completed services.",
        "How do I track my earnings?" => "Navigate to the “Earnings” section in your dashboard to view a breakdown of completed services and payments.",
        "Who should I contact for support?" => "If you need help, please use the “Contact Support” form below or email us at support@homeservice.com."
    ];

    foreach ($faqs as $question => $answer): ?>
        <div class="accordion-item">
            <div class="accordion-header"><?= $question ?></div>
            <div class="accordion-content"><?= $answer ?></div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Accordion Toggle
document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', function () {
        const content = this.nextElementSibling;
        const isOpen = content.style.display === 'block';
        document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
        content.style.display = isOpen ? 'none' : 'block';
    });
});

// Mobile Menu Toggle
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});
</script>

</body>
</html>
