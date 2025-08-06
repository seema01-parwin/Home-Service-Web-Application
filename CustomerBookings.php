<?php  
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin1.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cancel booking
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $booking_id = intval($_GET['cancel']);
    $conn->query("UPDATE bookings SET booking_status='Cancelled', status='Cancelled' WHERE booking_id=$booking_id AND customer_id=$customer_id");
    header("Location: CustomerBookings.php");
    exit();
}

// Soft delete booking
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $conn->query("UPDATE bookings SET customer_visible=0 WHERE booking_id=$booking_id AND customer_id=$customer_id");
    header("Location: CustomerBookings.php");
    exit();
}

// Filters
$filter_status = $_GET['status'] ?? '';
$filter_date = $_GET['date'] ?? '';
$filter_service = $_GET['service'] ?? '';

$sql = "SELECT b.*, s.service_name, w.fullname AS worker_name 
        FROM bookings b
        JOIN services s ON b.service_id = s.service_id
        JOIN workers w ON b.worker_id = w.worker_id
        WHERE b.customer_id = ? AND b.customer_visible = 1";

$params = [$customer_id];
$types = "i";

if ($filter_status != "") {
    $sql .= " AND b.booking_status = ?";
    $params[] = $filter_status;
    $types .= "s";
}
if ($filter_date != "") {
    $sql .= " AND b.date = ?";
    $params[] = $filter_date;
    $types .= "s";
}
if ($filter_service != "") {
    $sql .= " AND s.service_name = ?";
    $params[] = $filter_service;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$services = $conn->query("SELECT DISTINCT service_name FROM services WHERE active = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - SP Home Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

            
body {
    font-family: Arial, sans-serif;
   background: #fbecff;
    padding: 20px;

  }

        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        header {
            background: linear-gradient(90deg,rgb(170, 0, 176),rgb(138, 61, 201));
            color: white;
            padding: 30px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
           
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        header h2 {
            font-size: 1.8rem;
        }

        nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color:rgb(219, 161, 255);
        }

        .container {
    flex: 1;
    max-width: 1100px;
    margin: 30px auto;
    background:white;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

        @keyframes fadeInCard {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color:rgb(92, 18, 138);
            font-size: 2rem;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .filters input[type="date"],
        .filters select {
            padding: 10px 14px;
            font-size: 1rem;
            border: 1.5px solid #ced4da;
            border-radius: 6px;
            width: 180px;
            transition: all 0.3s ease;
        }

        .filters button {
            background-color:rgb(126, 48, 179);
            border: none;
            padding: 10px 22px;
            font-size: 1rem;
            border-radius: 7px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .filters button:hover {
            background-color:rgb(71, 0, 109);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            animation: fadeInTable 1s ease;
            
        }

        @keyframes fadeInTable {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        th, td {
            padding: 14px;
            border: 1px solid #dee2e6;
            text-align: center;
            
        }


        thead {
            background-color:rgb(107, 3, 163);
            color: white;
        }

        tbody tr:hover {
            background-color:rgb(237, 215, 252);
            transition: background-color 0.3s ease;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s, background-color 0.3s;
        }

        .btn.cancel {
            background-color: #fd7e14;
            color: white;
        }

        .btn.cancel:hover {
            background-color: #e66c0d;
            transform: scale(1.05);
        }

        .btn.delete {
            background-color: #dc3545;
            color: white;
        }

        .btn.delete:hover {
            background-color: #b02a37;
            transform: scale(1.05);
        }

        footer {
    background: #630675;
    color: white;
    text-align: center;
    padding: 18px 12px;
    font-size: 0.95rem;
    position: relative; /* not fixed */
    bottom: 0;
    width: 100%;
}

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: center;
            }

            .filters input, .filters select {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>My Bookings</h1>
    <nav>
        <a href="CustomerDashboard1.php">Dashboard</a>
        <a href="BookServices.php">Book Service</a>
        <a href="CustomerLogout1.php">Logout</a>
    </nav>
</header>

<div class="container">
   

    <form method="GET" class="filters">
        <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" />
        <select name="status">
            <option value="">--Status--</option>
            <option value="Pending" <?= $filter_status == "Pending" ? "selected" : "" ?>>Pending</option>
            <option value="Approved" <?= $filter_status == "Approved" ? "selected" : "" ?>>Approved</option>
            <option value="Completed" <?= $filter_status == "Completed" ? "selected" : "" ?>>Completed</option>
            <option value="Cancelled" <?= $filter_status == "Cancelled" ? "selected" : "" ?>>Cancelled</option>
        </select>
        <select name="service">
            <option value="">--Service--</option>
            <?php while ($row = $services->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['service_name']) ?>" <?= $filter_service == $row['service_name'] ? "selected" : "" ?>>
                    <?= htmlspecialchars($row['service_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Worker</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): $i = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td><?= htmlspecialchars($row['worker_name']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['time']) ?></td>
                    <td><?= htmlspecialchars($row['booking_status']) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td>
                        <?php if ($row['booking_status'] == "Pending"): ?>
                            <a href="?cancel=<?= $row['booking_id'] ?>" class="btn cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $row['booking_id'] ?>" class="btn delete" onclick="return confirm('Delete this booking from view?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No bookings found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!--<footer>
    &copy; <?= date("Y") ?> SPI Home Service Management System. All rights reserved.
</footer>-->

</body>
</html>


