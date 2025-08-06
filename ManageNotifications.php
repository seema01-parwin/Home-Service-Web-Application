<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Handle sending ---
if (isset($_POST['send_notification'])) {
    [$type, $id] = explode('-', $_POST['recipient']);
    $recipient_type = $type;
    $recipient_id = ($type === 'all_customers' || $type === 'all_workers') ? 'NULL' : (int)$id;
    $message = $conn->real_escape_string($_POST['message']);
    $emoji = $conn->real_escape_string($_POST['emoji']);

    $conn->query("INSERT INTO notifications (recipient_type, recipient_id, message, emoji)
                  VALUES ('$recipient_type', $recipient_id, '$message', '$emoji')");
}

// --- Handle deletion ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM notifications WHERE id = $id");
}

// --- Handle editing ---
if (isset($_POST['edit_notification'])) {
    $id = (int)$_POST['id'];
    $message = $conn->real_escape_string($_POST['message']);
    $emoji = $conn->real_escape_string($_POST['emoji']);
    $conn->query("UPDATE notifications SET message = '$message', emoji = '$emoji' WHERE id = $id");
}

// --- Load users ---
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name ASC");
$workers = $conn->query("SELECT worker_id, fullname FROM workers ORDER BY fullname ASC");
$notifications = $conn->query("SELECT * FROM notifications ORDER BY sent_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Notification</title>
  <style>
    /* Reset & base */
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fbecff;
      color: #333;
      margin: 0; padding: 0 1rem;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      animation: fadeIn 1s ease forwards;
      padding: 50px;
    }
    h2 {
      color:rgb(67, 44, 80);
      margin-top: 2rem;
      margin-bottom: 1rem;
      text-align: center;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      font-size: 1.8rem;
    }

    /* Container */
    .container {
      max-width: 900px;
      width: 100%;
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      margin-bottom: 3rem;
      animation: slideDownFade 0.8s ease forwards;
    }

    /* Form */
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem 2rem;
      align-items: center;
      justify-content: space-between;
    }
    label {
      flex: 0 0 100%;
      font-weight: 600;
      margin-bottom: 0.3rem;
      color:rgb(139, 61, 162);
    }
    select, textarea, input[type="text"] {
      padding: 0.5rem 0.75rem;
      border: 1.8px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
      width: 100%;
      max-width: 100%;
    }
    select:focus, textarea:focus, input[type="text"]:focus {
      border-color:rgb(184, 86, 237);
      outline: none;
      box-shadow: 0 0 8px rgba(182, 39, 211, 0.3);
    }
    textarea {
      resize: vertical;
      min-height: 80px;
      max-width: 100%;
      font-family: inherit;
    }

    /* Recipient select width */
    select[name="recipient"] {
      max-width: 400px;
    }

    /* Emoji radio buttons */
    label > input[type="radio"] {
      margin-right: 6px;
      transform: scale(1.3);
      vertical-align: middle;
      cursor: pointer;
    }
    label {
      cursor: pointer;
      user-select: none;
      font-size: 1.3rem;
      margin-right: 10px;
      display: inline-flex;
      align-items: center;
      transition: color 0.25s ease;
    }
    label:hover {
      color:rgb(152, 52, 219);
    }
    input[type="radio"]:checked + span {
      color:rgb(139, 41, 185);
    }

    /* Buttons */
    button[type="submit"] {
      cursor: pointer;
      background:rgb(166, 52, 219);
      color: white;
      font-weight: 700;
      border: none;
      padding: 0.7rem 1.8rem;
      border-radius: 6px;
      box-shadow: 0 5px 12px rgba(147, 52, 219, 0.5);
      font-size: 1.1rem;
      transition: background-color 0.3s ease, transform 0.2s ease;
      flex-shrink: 0;
      margin-top: 0.5rem;
      user-select: none;
    }
    button[type="submit"]:hover {
      background-color:rgb(123, 41, 185);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(115, 41, 185, 0.7);
    }

    /* Table */
    table {
      border-collapse: collapse;
      width: 100%;
      max-width: 100%;
      background: white;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      border-radius: 10px;
      overflow: hidden;
      animation: slideUpFade 0.8s ease forwards;
    }
    th, td {
      padding: 0.9rem 1rem;
      text-align: left;
      border-bottom: 1px solid #ecf0f1;
      font-size: 0.95rem;
    }
    th {
      background-color:rgb(141, 52, 219);
      color: white;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }
    tr:hover {
      background-color: #f1faff;
    }
    td > form {
      display: flex;
      gap: 0.5rem;
      align-items: center;
      flex-wrap: wrap;
    }
    td > form input[type="text"] {
      width: 180px;
      padding: 0.3rem 0.5rem;
      font-size: 0.9rem;
    }
    td > form select {
      width: 70px;
      padding: 0.3rem;
      font-size: 1.1rem;
    }
    td > form button {
      padding: 0.4rem 0.8rem;
      font-size: 0.9rem;
      background-color: #27ae60;
      box-shadow: none;
      transition: background-color 0.3s ease;
    }
    td > form button:hover {
      background-color: #1e8449;
    }

    /* Delete link */
    a {
      color: #e74c3c;
      font-weight: 600;
      text-decoration: none;
      margin-left: 0.5rem;
      transition: color 0.3s ease;
      cursor: pointer;
    }
    a:hover {
      color: #c0392b;
    }

    /* Responsive */
    @media (max-width: 720px) {
      .container {
        padding: 1.5rem 1.5rem;
      }
      form {
        flex-direction: column;
        gap: 1rem;
      }
      td > form {
        flex-direction: column;
        gap: 0.3rem;
      }
      td > form input[type="text"], td > form select {
        width: 100%;
      }
      button[type="submit"] {
        width: 100%;
      }
    }

    /* Animations */
    @keyframes fadeIn {
      0% {opacity: 0;}
      100% {opacity: 1;}
    }
    @keyframes slideDownFade {
      0% {
        opacity: 0;
        transform: translateY(-20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes slideUpFade {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

      /* Back to Dashboard Button Style */
  .back-btn {
    display: inline-block;
    margin: 20px 0;
    padding: 10px 18px;
    background-color:rgb(152, 44, 229);
    color: white;
    text-decoration: none;
    font-weight: 600;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 6px rgba(192, 44, 229, 0.3);
  }
  .back-btn:hover {
    background-color:rgb(154, 26, 193);
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(112, 26, 193, 0.5);
  }
  </style>
</head>
<body>

<div class="container">

<h2>Send Notification</h2>
<form method="POST" novalidate>
    <label for="recipient">Recipient:</label>
    <select name="recipient" id="recipient" required>
        <option value="">-- Select Recipient --</option>
        <option value="all_customers-0">📢 All Customers</option>
        <option value="all_workers-0">📢 All Workers</option>
        <optgroup label="Customers">
            <?php while ($c = $customers->fetch_assoc()): ?>
                <option value="customer-<?= $c['customer_id'] ?>">👤 <?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </optgroup>
        <optgroup label="Workers">
            <?php while ($w = $workers->fetch_assoc()): ?>
                <option value="worker-<?= $w['worker_id'] ?>">🧑‍🔧 <?= htmlspecialchars($w['fullname']) ?></option>
            <?php endwhile; ?>
        </optgroup>
    </select>

    <label for="message">Message:</label>
    <textarea name="message" id="message" required rows="4" placeholder="Type your notification message here..."></textarea>

    <label>Select Emoji:</label>
    <div style="max-width: 600px; margin-bottom: 1rem;">
    <?php
    $emojis = ["😀", "😅", "😂", "😍", "🥳", "👍", "🙏", "🎉", "📢", "💡", "✅", "❌", "🔥", "💬", "🚀", "❤️", "🤝", "📞", "📆", "🛠️"];
    foreach ($emojis as $emoji) {
        echo "<label><input type='radio' name='emoji' value='$emoji' required><span>$emoji</span></label> ";
    }
    ?>
    </div>

    <button type="submit" name="send_notification">📨 Send Notification</button>
</form>
</div>

<hr style="width: 90%; max-width: 900px; margin: 2rem auto; border-color: #ddd;" />

<h2>Notification History</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Recipient</th>
        <th>Message</th>
        <th>Emoji</th>
        <th>Sent At</th>
        <th style="min-width: 250px;">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($n = $notifications->fetch_assoc()): ?>
        <tr>
            <td><?= $n['id'] ?></td>
            <td>
                <?php
                 switch ($n['recipient_type']) {
                    case 'customer':
                        $res = $conn->query("SELECT name FROM customers WHERE customer_id = " . (int)$n['recipient_id']);
                        $row = $res ? $res->fetch_assoc() : null;
                        echo $row ? htmlspecialchars($row['name']) . " (Customer)" : "Unknown Customer";
                        break;
                    case 'worker':
                        $res = $conn->query("SELECT fullname FROM workers WHERE worker_id = " . (int)$n['recipient_id']);
                        $row = $res ? $res->fetch_assoc() : null;
                        echo $row ? htmlspecialchars($row['fullname']) . " (Worker)" : "Unknown Worker";
                        break;
                    case 'admin':
                        echo "👑 Admin";
                        break;
                    case 'all_customers':
                        echo "📢 All Customers";
                        break;
                    case 'all_workers':
                        echo "📢 All Workers";
                        break;
                    case 'all_admins':
                        echo "📢 All Admins";
                        break;
                    default:
                        echo "🔘 Unknown";
                }
                ?>
            </td>
            <td><?= htmlspecialchars($n['message']) ?></td>
            <td style="font-size: 1.5rem; text-align: center;"><?= $n['emoji'] ?></td>
            <td><?= $n['sent_at'] ?></td>
            <td>
                <!-- Edit -->
                <form method="POST" style="display:inline-flex; align-items:center; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                    <input type="text" name="message" value="<?= htmlspecialchars($n['message']) ?>" required
                        aria-label="Edit message for notification ID <?= $n['id'] ?>">
                    <select name="emoji" required aria-label="Select emoji for notification ID <?= $n['id'] ?>">
                        <?php foreach ($emojis as $e): ?>
                            <option value="<?= $e ?>" <?= $n['emoji'] === $e ? 'selected' : '' ?>><?= $e ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="edit_notification" title="Update notification <?= $n['id'] ?>">✏️ Update</button>
                </form>
                <!-- Delete -->
                <a href="?delete=<?= $n['id'] ?>" onclick="return confirm('Delete this notification?')"
                   aria-label="Delete notification ID <?= $n['id'] ?>">🗑️</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>


<a href="AdminDashboard1.php" class="back-btn">← Back to Dashboard</a>


</body>
</html>
