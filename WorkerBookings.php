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


// Handle Accept / Decline actions
if (isset($_GET['action'], $_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $action = $_GET['action'];

    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE bookings SET booking_status='Confirmed' WHERE booking_id=? AND worker_id=? AND booking_status='Pending'");
        $stmt->bind_param("ii", $booking_id, $worker_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE bookings SET booking_status='Cancelled' WHERE booking_id=? AND worker_id=? AND booking_status='Pending'");
        $stmt->bind_param("ii", $booking_id, $worker_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: WorkerBookings.php");
    exit();
}

// Filter by booking_status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$allowed_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'all'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'all';
}

// Prepare SQL query with optional booking_status filter
if ($status_filter === 'all') {
    $sql = "SELECT b.*, c.name as customer_name, s.service_name
            FROM bookings b
            LEFT JOIN customers c ON b.customer_id = c.customer_id
            LEFT JOIN services s ON b.service_id = s.service_id
            WHERE b.worker_id = ?
            ORDER BY b.booking_datetime DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $worker_id);
} else {
    $sql = "SELECT b.*, c.name as customer_name, s.service_name
            FROM bookings b
            LEFT JOIN customers c ON b.customer_id = c.customer_id
            LEFT JOIN services s ON b.service_id = s.service_id
            WHERE b.worker_id = ? AND b.booking_status = ?
            ORDER BY b.booking_datetime DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $worker_id, $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Worker Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right,rgb(253, 228, 253),rgb(233, 176, 255));
            padding: 0;
            margin: 0;
        }

        /* Header */
        header {
            background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
            color: #fff;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.5s ease;
        }

        header h1 {
            font-size: 24px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            position: relative;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: white;
            transition: 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        .container {
            padding: 30px;
        }

        .go-back {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
            margin-bottom: 20px;
            display: inline-block;
        }

        .go-back:hover {
            background-color: #5a6268;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            animation: fadeIn 0.6s ease-in;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color:rgb(208, 55, 255);
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        .actions a {
            margin-right: 8px;
            padding: 6px 12px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
            font-size: 14px;
        }

        .accept { background-color: #28a745; }
        .accept:hover { background-color: #218838; }

        .decline { background-color: #dc3545; }
        .decline:hover { background-color: #c82333; }

        .filter-form {
            margin: 20px 0;
        }

        select {
            padding: 6px 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            nav {
                display: flex;
                flex-wrap: wrap;
            }

            table, thead, tbody, th, td, tr {
                font-size: 14px;
            }

            .container {
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Manage Your Bookings</h1>
    <nav>
        <a href="WorkerDashboard1.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="WorkerProfile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="WorkerLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="container">
<!--    <a class="go-back" href="WorkerDashboard.php"><i class="fas fa-arrow-left"></i> Go Back</a>-->


    <form method="GET" class="filter-form">
        <label for="status">Filter by status:</label>
        <select name="status" id="status" onchange="this.form.submit()">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Confirmed" <?= $status_filter === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="Completed" <?= $status_filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= $status_filter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </form>

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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="8" style="text-align:center;">No bookings found.</td></tr>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['booking_id']) ?></td>
                        <td><?= htmlspecialchars($row['customer_name'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($row['service_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars(substr($row['time'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($row['service_address']) ?></td>
                        <td><?= htmlspecialchars($row['booking_status']) ?></td>
                        <td class="actions">
                            <?php if ($row['booking_status'] === 'Pending'): ?>
                                <a class="accept" href="?action=accept&booking_id=<?= $row['booking_id'] ?>" onclick="return confirm('Accept this booking?')">Accept</a>
                                <a class="decline" href="?action=decline&booking_id=<?= $row['booking_id'] ?>" onclick="return confirm('Decline this booking?')">Decline</a>
                            <?php else: ?>
                                <em>No actions</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>


<?php
$stmt->close();
$conn->close();
?>
