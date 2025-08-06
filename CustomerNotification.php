<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];

// Send notification
if (isset($_POST['send_notification'])) {
    $recipient_type = $conn->real_escape_string($_POST['recipient_type']);
    $message = $conn->real_escape_string($_POST['message']);
    $emoji = $conn->real_escape_string($_POST['emoji']);

    $sql = "INSERT INTO notifications (sender_type, sender_id, recipient_type, recipient_id, message, emoji, sent_at)
            VALUES ('customer', ?, ?, NULL, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $customer_id, $recipient_type, $message, $emoji);
    $stmt->execute();
}

// Delete notification (only own sent ones)
if (isset($_POST['delete_notification_id'])) {
    $delete_id = intval($_POST['delete_notification_id']);
    $sql = "DELETE FROM notifications WHERE id = ? AND sender_type = 'customer' AND sender_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $delete_id, $customer_id);
    $stmt->execute();
}

// Fetch received notifications
$receivedSQL = "SELECT * FROM notifications 
                WHERE recipient_type IN ('customer', 'all_customers') 
                AND (recipient_id = ? OR recipient_id IS NULL)
                ORDER BY sent_at DESC";
$receivedStmt = $conn->prepare($receivedSQL);
$receivedStmt->bind_param("i", $customer_id);
$receivedStmt->execute();
$receivedResult = $receivedStmt->get_result();

// Fetch sent notifications
$sentSQL = "SELECT * FROM notifications 
            WHERE sender_type = 'customer' AND sender_id = ?
            ORDER BY sent_at DESC";
$sentStmt = $conn->prepare($sentSQL);
$sentStmt->bind_param("i", $customer_id);
$sentStmt->execute();
$sentResult = $sentStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        
    :root {
        --primary-color: #6a1b9a; /* Deep purple */
        --secondary-color: #8e24aa;
        --background-color: #f3e5f5;
        --text-color: #333;
        --danger-color: #e53935;
        --light-color: #ffffff;
        --hover-purple: #7b1fa2;
        --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

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
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        animation: slideDown 0.6s ease-in-out;
        border-radius: 12px;
    }

    @keyframes slideDown {
        from { transform: translateY(-100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    header h1 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    header nav a {
        color: white;
        margin-left: 20px;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }

    header nav a:hover {
        color:rgb(200, 136, 255);
    }

    .container {
        max-width: 960px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        animation: fadeInUp 1s ease-in-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        margin-bottom: 12px;
        color: var(--primary-color);
    }

    ul {
        list-style: none;
        padding: 0;
        margin-top: 10px;
    }

    li {
        background: var(--background-color);
        margin: 10px 0;
        padding: 14px 16px;
        border-left: 6px solid var(--primary-color);
        border-radius: 8px;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    li:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
    }

    time {
        font-size: 0.85rem;
        color: #666;
        display: block;
        margin-top: 5px;
    }

    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: border 0.3s ease;
    }

    form textarea:focus,
    form select:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    form button {
        background-color: var(--primary-color);
        color: white;
        padding: 10px 22px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    form button:hover {
        background-color: var(--hover-purple);
        transform: scale(1.02);
    }

    .emoji-option {
        margin: 5px 10px 5px 0;
        display: inline-block;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .emoji-option:hover {
        transform: scale(1.2);
    }

    .delete-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        color: var(--danger-color);
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .delete-btn:hover {
        transform: scale(1.2);
    }

    @media (max-width: 600px) {
        header {
            flex-direction: column;
            align-items: flex-start;
        }

        header nav {
            margin-top: 10px;
        }

        form button {
            width: 100%;
        }
    }

    </style>
</head>
<body>

<header>
    <h1>Customer Notifications</h1>
    <nav>
        <a href="CustomerDashboard1.php">Dashboard</a>
        <a href="CustomerLogout1.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>📩 Notifications for You</h2>
    <ul>
        <?php if ($receivedResult->num_rows > 0): ?>
            <?php while ($row = $receivedResult->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['message']) ?>
                    <time><?= $row['sent_at'] ?></time>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No received notifications.</li>
        <?php endif; ?>
    </ul>

    <h2>📤 Your Sent Notifications</h2>
    <ul>
        <?php if ($sentResult->num_rows > 0): ?>
            <?php while ($row = $sentResult->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['message']) ?>
                    <time><?= $row['sent_at'] ?></time>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                        <input type="hidden" name="delete_notification_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No sent notifications.</li>
        <?php endif; ?>
    </ul>

    <h2>➕ Send a New Notification</h2>
    <form method="POST">
        <label>Send To:</label>
        <select name="recipient_type" required>
            <option value="">-- Select --</option>
            <option value="admin">Admin</option>
            <option value="all_workers">All Workers</option>
        </select>

        <label>Message:</label>
        <textarea name="message" required></textarea>

        <label>Select Emoji:</label><br>
        <?php
        $emojis = ["😀", "😅", "😂", "😍", "🥳", "👍", "🙏", "🎉", "📢", "💡", "✅", "❌", "🔥", "💬", "🚀", "❤️", "🤝", "📞", "📆", "🛠️"];
        foreach ($emojis as $e) {
            echo "<label class='emoji-option'><input type='radio' name='emoji' value='$e' required> $e</label>";
        }
        ?>
        <br><br>
        <button type="submit" name="send_notification">Send</button>
    </form>
</div>
</body>
</html>
