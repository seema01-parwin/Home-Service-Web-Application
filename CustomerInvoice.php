<?php 
session_start();
$conn = new mysqli("localhost", "root", "", "home_service_db");

if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin.php");
    exit();
}

$customer_id = intval($_SESSION['customer_id']);

// Build dynamic filter conditions
$where = "WHERE p.customer_id = ?";
$params = [$customer_id];
$types = "i";

if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $where .= " AND DATE(p.payment_date) BETWEEN ? AND ?";
    $params[] = $_GET['from'];
    $params[] = $_GET['to'];
    $types .= "ss";
}

if (!empty($_GET['status'])) {
    $where .= " AND p.payment_status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

$sql = "SELECT p.*, b.booking_datetime, b.booking_status, w.fullname AS worker_name
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN workers w ON p.worker_id = w.worker_id
        $where
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Invoices - Customer Dashboard</title>

<style>
  :root {
    --primary: #6a1b9a;
    --secondary: #8e24aa;
    --bg: #f3e5f5;
    --white: #fff;
    --text: #333;
    --border: #ddd;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0; padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--bg);
    color: var(--text);
    animation: fadeIn 0.5s ease-in;
  }

  @keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
  }

  header {
    position: sticky;
    top: 0; left: 0; right: 0;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    color: var(--white);
    padding: 18px 30px;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 1px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    z-index: 10;
    animation: slideDown 0.6s ease forwards;
  }

  @keyframes slideDown {
    from {transform: translateY(-100%);}
    to {transform: translateY(0);}
  }

  header a.dashboard-btn {
    background: #6a1b9a;
    color: white;
    text-decoration: none;
    font-weight: 400;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
  }

  header a.dashboard-btn:hover {
    background: var(--secondary);
    color: var(--white);
    transform: scale(1.05);
  }

  main.container {
    max-width: 900px;
    margin: 30px auto 60px;
    background: var(--white);
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    padding: 25px 30px;
    animation: slideUp 0.5s ease forwards;
  }

  @keyframes slideUp {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
  }

  form.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
    align-items: center;
    justify-content: flex-start;
  }

  form.filter-form label {
    font-weight: 600;
    margin-right: 5px;
    color: var(--primary);
  }

  form.filter-form input[type="date"],
  form.filter-form select {
    padding: 7px 10px;
    border-radius: 6px;
    border: 1.8px solid var(--border);
    font-size: 14px;
    transition: border-color 0.3s ease;
  }

  form.filter-form input[type="date"]:focus,
  form.filter-form select:focus {
    outline: none;
    border-color: var(--secondary);
  }

  form.filter-form button,
  form.export-form button {
    cursor: pointer;
    padding: 9px 18px;
    background: var(--primary);
    border: none;
    border-radius: 8px;
    color: var(--white);
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 4px 9px rgba(106,27,154,0.4);
    transition: background 0.3s ease, transform 0.25s ease;
  }

  form.filter-form button:hover,
  form.export-form button:hover {
    background: var(--secondary);
    transform: scale(1.05);
  }

  form.filter-form a.reset-btn {
    font-weight: 600;
    color: var(--secondary);
    text-decoration: underline;
    cursor: pointer;
    margin-left: 12px;
    font-size: 14px;
    align-self: center;
  }

  /* Invoice cards */

  .invoice {
    border-left: 6px solid var(--primary);
    padding: 20px 25px 25px 25px;
    margin-bottom: 30px;
    background:rgb(244, 204, 255);
    border-radius: 12px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.07);
    transition: box-shadow 0.3s ease;
  }

  .invoice:hover {
    box-shadow: 0 8px 25px rgba(106,27,154,0.15);
  }

  .invoice h2 {
    margin-top: 0;
    margin-bottom: 12px;
    color: var(--primary);
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 6px;
  }

  .invoice-details p {
    margin: 8px 0;
    font-size: 15px;
    color: #444;
  }

  .invoice-details p strong {
    display: inline-block;
    width: 160px;
    color: #222;
  }

  .amount {
    font-size: 18px;
    font-weight: 700;
    color: var(--secondary);
    margin-top: 15px;
  }

  .footer-note {
    font-style: italic;
    color: #777;
    margin-top: 20px;
  }

  /* Responsive */
  @media (max-width: 700px) {
    form.filter-form {
      flex-direction: column;
      align-items: stretch;
    }

    form.filter-form label,
    form.filter-form input[type="date"],
    form.filter-form select,
    form.filter-form button,
    form.export-form button {
      width: 100%;
    }

    form.filter-form button,
    form.export-form button {
      margin-top: 10px;
    }

    .invoice-details p strong {
      width: 120px;
    }
  }

  .back-dashboard {
    
    display: inline-block;
    padding: 10px 20px;
    background: #9c27b0;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background 0.3s, transform 0.3s;
  }

  .back-dashboard:hover {
    background: #7b1fa2;
    transform: scale(1.05);
  }
</style>

</head>
<body>

<header>
  <div>My Invoices</div>
</header>


<main class="container">

  <!-- Filter Form -->
  <form method="GET" class="filter-form" aria-label="Filter invoices">
    <label for="from">From:</label>
    <input type="date" id="from" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" />

    <label for="to">To:</label>
    <input type="date" id="to" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" />

    <label for="status">Payment Status:</label>
    <select id="status" name="status">
      <option value="" <?= (empty($_GET['status'])) ? 'selected' : '' ?>>All</option>
      <option value="Paid" <?= (($_GET['status'] ?? '') === 'Paid') ? 'selected' : '' ?>>Paid</option>
      <option value="Pending" <?= (($_GET['status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
      <option value="Failed" <?= (($_GET['status'] ?? '') === 'Failed') ? 'selected' : '' ?>>Failed</option>
    </select>

    <button type="submit">Filter</button>

    <?php if (!empty($_GET['from']) || !empty($_GET['to']) || !empty($_GET['status'])): ?>
      <a href="CustomerInvoices.php" class="reset-btn" title="Reset filters">Reset</a>
    <?php endif; ?>
  </form>

  <!-- Export Form -->
  <form method="POST" action="export_invoices_excel.php" class="export-form" aria-label="Export invoices to Excel">
    <!-- Pass current filters as hidden inputs -->
    <input type="hidden" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
    <input type="hidden" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
    <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
    <button type="submit">Export to Excel</button>
  </form>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="invoice" tabindex="0" aria-label="Invoice #<?= $row['payment_id'] ?>">
        <h2>Invoice #<?= $row['payment_id'] ?></h2>
        <div class="invoice-details">
          <p><strong>Booking Date & Time:</strong> <?= date('d M Y, H:i', strtotime($row['booking_datetime'])) ?></p>
          <p><strong>Worker:</strong> <?= htmlspecialchars($row['worker_name']) ?></p>
          <p><strong>Booking Status:</strong> <?= htmlspecialchars($row['booking_status']) ?></p>
          <p><strong>Payment Date:</strong> <?= date('d M Y', strtotime($row['payment_date'])) ?></p>
          <p><strong>Payment Status:</strong> <span style="color: <?= ($row['payment_status'] == 'Paid') ? 'green' : (($row['payment_status'] == 'Pending') ? 'orange' : 'red') ?>; font-weight:700;">
            <?= htmlspecialchars($row['payment_status']) ?></span></p>
          <p class="amount"><strong>Amount Paid:</strong> LKR <?= number_format($row['amount'], 2) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No invoices found for the selected filters.</p>
  <?php endif; ?>

</main>

<div style="text-align:center;">
  <a href="CustomerDashboard1.php" class="back-dashboard"><i class="fas fa-arrow-left"></i> Dashboard</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
