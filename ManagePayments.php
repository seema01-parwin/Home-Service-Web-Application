<?php
session_start();
$conn = new mysqli("localhost", "root", "", "home_service_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin.php");
    exit();
}

// Handle Mark as Paid
if (isset($_POST['mark_paid'])) {
    $booking_id = intval($_POST['booking_id']);
    $conn->query("UPDATE bookings SET payment_status = 'Paid', payment_method='Paid' WHERE booking_id = $booking_id");

    $check = $conn->query("SELECT * FROM payments WHERE booking_id = $booking_id");
    if ($check->num_rows == 0) {
        $details = $conn->query("
            SELECT b.*, c.customer_id, w.worker_id 
            FROM bookings b 
            JOIN customers c ON b.customer_id = c.customer_id 
            JOIN workers w ON b.worker_id = w.worker_id 
            WHERE b.booking_id = $booking_id
        ")->fetch_assoc();

        $stmt = $conn->prepare("INSERT INTO payments (booking_id, customer_id, worker_id, amount, payment_method, payment_status, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $method = 'Cash'; 
        $status = 'Paid';
        $remarks = 'Marked as paid by admin';
        $stmt->bind_param("iiidsss", $details['booking_id'], $details['customer_id'], $details['worker_id'], $details['total_amount'], $method, $status, $remarks);
        $stmt->execute();
    }
}

// Handle Send Invoice
if (isset($_POST['send_invoice'])) {
    $booking_id = intval($_POST['booking_id']);

    $booking = $conn->query("
        SELECT b.*, c.customer_id, c.name AS customer_name, w.worker_id, w.fullname AS worker_name
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN workers w ON b.worker_id = w.worker_id
        WHERE b.booking_id = $booking_id
    ")->fetch_assoc();

    if ($booking) {
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, customer_id, worker_id, amount, payment_method, payment_status, payment_date, remarks) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
        
        $payment_method = 'Cash';
        $payment_status = 'Paid';
        $remarks = "Invoice sent by admin";

        $stmt->bind_param(
            "iiiisss",
            $booking_id,
            $booking['customer_id'],
            $booking['worker_id'],
            $booking['total_amount'],
            $payment_method,
            $payment_status,
            $remarks
        );

        if ($stmt->execute()) {
            $conn->query("UPDATE bookings SET payment_status = 'Paid', payment_method = '$payment_method' WHERE booking_id = $booking_id");
            echo "<script>alert('Invoice sent to customer dashboard and payment recorded.');</script>";
        } else {
            echo "<script>alert('Failed to send invoice.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Booking not found.');</script>";
    }
}

// Search filter
$filter = "";
if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $filter = "WHERE c.name LIKE '%$search%' OR w.fullname LIKE '%$search%'";
}

// Fetch bookings with payment info
$sql = "SELECT b.*, c.name AS customer_name, w.fullname AS worker_name
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN workers w ON b.worker_id = w.worker_id
        $filter
        ORDER BY b.booking_datetime DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Payments - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Reset */
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background:rgb(251, 236, 255);
            color: #eee;
            min-height: 100vh;
            padding: 20px;
        }

        h2 {
            color: #9b59b6;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 2rem;
            animation: fadeInDown 1s ease forwards;
        }

        /* Back to Dashboard button */
        .back-btn {
            display: inline-block;
            margin-bottom: 25px;
            padding: 10px 20px;
            background: #8e44ad;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(142, 68, 173, 0.5);
            transition: background-color 0.3s ease, transform 0.2s ease;
            animation: fadeInLeft 1s ease forwards;
        }
        .back-btn:hover {
            background: #732d91;
            transform: translateY(-3px);
        }

        .search-box {
            max-width: 400px;
            margin: 0 auto 30px auto;
            display: flex;
            justify-content: center;
            animation: fadeIn 1s ease forwards;
        }

        .search-box input[type="text"] {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 1rem;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .search-box input[type="text"]:focus {
            background-color: #3a1f5c;
            color: #fff;
        }

        .search-box button {
            background: #9b59b6;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            color: #fff;
            font-size: 1rem;
            border-radius: 0 30px 30px 0;
            transition: background-color 0.3s ease;
        }

        .search-box button:hover {
            background: #7d3c98;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background:rgba(115, 12, 141, 0.47);
            box-shadow: 0 0 15px rgba(155, 89, 182, 0.3);
            border-radius: 10px;
            overflow: hidden;
            animation: fadeInUp 1s ease forwards;
        }

        thead tr {
            background: #8e44ad;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        th, td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        tbody tr:hover {
            background: #4b3076;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .paid {
            color: #27ae60;
            font-weight: 600;
            animation: pulseGreen 2s infinite;
        }

        .unpaid {
            color: #e74c3c;
            font-weight: 600;
            animation: pulseRed 2s infinite;
        }

        /* Buttons */
        .btn {
            padding: 7px 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .btn-paid {
            background-color: #9b59b6;
            color: #fff;
        }
        .btn-paid:hover {
            background-color: #7d3c98;
            transform: scale(1.05);
        }

        .btn-unpaid {
            background-color: #e67e22;
            color: #fff;
        }
        .btn-unpaid:hover {
            background-color: #ca6c13;
            transform: scale(1.05);
        }

        .btn-delete {
            background-color: #e74c3c;
            color: #fff;
        }
        .btn-delete:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 900px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tbody tr {
                margin-bottom: 20px;
                background: #3b2a61;
                border-radius: 10px;
                padding: 15px;
            }
            tbody tr:hover {
                background: #593d8f;
            }
            td {
                padding: 10px 10px;
                position: relative;
                text-align: left;
                border: none;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            td:before {
                position: absolute;
                top: 12px;
                left: 15px;
                width: 45%;
                white-space: nowrap;
                font-weight: 700;
                color: #b29ddb;
            }
            td:nth-of-type(1):before { content: "#ID"; }
            td:nth-of-type(2):before { content: "Customer"; }
            td:nth-of-type(3):before { content: "Worker"; }
            td:nth-of-type(4):before { content: "Amount"; }
            td:nth-of-type(5):before { content: "Payment Method"; }
            td:nth-of-type(6):before { content: "Payment Status"; }
            td:nth-of-type(7):before { content: "Booking Status"; }
            td:nth-of-type(8):before { content: "Booking Date"; }
            td:nth-of-type(9):before { content: "Actions"; }
            td:last-child {
                padding-bottom: 15px;
            }
            .search-box {
                max-width: 100%;
                padding: 0 10px;
            }
        }

        /* Animations */
        @keyframes fadeInDown {
            0% {opacity: 0; transform: translateY(-20px);}
            100% {opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeInLeft {
            0% {opacity: 0; transform: translateX(-30px);}
            100% {opacity: 1; transform: translateX(0);}
        }
        @keyframes fadeInUp {
            0% {opacity: 0; transform: translateY(30px);}
            100% {opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeIn {
            0% {opacity: 0;}
            100% {opacity: 1;}
        }
        @keyframes pulseGreen {
            0%, 100% {color: #27ae60;}
            50% {color: #2ecc71;}
        }
        @keyframes pulseRed {
            0%, 100% {color: #e74c3c;}
            50% {color: #ff6b6b;}
        }
    </style>
</head>
<body>

    <a href="AdminDashboard1.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <h2>💰 Manage Booking Payments</h2>

    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by Customer/Worker..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Worker</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Booking Status</th>
                <th>Booking Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['booking_id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['worker_name']) ?></td>
                <td>Rs. <?= number_format($row['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td class="<?= $row['payment_status'] == 'Paid' ? 'paid' : 'unpaid' ?>">
                    <?= ucfirst($row['payment_status']) ?>
                </td>
                <td><?= htmlspecialchars($row['booking_status']) ?></td>
                <td><?= htmlspecialchars($row['booking_datetime']) ?></td>
                <td>
                    <?php if ($row['payment_status'] != 'Paid'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <button type="submit" name="mark_paid" class="btn btn-paid" title="Mark as Paid">✓</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <button type="submit" name="mark_unpaid" class="btn btn-unpaid" title="Mark as Unpaid">✗</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this booking record?');">
                        <input type="hidden" name="delete_booking" value="<?= $row['booking_id'] ?>">
                        <button type="submit" class="btn btn-delete" title="Delete Booking"><i class="fas fa-trash"></i></button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                        <button type="submit" name="send_invoice" class="btn btn-paid" title="Send Invoice">📧</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">No payment records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
