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

$worker_id = $_SESSION['worker_id'] ?? 0;

if ($worker_id == 0) {
    echo "Unauthorized access.";
    exit;
}

// Total Earnings
$total = $conn->query("
    SELECT SUM(amount) as total_earned 
    FROM payments 
    WHERE worker_id = $worker_id AND payment_status = 'Paid'
")->fetch_assoc()['total_earned'] ?? 0.00;

// Monthly Earnings for Chart
$monthlyData = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(amount) AS earnings
    FROM payments
    WHERE worker_id = $worker_id AND payment_status = 'Paid'
    GROUP BY month
    ORDER BY month ASC
");

$months = [];
$earnings = [];
while ($row = $monthlyData->fetch_assoc()) {
    $months[] = date('M Y', strtotime($row['month'] . '-01'));
    $earnings[] = $row['earnings'];
}

// Earnings Records Table
$payments = $conn->query("
    SELECT p.*, c.name AS customer_name, s.service_name 
    FROM payments p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN customers c ON p.customer_id = c.customer_id
    JOIN services s ON b.service_id = s.service_id
    WHERE p.worker_id = $worker_id AND p.payment_status = 'Paid'
    ORDER BY p.payment_date DESC
");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Earnings Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(to right,rgb(253, 228, 253),rgb(233, 176, 255));
    font-family: 'Poppins', sans-serif;
}

.navbar {
     background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    animation: slideDown 0.6s ease-in-out;
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

.logo {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
}

.menu-icon {
    font-size: 28px;
    color: white;
    display: none;
    cursor: pointer;
}

#menu-toggle {
    display: none;
}

.nav-links {
    display: flex;
    gap: 20px;
}

.nav-links a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    position: relative;
    transition: 0.3s ease;
}

.nav-links a::after {
    content: '';
    position: absolute;
    width: 0%;
    height: 2px;
    bottom: -4px;
    left: 0;
    background-color: #fff;
    transition: width 0.3s ease-in-out;
}

.nav-links a:hover::after,
.nav-links a.active::after {
    width: 100%;
}

.nav-links a:hover {
    color:rgb(245, 131, 255);
}

@media (max-width: 768px) {
    .menu-icon {
        display: block;
    }

    .nav-links {
        position: absolute;
        top: 60px;
        left: 0;
        background-color:rgb(69, 44, 80);
        width: 100%;
        flex-direction: column;
        display: none;
    }

    #menu-toggle:checked + .menu-icon + .nav-links {
        display: flex;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .nav-links a {
        padding: 12px;
        text-align: center;
    }
}

/* Main Container */
.earnings-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

/* Earnings Header */
.earnings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.earnings-header h2 {
    color:rgb(68, 44, 80);
    font-size: 24px;
}

.total-earned {
    background: #27ae60;
    color: #fff;
    padding: 10px 25px;
    border-radius: 8px;
    font-size: 18px;
    margin-top: 10px;
}

/* Chart */
.chart-container {
    margin: 30px 0;
}

/* Table */
.earnings-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.earnings-table th, .earnings-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    font-size: 15px;
}

.earnings-table th {
    background: #f0f2f5;
    color:rgb(71, 44, 80);
}

.earnings-table td {
    color: #555;
}

@media (max-width: 600px) {
    .earnings-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .total-earned {
        margin-top: 15px;
    }

    .earnings-table th, .earnings-table td {
        font-size: 14px;
    }
}

    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">My Earnings</div>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label>
    <nav class="nav-links">
        <a href="WorkerDashboard1.php">Dashboard</a>
    
    
        <a href="WorkerLogout.php">Logout</a>
    </nav>
</header>


<div class="earnings-container">
    <div class="earnings-header">
        
        <div class="total-earned">Total: Rs. <?= number_format($total, 2) ?></div>
    </div>

    <!-- Chart Section -->
    <div class="chart-container">
        <canvas id="earningsChart" height="90"></canvas>
    </div>

    <!-- Table Section -->
    <table class="earnings-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Amount (Rs.)</th>
                <th>Method</th>
                <th>Paid On</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($payments->num_rows > 0): $i = 1; ?>
                <?php while ($row = $payments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $row['booking_id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['service_name']) ?></td>
                        <td><?= number_format($row['amount'], 2) ?></td>
                        <td><?= $row['payment_method'] ?></td>
                        <td><?= date('d M Y', strtotime($row['payment_date'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No earnings data found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Chart Script -->
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Monthly Earnings (Rs.)',
                data: <?= json_encode($earnings) ?>,
                backgroundColor: 'purple',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `Rs. ${ctx.raw}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rs. ' + value
                    }
                }
            }
        }
    });
</script>

</body>
</html>
