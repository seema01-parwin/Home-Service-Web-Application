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

// Check if worker is logged in
if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

// Handle delete notification request
if (isset($_POST['delete_notification_id'])) {
    $delete_id = intval($_POST['delete_notification_id']);

    // Optional: Add check to allow only delete notifications sent by this worker or all (depends on your rules)
    $checkSQL = "SELECT sender_type, sender_id FROM notifications WHERE id = ?";
    $checkStmt = $conn->prepare($checkSQL);
    $checkStmt->bind_param("i", $delete_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $notificationToDelete = $checkResult->fetch_assoc();

    if ($notificationToDelete) {
        // Allow delete only if this worker is sender OR (optional) admin (if needed)
        if ($notificationToDelete['sender_type'] === 'worker' && $notificationToDelete['sender_id'] == $worker_id) {
            $deleteSQL = "DELETE FROM notifications WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSQL);
            $deleteStmt->bind_param("i", $delete_id);
            $deleteStmt->execute();
        }
        // else, you can deny deletion or allow admins to delete
    }
}


// Fetch worker info
$sql = "SELECT * FROM workers WHERE worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

// Handle sending notification (worker sends notification)
if (isset($_POST['send_notification'])) {
    $recipient_type = $conn->real_escape_string($_POST['recipient_type']);
    $message = $conn->real_escape_string($_POST['message']);
    $emoji = $conn->real_escape_string($_POST['emoji']);

    $insertSQL = "INSERT INTO notifications (sender_type, sender_id, recipient_type, recipient_id, message, emoji, sent_at)
                  VALUES ('worker', ?, ?, NULL, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertSQL);

    if ($insertStmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $insertStmt->bind_param("isss", $worker_id, $recipient_type, $message, $emoji);
    $insertStmt->execute();
}


// Fetch notifications for this worker
$notificationQuery = "SELECT * FROM notifications 
    WHERE recipient_type IN ('all_workers', 'worker') 
    AND (recipient_id = ? OR recipient_id IS NULL) 
    ORDER BY sent_at DESC 
    LIMIT 20";

$notificationStmt = $conn->prepare($notificationQuery);
if ($notificationStmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

$notificationStmt->bind_param("i", $worker_id);
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();

// Fetch notifications sent by this worker
$sentNotificationQuery = "SELECT * FROM notifications 
    WHERE sender_type = 'worker' 
    AND sender_id = ? 
    ORDER BY sent_at DESC 
    LIMIT 20";

$sentNotificationStmt = $conn->prepare($sentNotificationQuery);
$sentNotificationStmt->bind_param("i", $worker_id);
$sentNotificationStmt->execute();
$sentNotificationResult = $sentNotificationStmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Worker Notifications</title>
    <link rel="stylesheet" href="WorkerDashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
      /* Container centering */
      .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
      h2 {
        margin-bottom: 20px;
        color: #333;
      }
      form label {
        display: block;
        margin: 10px 0 5px;
        font-weight: 600;
      }
      form select, form textarea, form input[type="text"] {
        width: 100%;
        padding: 8px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;
      }
      form textarea {
        min-height: 80px;
      }
      form button {
        margin-top: 15px;
        padding: 10px 25px;
        background-color:rgb(113, 29, 155);
        border: none;
        border-radius: 5px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }
      form button:hover {
        background-color:rgb(87, 0, 179);
      }
      ul.notifications-list {
        list-style: none;
        padding: 0;
      }
      ul.notifications-list li {
        background: #f7f9fc;
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 10px;
        border-left: 5px solid rgb(108, 21, 180);
        font-size: 1rem;
        color: #444;
      }
      ul.notifications-list li time {
        display: block;
        font-size: 0.85rem;
        color: #666;
        margin-top: 4px;
      }

      .main-container {
    max-width: 1200px;
    margin: auto;
    padding: 30px 20px;
}

.page-title {
    text-align: center;
    margin-bottom: 30px;
    color:rgb(162, 0, 255);
    font-size: 2rem;
}

.notifications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

ul.notifications-list {
    list-style: none;
    padding: 0;
}

ul.notifications-list li {
    background:rgb(246, 217, 255);
    border-left: 4px solid rgb(120, 44, 153);
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    position: relative;
}

ul.notifications-list li time {
    display: block;
    font-size: 0.8rem;
    color: #555;
    margin-top: 5px;
}

ul.notifications-list li button {
    background: none;
    border: none;
    color: red;
    cursor: pointer;
    float: right;
    font-size: 1rem;
}

.emoji-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
    max-height: 120px;
    overflow-y: auto;
}

.emoji-grid label {
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 6px;
    background: #f1f1f1;
    transition: background 0.2s ease;
}

.emoji-grid input[type="radio"] {
    display: none;
}

.emoji-grid input[type="radio"]:checked + label {
    background:rgb(130, 32, 186);
    color: white;
}

button[name="send_notification"] {
    margin-top: 20px;
    padding: 10px 30px;
    background-color:rgb(76, 24, 108);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}

button[name="send_notification"]:hover {
    background-color:rgb(137, 0, 179);
}

/* Animations */
.animate {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s forwards;
}

.delay-1 {
    animation-delay: 0.2s;
}

.delay-2 {
    animation-delay: 0.4s;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .emoji-grid {
        font-size: 1.2rem;
    }

    .notifications-grid {
        grid-template-columns: 1fr;
    }
}

    </style>
</head>
<body>



<header>
    <div class="logo">
        
        <h1>📨 Worker Notifications</h1>
    </div>
    <nav>
        <ul>
            <li><a href="WorkerDashboard1.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Dashboard</a></li>
        </ul>
    </nav>
</header>

<div class="main-container">


    <div class="notifications-grid">
        <!-- Section 1: Received Notifications -->
        <section class="card animate">
            <h3>📥 Received</h3>
            <ul class="notifications-list">
                <?php if ($notificationResult->num_rows > 0): ?>
                    <?php while ($note = $notificationResult->fetch_assoc()): ?>
                        <li>
                            <?= htmlspecialchars($note['emoji']) ?> <?= htmlspecialchars($note['message']) ?>
                            <time><?= $note['sent_at'] ?></time>
                            <?php if ($note['sender_type'] === 'worker' && $note['sender_id'] == $worker_id): ?>
                            <form method="POST" onsubmit="return confirm('Delete this notification?');" style="display:inline;">
                                <input type="hidden" name="delete_notification_id" value="<?= htmlspecialchars($note['id']) ?>">
                                <button type="submit" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No received notifications found.</li>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Section 2: Sent Notifications -->
        <section class="card animate delay-1">
            <h3>📤 Sent</h3>
            <ul class="notifications-list">
                <?php if ($sentNotificationResult->num_rows > 0): ?>
                    <?php while ($note = $sentNotificationResult->fetch_assoc()): ?>
                        <li>
                            To:
                            <?php
                            switch ($note['recipient_type']) {
                                case 'admin': echo "Admin"; break;
                                case 'all_customers': echo "All Customers"; break;
                                case 'all_workers': echo "All Workers"; break;
                                case 'customer': echo "Customer ID " . $note['recipient_id']; break;
                                case 'worker': echo "Worker ID " . $note['recipient_id']; break;
                                default: echo "Unknown";
                            }
                            ?>:
                            <?= htmlspecialchars($note['emoji']) ?> <?= htmlspecialchars($note['message']) ?>
                            <time><?= $note['sent_at'] ?></time>
                            <form method="POST" onsubmit="return confirm('Delete this notification?');" style="display:inline;">
                                <input type="hidden" name="delete_notification_id" value="<?= htmlspecialchars($note['id']) ?>">
                                <button type="submit" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No sent notifications found.</li>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Section 3: Send Notification -->
        <section class="card animate delay-2">
            <h3>✉️ Send Notification</h3>
            <form method="POST">
                <label for="recipient_type">Send To:</label>
                <select name="recipient_type" id="recipient_type" required>
                    <option value="">-- Select Recipient --</option>
                    <option value="admin">Admin</option>
                    <option value="all_customers">All Customers</option>
                    <option value="all_workers">All Workers</option>
                </select>

                <label for="message">Message:</label>
                <textarea name="message" id="message" required></textarea>

                <label>Select Emoji:</label><br>
                <div class="emoji-grid">
                    <?php
                    $emojis = ["😀", "😅", "😂", "😍", "🥳", "👍", "🙏", "🎉", "📢", "💡", "✅", "❌", "🔥", "💬", "🚀", "❤️", "🤝", "📞", "📆", "🛠️"];
                    foreach ($emojis as $emoji) {
                        echo "<label><input type='radio' name='emoji' value='$emoji' required> $emoji</label>";
                    }
                    ?>
                </div>

                <button type="submit" name="send_notification">Send Notification</button>
            </form>
        </section>
    </div>
</div>





</body>
</html>
