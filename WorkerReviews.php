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
} // Your DB connection

// Assuming worker is logged in and worker_id is stored in session
if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit;
}

$worker_id = $_SESSION['worker_id'];
$message = "";

// Handle review delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
    $delete_review_id = intval($_POST['delete_review_id']);

    // Verify the review belongs to this worker
    $stmt_check = $conn->prepare("SELECT review_id FROM reviews WHERE review_id = ? AND worker_id = ?");
    $stmt_check->bind_param("ii", $delete_review_id, $worker_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Delete review replies first (to maintain DB integrity)
        $stmt_del_replies = $conn->prepare("DELETE FROM review_replies WHERE review_id = ?");
        $stmt_del_replies->bind_param("i", $delete_review_id);
        $stmt_del_replies->execute();
        $stmt_del_replies->close();

        // Delete review
        $stmt_del_review = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        if ($stmt_del_review) {
            $stmt_del_review->bind_param("i", $delete_review_id);
            if ($stmt_del_review->execute()) {
                $message = "Review deleted successfully.";
            } else {
                $message = "Failed to delete the review.";
            }
            $stmt_del_review->close();
        }
    } else {
        $message = "You are not authorized to delete this review.";
    }
    $stmt_check->close();
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['reply_text'])) {
    $review_id = intval($_POST['review_id']);
    $reply_text = trim($_POST['reply_text']);

    if ($reply_text !== "") {
        $stmt = $conn->prepare("INSERT INTO review_replies (review_id, worker_id, reply_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $review_id, $worker_id, $reply_text);
        if ($stmt->execute()) {
            $message = "Reply sent successfully!";
        } else {
            $message = "Failed to send reply.";
        }
        $stmt->close();
    } else {
        $message = "Reply cannot be empty.";
    }
}

// Fetch reviews for this worker along with customer info and replies
$query = "
SELECT r.review_id, r.rating, r.review_text, r.review_date,
       c.name AS customer_name,
       rr.reply_id, rr.reply_text, rr.reply_date
FROM reviews r
JOIN customers c ON r.customer_id = c.customer_id
LEFT JOIN review_replies rr ON rr.review_id = r.review_id AND rr.worker_id = ?
WHERE r.worker_id = ?
ORDER BY r.review_date DESC, rr.reply_date ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $worker_id, $worker_id);
$stmt->execute();
$result = $stmt->get_result();

// Organize reviews and replies
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $rid = $row['review_id'];
    if (!isset($reviews[$rid])) {
        $reviews[$rid] = [
            'review_id' => $rid,
            'rating' => $row['rating'],
            'review_text' => $row['review_text'],
            'review_date' => $row['review_date'],
            'customer_name' => $row['customer_name'],
            'replies' => []
        ];
    }
    if ($row['reply_id']) {
        $reviews[$rid]['replies'][] = [
            'reply_id' => $row['reply_id'],
            'reply_text' => $row['reply_text'],
            'reply_date' => $row['reply_date']
        ];
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customer Reviews - Worker Reply</title>
<style>
  /* General Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to right,rgb(253, 228, 253),rgb(240, 201, 255));
    padding-top: 80px;
  }

  /* Header and Navigation */
  header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
    color: #fff;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    animation: slideDown 0.5s ease forwards;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  @keyframes slideDown {
    from {
      transform: translateY(-100%);
    }
    to {
      transform: translateY(0);
    }
  }

  header h1 {
    font-size: 1.5rem;
    color: #fff;
  }

  nav a {
    margin-left: 25px;
    text-decoration: none;
    color: #fff;
    font-weight: 500;
    position: relative;
    transition: color 0.3s ease;
  }

  nav a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: #fff;
    transition: width 0.3s ease;
  }

  nav a:hover {
    color:rgb(214, 176, 231);
  }

  nav a:hover::after {
    width: 100%;
  }

  /* Message */
  .message {
    max-width: 900px;
    margin: 20px auto;
    padding: 15px;
    background: #d4edda;
    color: #155724;
    border-radius: 6px;
    font-size: 1rem;
  }

  /* Review Section */
  .container {
    max-width: 900px;
    margin: auto;
    padding: 20px;
  }

  h2 {
    margin-top: 40px;
    color: #333;
  }

  .review-card {
    background: #fff;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }

  .review-card:hover {
    transform: translateY(-3px);
  }

  .review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
    margin-bottom: 8px;
  }

  .customer-name {
    font-style: italic;
    color: #444;
  }

  .rating-stars {
    color: #f1c40f;
    font-size: 1.2rem;
  }

  .review-text {
    margin: 12px 0;
    color: #333;
  }

  .review-date {
    font-size: 0.85rem;
    color: #888;
  }

  .replies {
    margin-top: 15px;
    padding-left: 20px;
    border-left: 4px solid #ccc;
  }

  .reply {
    background: #f5f5f5;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 10px;
    font-size: 0.95rem;
  }

  .reply-date {
    font-size: 0.8rem;
    color: #666;
    margin-top: 5px;
  }

  form.reply-form textarea {
    width: 100%;
    min-height: 60px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 10px;
    resize: vertical;
  }

  form.reply-form button {
    margin-top: 10px;
    padding: 8px 16px;
    border: none;
    background:rgb(126, 28, 191);
    color: white;
    border-radius: 5px;
    cursor: pointer;
  }

  form.reply-form button:hover {
    background:rgb(144, 30, 196);
  }

  .emoji-toggle-btn {
    background: #eee;
    border: 1px solid #bbb;
    border-radius: 4px;
    cursor: pointer;
    padding: 5px 10px;
    margin-top: 10px;
    margin-right: 10px;
  }

  .emoji-picker {
    display: none;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 300px;
  }

  .emoji-picker span {
    font-size: 1.5rem;
    cursor: pointer;
    margin: 3px;
  }

  /* Table Styling */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
  }

  th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }

  th {
    background:rgb(149, 28, 229);
    color: #fff;
  }

  td {
    color: #333;
  }

  table button {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
  }

  table button:hover {
    background: #c82333;
  }

  /* Responsive */
  @media screen and (max-width: 768px) {
    header {
      flex-direction: column;
      align-items: flex-start;
    }

    nav {
      margin-top: 10px;
    }

    nav a {
      display: block;
      margin: 10px 0;
    }

    .review-header {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

</head>
<body>

<header>
<h1>Customer Reviews</h1>
  <nav>
    <a href="WorkerDashboard1.php">Dashboard</a>
    
    <a href="WorkerLogout.php">Logout</a>
  </nav>
</header>

<div class="container">



<?php if ($message): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (empty($reviews)): ?>
  <p>No reviews found.</p>
<?php else: ?>
  <?php foreach ($reviews as $review): ?>
    <div class="review-card">
      <div class="review-header">
        <span class="customer-name"><?= htmlspecialchars($review['customer_name']) ?></span> 
        - <span class="rating-stars"><?= str_repeat('★', floor($review['rating'])) . str_repeat('☆', 5 - floor($review['rating'])) ?></span>
      </div>
      <div class="review-text"><?= nl2br(htmlspecialchars($review['review_text'])) ?></div>
      <div class="review-date"><?= date("Y-m-d H:i", strtotime($review['review_date'])) ?></div>

      <div class="replies">
        <?php if ($review['replies']): ?>
          <?php foreach ($review['replies'] as $reply): ?>
            <div class="reply">
              <?= nl2br(htmlspecialchars($reply['reply_text'])) ?>
              <div class="reply-date"><?= date("Y-m-d H:i", strtotime($reply['reply_date'])) ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <form method="POST" class="reply-form">
        <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
        <textarea name="reply_text" placeholder="Write your reply here..." required></textarea>
        <button type="button" class="emoji-toggle-btn">😀 Emojis</button>
        <div class="emoji-picker">
          <?php
          $emojis = ['😀','😁','😂','🤣','😊','😍','😘','😎','😢','😡','👍','👎','🙏','🔥','🌟'];
          foreach ($emojis as $emoji) {
              echo "<span>{$emoji}</span>";
          }
          ?>
        </div>
        <button type="submit">Send Reply</button>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<h2>Review History</h2>

<?php if (empty($reviews)): ?>
  <p>No review history found.</p>
<?php else: ?>
  <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
    <thead>
      <tr style="background: #007bff; color: white;">
        <th>Customer</th>
        <th>Rating</th>
        <th>Review</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($reviews as $review): ?>
      <tr>
        <td><?= htmlspecialchars($review['customer_name']) ?></td>
        <td><?= str_repeat('★', floor($review['rating'])) . str_repeat('☆', 5 - floor($review['rating'])) ?></td>
        <td><?= nl2br(htmlspecialchars($review['review_text'])) ?></td>
        <td><?= date("Y-m-d H:i", strtotime($review['review_date'])) ?></td>
        <td>
          <form method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');" style="display:inline;">
            <input type="hidden" name="delete_review_id" value="<?= $review['review_id'] ?>">
            <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

    </div>

<script>
// Emoji picker toggle and insertion
document.querySelectorAll('.emoji-toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const picker = btn.nextElementSibling;
    picker.style.display = (picker.style.display === 'block') ? 'none' : 'block';
  });
});

document.querySelectorAll('.emoji-picker').forEach(picker => {
  picker.querySelectorAll('span').forEach(emoji => {
    emoji.addEventListener('click', () => {
      const textarea = picker.previousElementSibling.previousElementSibling; // textarea before emoji btn
      textarea.value += emoji.textContent;
      textarea.focus();
    });
  });
});


  document.getElementById("menuToggle").addEventListener("click", function () {
    const nav = document.querySelector(".nav-links");
    nav.classList.toggle("show");
  });

</script>

</body>
</html>
