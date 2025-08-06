<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

$sql = "SELECT b.*, c.name AS customer_name, s.service_name
        FROM bookings b
        LEFT JOIN customers c ON b.customer_id = c.customer_id
        LEFT JOIN services s ON b.service_id = s.service_id
        WHERE b.worker_id = ? AND b.booking_status IN ('Pending', 'Confirmed')
        ORDER BY b.booking_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Current Bookings</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(to right,rgb(253, 228, 253),rgb(233, 176, 255));
        padding: 0;
        min-height: 100vh;
    }

    header {
        background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    header .logo {
        display: flex;
        align-items: center;
    }

    header .logo img {
        width: 40px;
        margin-right: 10px;
    }

    nav ul {
        list-style: none;
        display: flex;
        gap: 20px;
    }

    nav a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    nav a:hover {
        color:rgb(208, 120, 249);
    }

    .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        animation: fadeIn 0.7s ease-in-out;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        animation: fadeIn 0.7s ease-in-out;
    }

    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f9f9f9;
        color: white;
    }

    tr:nth-child(even) {
        
        background:rgb(180, 59, 255);
    }

    .status-pending {
        color: orange;
        font-weight: bold;
    }

    .status-confirmed {
        color: green;
        font-weight: bold;
    }

    .go-back {
        display: inline-block;
        margin: 20px auto 0;
        padding: 10px 20px;
        background-color:rgb(101, 47, 163);
        color: white;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 16px;
        transition: background-color 0.3s ease, transform 0.2s;
        text-align: center;
    }

    .go-back i {
        margin-right: 8px;
    }

    .go-back:hover {
        background-color:rgb(81, 30, 144);
        transform: translateY(-2px);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 15px;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        td {
            display: flex;
            justify-content: space-between;
            padding: 10px 5px;
            border-bottom: none;
        }

        td::before {
            content: attr(data-label);
            font-weight: bold;
            flex: 1;
            color: #444;
        }
    }
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="Image/Logo_Home.png" alt="Logo">
        <h2>SPI Home Services</h2>
    </div>
    <!--<nav>
        <ul>
            <li><a href="WorkerDashboard1.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        </ul>
    </nav>-->
</header>

<div class="container">
    <h1>Your Current Bookings</h1>

    <?php if ($result->num_rows === 0): ?>
        <p style="text-align: center;">No current bookings found.</p>
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
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Booking ID"><?= htmlspecialchars($row['booking_id']) ?></td>
                        <td data-label="Customer"><?= htmlspecialchars($row['customer_name'] ?? 'Unknown') ?></td>
                        <td data-label="Service"><?= htmlspecialchars($row['service_name'] ?? 'N/A') ?></td>
                        <td data-label="Date"><?= htmlspecialchars($row['date']) ?></td>
                        <td data-label="Time"><?= htmlspecialchars(substr($row['time'], 0, 5)) ?></td>
                        <td data-label="Address"><?= htmlspecialchars($row['service_address']) ?></td>
                        <td data-label="Status" class="status-<?= strtolower($row['booking_status']) ?>">
                            <?= htmlspecialchars($row['booking_status']) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="WorkerDashboard1.php" class="go-back"><i class="fas fa-arrow-left"></i> Go Back</a>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
