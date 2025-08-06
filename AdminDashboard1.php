<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Dashboard metrics
$workerCount   = $conn->query("SELECT COUNT(*) as total FROM workers")->fetch_assoc()['total'];
$customerCount = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
$bookingCount  = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$serviceCount  = $conn->query("SELECT COUNT(*) as total FROM services")->fetch_assoc()['total'];
$todayBookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['total'];

// Fetch total earnings
$totalEarnings = 0.00;
$result = $conn->query("SELECT SUM(amount) AS total_earnings FROM payments");
if ($result && $row = $result->fetch_assoc()) {
    $totalEarnings = $row['total_earnings'] ?? 0.00;
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color:rgb(254, 242, 255);
}

.sidebar {
    width: 240px;
    background:rgb(126, 69, 131);
    position: fixed;
    top: 0;
    bottom: 0;
    color: white;
    padding: 20px;
}

.sidebar h2 {
    margin: 0 0 20px;
    font-size: 24px;
    text-align: center;
}

.sidebar a {
    display: block;
    color: white;
    padding: 12px;
    margin: 10px 0;
    text-decoration: none;
    border-radius: 6px;
}

.sidebar a:hover {
    background-color:rgb(84, 52, 94);
}

.main {
    margin-left: 260px;
    padding: 30px;
}

header {
    background-color:rgb(191, 104, 199);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(10px, 1fr));
    gap: 20px;
}

.card {
    background-color: white;
    border-radius: 12px;
    padding: 1px;
    margin-bottom: 1px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-6px);
    background: #f5c2ff;
}

.card i {
    font-size: 50px;
    color:rgb(108, 43, 114);
    margin-bottom: 12px;
}

.card h3 {
    font-size: 17px;
    color: #333;
    margin-bottom: 10px;
}

.card p {
    font-size: 22px;
    font-weight: bold;
    color: #555;
}

.logout-btn {
    background:rgb(236, 144, 245);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
}

.logout-btn:hover {
    background:rgb(187, 43, 192);
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .main {
        margin-left: 0;
    }
}

body.dark-theme {
background-color:rgb(41, 30, 47);
color: #f0f0f0;
}
body.dark-theme .card {
background-color:rgb(65, 44, 74);
color: #f0f0f0;
}
body.dark-theme header {
background-color:rgb(164, 60, 185);
}
body.dark-theme .sidebar {
background-color:rgb(45, 32, 55);
}
body.dark-theme .sidebar a {
color: #ccc;
}


    </style>
</head>
<body>

<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="ManageAdminProfile.php"><i class="fas fa-user"></i> My Profile</a>
    <a href="ManageWorkers.php"><i class="fas fa-users-cog"></i> Manage Workers</a>
    <a href="ManageCustomer.php"><i class="fas fa-users"></i> Manage Customers</a>
    <a href="ManageBookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
    <a href="ManageServices.php"><i class="fas fa-tools"></i> Services</a>
    <a href="ManageNotifications.php"><i class="fas fa-envelope"></i> Notifications</a>
    <a href="ManagePayments.php"><i class="fas fa-file-invoice-dollar"></i> Payment & Invoices</a>
    <a href="ContactSupport.php"><i class="fas fa-question-circle"></i> Contact & FAQs</a>
    <form method="post" action="AdminLogout1.php" style="margin-top: 20px;">
        <button class="logout-btn" type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
</div>

<div class="main">
    <header>
        <h1>Welcome, Admin!</h1>
        <h4>Here's what's happening today:</h4>

        <!-- Theme Toggle -->
    <div style="text-align: right; margin-bottom: 15px;">
    <label>
        <input type="checkbox" id="themeToggle"> Dark Mode
    </label>
    </div>
    </header>

    <div class="dashboard">
        <div class="card"><br>
            <i class="fas fa-users-cog"></i>
            <h3>Total Workers</h3>
            <p><?= $workerCount ?></p>
        </div>

        <div class="card"><br>
            <i class="fas fa-users"></i>
            <h3>Total Customers</h3>
            <p><?= $customerCount ?></p>
        </div>

        <div class="card"><br>
            <i class="fas fa-clipboard-list"></i>
            <h3>Total Bookings</h3>
            <p><?= $bookingCount ?></p>
        </div>

        <div class="card"><br>
            <i class="fas fa-tools"></i>
            <h3>Services Offered</h3>
            <p><?= $serviceCount ?></p>
        </div>

        <div class="card"><br>
            <i class="fas fa-calendar-day"></i>
            <h3>Today's Bookings</h3>
            <p><?= $todayBookings ?></p>
        </div>

        <div class="card"><br>
    <i class="fas fa-sack-dollar"></i>
    <h3>Total Earnings</h3>
    <p>Rs. <?= number_format($totalEarnings, 2) ?></p>
</div>

    </div>

    <!-- Charts Section -->
     <div>             
        <br>
        <br>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div style="position: relative; height: 250px; ">
            <canvas id="bookingChart"></canvas>
        </div>
        <div style="position: relative; height: 250px;">
            <canvas id="userChart"></canvas>
        </div>
    </div>
</div>


</div>

<script>
// Theme Toggle Script
const themeToggle = document.getElementById("themeToggle");
themeToggle.addEventListener("change", () => {
    document.body.classList.toggle("dark-theme");
});


// Chart Data (Sample – dynamic data via PHP can be added)
const bookingChartCtx = document.getElementById('bookingChart').getContext('2d');
const bookingChart = new Chart(bookingChartCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], // Replace with PHP if dynamic
        datasets: [{
            label: 'Monthly Bookings',
            data: [5, 10, 6, 12, 8], // Replace with PHP values
            backgroundColor: 'rgba(191, 95, 255, 0.2)',
            borderColor: 'purple',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true
    }
});

const userChartCtx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(userChartCtx, {
    type: 'doughnut',
    data: {
        labels: ['Workers', 'Customers'],
        datasets: [{
            label: 'Users',
            data: [<?= $workerCount ?>, <?= $customerCount ?>],
            backgroundColor: ['rgb(222, 131, 250)', 'rgb(128, 18, 128)'],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true
    }
});
</script>


</body>
</html>
