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

// Fetch completed or cancelled bookings for this worker
$sql = "SELECT b.*, c.name AS customer_name, s.service_name
        FROM bookings b
        LEFT JOIN customers c ON b.customer_id = c.customer_id
        LEFT JOIN services s ON b.service_id = s.service_id
        WHERE b.worker_id = ? AND b.booking_status IN ('Completed', 'Cancelled')
        ORDER BY b.booking_datetime DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Service History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
             background: linear-gradient(to right,rgb(253, 228, 253),rgb(233, 176, 255));
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background:rgb(141, 45, 197);
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .completed {
            color: green;
            font-weight: bold;
        }
        .cancelled {
            color: red;
            font-weight: bold;
        }

        /* Header Nav */
    .header {
        background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
        color: #fff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .logo {
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .nav-links {
        display: flex;
        gap: 20px;
        list-style: none;
        transition: all 0.3s ease-in-out;
    }

    .nav-links li a {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        position: relative;
    }

    .nav-links li a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -4px;
        left: 0;
        background-color:rgb(112, 0, 209);
        transition: width 0.3s;
    }

    .nav-links li a:hover::after {
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
        transition: all 0.3s ease-in-out;
    }

    @media (max-width: 768px) {
        .nav-links {
            position: absolute;
            top: 65px;
            right: 0;
            background:rgb(59, 52, 64);
            flex-direction: column;
            width: 100%;
            max-height: 0;
            overflow: hidden;
        }

        .nav-links.active {
            max-height: 300px;
            padding: 15px 30px;
        }

        .menu-toggle {
            display: flex;
        }
    }

        
    </style>
</head>
<body>

<header class="header">
    <div class="logo">Service History</div>
    <nav>
        <ul class="nav-links" id="navLinks">
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

<br>
<br>

<?php if ($result->num_rows === 0): ?>
    <p>No completed or cancelled bookings found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Address</th>
                <th>Status</th>
                <th>Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['booking_id']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($row['service_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars(substr($row['time'], 0, 5)) ?></td>
                    <td><?= htmlspecialchars($row['service_address']) ?></td>
                    <td class="<?= strtolower($row['booking_status']) ?>">
                        <?= htmlspecialchars($row['booking_status']) ?>
                    </td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>

</body>
</html>
