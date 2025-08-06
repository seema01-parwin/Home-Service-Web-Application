<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_POST['assign_worker'])) {
    $booking_id = intval($_POST['booking_id']);
    $worker_id = intval($_POST['worker_id']);

    $stmt = $conn->prepare("UPDATE bookings SET worker_id = ?, updated_at = NOW() WHERE booking_id = ?");
    $stmt->bind_param("ii", $worker_id, $booking_id);
    $stmt->execute();
    $stmt->close();
}


// Update booking status
if (isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['booking_status'];

    // Validate status
    $valid_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = ?, updated_at = NOW() WHERE booking_id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete booking
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $conn->query("DELETE FROM bookings WHERE booking_id='$booking_id'");
}


// Update payment status and amount without updated_at
if (isset($_POST['update_payment'])) {
    $booking_id = intval($_POST['booking_id']);
    $payment_status = $_POST['payment_status'];
    $total_amount = floatval($_POST['total_amount']);

    $valid_statuses = ['Unpaid', 'Paid', 'Pending'];
    if (in_array($payment_status, $valid_statuses) && $total_amount >= 0) {
        $stmt = $conn->prepare("UPDATE bookings SET payment_status = ?, total_amount = ? WHERE booking_id = ?");
        
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sdi", $payment_status, $total_amount, $booking_id);
        $stmt->execute();
        $stmt->close();
    }
}



// Fetch all booking data with JOINs
$sql = "SELECT b.*, 
               c.name AS customer_name, 
               w.fullname AS worker_name, 
               s.service_name
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN workers w ON b.worker_id = w.worker_id
        JOIN services s ON b.service_id = s.service_id
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Manage Bookings</title>
   <style>
:root {
    --primary: #6a1b9a;
    --primary-dark: #4a148c;
    --secondary: #ede7f6;
    --text: #333;
    --white: #fff;
    --danger: #e53935;
    --success: #43a047;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: var(--secondary);
    color: var(--text);
    padding: 20px;
}

.go-back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 16px;
    background-color: var(--primary-dark);
    color: var(--white);
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s ease, transform 0.2s;
}

.go-back-btn:hover {
    background-color: var(--primary);
    transform: scale(1.05);
}

.container {
    background: var(--white);
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 0 15px rgba(0,0,0,0.08);
    overflow-x: auto;
    animation: fadeIn 1s ease;
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 30px;
    color: var(--primary-dark);
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

th, td {
    padding: 14px 16px;
    border: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
    font-size: 14px;
}

th {
    background-color: var(--primary);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
}

tr {
    transition: background-color 0.3s ease;
}

tr:nth-child(even) {
    background-color: #f8f5fb;
}

tr:hover {
    background-color: #f1e9fc;
}

select, input[type="number"] {
    padding: 8px 10px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
    transition: border-color 0.3s ease;
}

select:focus, input:focus {
    border-color: var(--primary-dark);
    outline: none;
}

button {
    cursor: pointer;
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 6px;
    border: none;
    margin-top: 5px;
    transition: all 0.3s ease;
}

.btn-update {
    background-color: var(--success);
    color: white;
}

.btn-update:hover {
    background-color: #388e3c;
    transform: scale(1.05);
}

.btn-delete {
    background-color: var(--danger);
    color: white;
}

.btn-delete:hover {
    background-color: #c62828;
    transform: scale(1.05);
}

form {
    display: flex;
    flex-direction: column;
    gap: 6px;
    animation: fadeSlideIn 0.6s ease;
}

/* Animations */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

@keyframes fadeSlideIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
}

@media (max-width: 1024px) {
    .container {
        padding: 15px;
        overflow-x: auto;
    }

    table {
        display: block;
        width: 100%;
        overflow-x: scroll;
        white-space: nowrap;
    }

    th, td {
        font-size: 13px;
        padding: 10px;
    }

    h2 {
        font-size: 24px;
    }
}
</style>

</head>
<body>
    <a href="AdminDashboard1.php" class="go-back-btn">← Go Back</a>

<div class="container">
    <h2>Manage Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Worker</th>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Address</th>
                <th>Note</th>
                <th>Created At</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): 
                while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?>
               <td>
    <?= htmlspecialchars($row['worker_name'] ?? 'Not Assigned') ?><br>

    <form method="POST">
        <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
        <select name="worker_id" required>
            <option value="">Assign Worker</option>
            <?php
            $workers = $conn->query("SELECT worker_id, fullname FROM workers");
            while ($w = $workers->fetch_assoc()):
            ?>
                <option value="<?= $w['worker_id'] ?>" <?= $row['worker_id'] == $w['worker_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($w['fullname']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="assign_worker" class="btn-update">Assign</button>
    </form>
</td>

                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td><?= $row['date'] . " " . $row['time'] ?></td>
                    <td><strong><?= $row['booking_status'] ?></strong></td>
                    <td colspan="2">
    <form method="POST" style="display: flex; flex-direction: column; gap: 5px;">
        <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">

        <!-- Payment Status Dropdown -->
        <select name="payment_status" required>
            <option value="">Payment Status</option>
            <option value="Unpaid" <?= $row['payment_status'] == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
            <option value="Paid" <?= $row['payment_status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
            <option value="Pending" <?= $row['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
        </select>

        <!-- Amount Input -->
        <input type="number" name="total_amount" value="<?= $row['total_amount'] ?>" step="0.01" min="0" required placeholder="Enter amount">

        <button type="submit" name="update_payment" class="btn-update">Update</button>
    </form>
</td>

                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['service_address']) ?><br>
                        <?= htmlspecialchars($row['city']) ?>, <?= $row['postal_code'] ?>
                    </td>
                    <td><?= htmlspecialchars($row['customer_note']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <select name="booking_status" required>
                                <option value="">Select</option>
                                <option value="Pending" <?= $row['booking_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Confirmed" <?= $row['booking_status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="Completed" <?= $row['booking_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= $row['booking_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-update">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure to delete this booking?');">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <button type="submit" name="delete_booking" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="14">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
