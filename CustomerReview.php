<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check customer login
if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];

$message = "";

// Handle delete review request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_review'], $_POST['delete_review_id'])) {
    $delete_review_id = intval($_POST['delete_review_id']);

    // Make sure the review belongs to the logged-in customer for security
    $check_stmt = $conn->prepare("SELECT review_id FROM reviews WHERE review_id = ? AND customer_id = ?");
    $check_stmt->bind_param("ii", $delete_review_id, $customer_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Review belongs to the customer - safe to delete
        $delete_stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $delete_stmt->bind_param("i", $delete_review_id);
        if ($delete_stmt->execute()) {
            $message = "Review deleted successfully.";
        } else {
            $message = "Failed to delete review.";
        }
        $delete_stmt->close();
    } else {
        $message = "You are not authorized to delete this review.";
    }
    $check_stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $worker_id = intval($_POST['worker_id']);
    $rating = floatval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($worker_id && $rating >= 0 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (worker_id, customer_id, rating, review_text, review_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iids", $worker_id, $customer_id, $rating, $review_text);
        if ($stmt->execute()) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Failed to submit review.";
        }
        $stmt->close();
    } else {
        $message = "Please select a worker and a valid rating.";
    }
}

// Fetch workers for dropdown
$workers = $conn->query("SELECT worker_id, fullname FROM workers WHERE status = 'Approved' ORDER BY fullname");

// Fetch all reviews by this customer along with worker replies (if any)
$review_sql = "
  SELECT r.review_id, r.worker_id, r.rating, r.review_text, r.review_date, w.fullname,
         rr.reply_text, rr.reply_date
  FROM reviews r
  JOIN workers w ON r.worker_id = w.worker_id
  LEFT JOIN review_replies rr ON r.review_id = rr.review_id
  WHERE r.customer_id = ?
  ORDER BY r.review_date DESC
";

$stmt = $conn->prepare($review_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customer Review</title>
<style>
  body {
    font-family: Arial, sans-serif;
   background: #fbecff;
    padding: 20px;
  }
  .review-container {
    background: white;
    max-width: 500px;
    margin: 0 auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
  }
  h2 {
    margin-bottom: 20px;
    text-align: center;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }
  select, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 6px;
    border-radius: 5px;
    border: 1px solid #ccc;
    resize: vertical;
    font-size: 1rem;
  }
  textarea {
    min-height: 100px;
  }
  .stars {
    margin-top: 8px;
    font-size: 2rem;
    cursor: pointer;
    user-select: none;
  }
  .stars span {
    padding: 0 4px;
    transition: transform 0.1s ease;
  }
  .stars span:hover,
  .stars span.selected {
    transform: scale(1.3);
  }
  .message {
    background:rgb(246, 224, 255);
    padding: 10px;
    border: 1px solid #0a0;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
    color: #060;
  }
  button[type="submit"] {
    margin-top: 20px;
    background:rgb(116, 40, 167);
    color: white;
    padding: 12px;
    border: none;
    border-radius: 7px;
    font-size: 1.1rem;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s ease;
  }
  button[type="submit"]:hover {
    background:rgb(93, 33, 136);
  }
  /* Emoji picker toggle button */
  #emoji-toggle {
    margin-top: 8px;
    cursor: pointer;
    background: #eee;
    border: 1px solid #ccc;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 1rem;
    user-select: none;
    width: fit-content;
  }
  #emoji-picker {
    margin-top: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px 8px;
    background: #fafafa;
    max-width: 100%;
    display: none; /* initially hidden */
    flex-wrap: wrap;
    gap: 8px;
  }
  #emoji-picker span {
    font-size: 1.7rem;
    cursor: pointer;
    user-select: none;
    padding: 4px;
    border-radius: 5px;
    transition: background-color 0.2s ease;
  }
  #emoji-picker span:hover {
    background-color: #ddd;
  }

  .stars {
  display: inline-block;
  cursor: pointer;
  font-size: 2rem;
  color: #ccc;
}
.stars span:hover,
.stars span:hover ~ span,
.stars .selected {
  color: gold;
}

.review-heading {
  margin-top: 40px;
  text-align: center;
  font-size: 24px;
  color: #333;
  font-weight: 600;
}

.review-card {
  border: 1px solid #ddd;
  padding: 20px;
  margin: 20px auto;
  border-radius: 10px;
  background: #fff;
  max-width: 700px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.3s ease;
}

.review-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.review-card p {
  margin: 10px 0;
  line-height: 1.6;
  color: #555;
}

.worker-reply {
  margin-top: 15px;
  padding: 15px;
  background-color:rgb(247, 234, 255);
  border-left: 5px solidrgb(140, 0, 255);
  border-radius: 5px;
  font-size: 14px;
}

.delete-btn {
  margin-top: 15px;
  padding: 8px 14px;
  background-color: #dc3545;
  border: none;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s ease;
}

.delete-btn:hover {
  background-color: #c82333;
}

.no-reviews-msg {
  margin-top: 40px;
  text-align: center;
  font-size: 18px;
  color: #666;
}

/* Header Styling */
.header {
  background: linear-gradient(90deg,rgb(170, 0, 176),rgb(138, 61, 201));
  padding: 20px;
  text-align: center;
  color: white;
  animation: slideDown 1s ease-out;
}

/* Footer Styling */
.footer {
  background: linear-gradient(90deg,rgb(170, 0, 176),rgb(138, 61, 201));
  color: #eee;
  text-align: center;
  padding: 15px 10px;
  font-size: 0.95rem;
  animation: fadeInUp 1.2s ease-out;
}

/* Grid Layout */
.grid-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  max-width: 1300px;
  margin: 40px auto;
  padding: 0 20px;
  animation: fadeIn 1s ease-in;
}

.review-form,
.review-list {
  background: white;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
  animation: fadeIn 0.8s ease-in;
}

/* Animations */
@keyframes slideDown {
  from {
    transform: translateY(-80px);
    opacity: 0;
  }
  to {
    transform: translateY(0px);
    opacity: 1;
  }
}

@keyframes fadeInUp {
  from {
    transform: translateY(60px);
    opacity: 0;
  }
  to {
    transform: translateY(0px);
    opacity: 1;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0.2;
  }
  to {
    opacity: 1;
  }
}

/* Responsive for smaller screens */
@media (max-width: 768px) {
  .grid-container {
    grid-template-columns: 1fr;
  }
}

.back-dashboard {
    margin: 20px;
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

<!-- Header with animation -->
<header class="header">
  <h1>Customer Feedback Portal</h1>
</header>

<!-- Main grid layout -->
<main class="grid-container">
  <!-- Review Form Section -->
  <section class="review-form">

<div class="review-container">
  <h2>Rate and Review a Worker</h2>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="worker_id">Select Worker</label>
    <select name="worker_id" id="worker_id" required>
      <option value="">-- Choose Worker --</option>
      <?php while ($worker = $workers->fetch_assoc()): ?>
        <option value="<?= $worker['worker_id'] ?>"><?= htmlspecialchars($worker['fullname']) ?></option>
      <?php endwhile; ?>
    </select>

   <label>Rating</label>
<div class="stars" title="Click to rate">
  <span data-value="1" title="1 Star">&#9733;</span> <!-- ★ -->
  <span data-value="2" title="2 Stars">&#9733;</span>
  <span data-value="3" title="3 Stars">&#9733;</span>
  <span data-value="4" title="4 Stars">&#9733;</span>
  <span data-value="5" title="5 Stars">&#9733;</span>
</div>
<input type="hidden" id="rating" name="rating" required>


    <label for="review_text">Review (optional)</label>
    <textarea name="review_text" id="review_text" placeholder="Write your feedback here..."></textarea>

    <div id="emoji-toggle" aria-expanded="false" aria-controls="emoji-picker">😀 Show Emojis</div>
    <div id="emoji-picker" role="list" aria-label="Emoji picker">
      <!-- More emojis -->
      <span>😀</span><span>😃</span><span>😄</span><span>😁</span><span>😆</span><span>😅</span><span>😂</span><span>🤣</span>
      <span>😊</span><span>😇</span><span>🙂</span><span>🙃</span><span>😉</span><span>😌</span><span>😍</span><span>🥰</span>
      <span>😘</span><span>😗</span><span>😙</span><span>😚</span><span>😋</span><span>😛</span><span>😝</span><span>😜</span>
      <span>🤪</span><span>🤨</span><span>🧐</span><span>🤓</span><span>😎</span><span>🥳</span><span>🤩</span><span>🥺</span>
      <span>😏</span><span>😒</span><span>😞</span><span>😔</span><span>😟</span><span>😕</span><span>🙁</span><span>☹️</span>
      <span>😣</span><span>😖</span><span>😫</span><span>😩</span><span>🥱</span><span>😤</span><span>😠</span><span>😡</span>
      <span>🤬</span><span>😳</span><span>🥵</span><span>🥶</span><span>😱</span><span>😨</span><span>😰</span><span>😥</span>
      <span>😓</span><span>🤗</span><span>🤔</span><span>🤭</span><span>🤫</span><span>🤥</span><span>😶</span><span>😐</span>
      <span>😑</span><span>😬</span><span>🙄</span><span>😯</span><span>😦</span><span>😧</span><span>😮</span><span>😲</span>
      <span>🥱</span><span>😴</span><span>🤤</span><span>😪</span><span>😵</span><span>🤐</span><span>🥴</span><span>🤢</span>
      <span>🤮</span><span>🤧</span><span>😷</span><span>🤒</span><span>🤕</span><span>🤑</span><span>🤠</span><span>😈</span>
      <span>👿</span><span>👹</span><span>👺</span><span>🤡</span><span>💩</span><span>👻</span><span>💀</span><span>☠️</span>
      <span>👽</span><span>👾</span><span>🤖</span><span>🎃</span><span>😺</span><span>😸</span><span>😹</span><span>😻</span>
      <span>😼</span><span>😽</span><span>🙀</span><span>😿</span><span>😾</span><span>👍</span><span>👎</span><span>👊</span>
      <span>✊</span><span>🤛</span><span>🤜</span><span>👏</span><span>🙌</span><span>👐</span><span>🤲</span><span>🤝</span>
      <span>🙏</span><span>✍️</span><span>💅</span><span>🤳</span><span>💪</span><span>🦾</span><span>🦿</span><span>🦵</span>
      <span>🦶</span><span>👂</span><span>🦻</span><span>👃</span><span>🧠</span><span>🦷</span><span>🦴</span>
      <span>❤️</span><span>💔</span><span>💕</span><span>💞</span><span>💓</span><span>💗</span><span>💖</span><span>💘</span>
      <span>💝</span><span>💟</span><span>💜</span><span>🧡</span><span>💛</span><span>💚</span><span>💙</span>
      <span>✨</span><span>⭐</span><span>🌟</span><span>🔥</span><span>💥</span><span>💫</span><span>🎉</span><span>🎊</span>
    </div>

    <button type="submit">Submit Review</button>
  </form>
</div>
  </section>

 <!-- Review List Section -->
  <section class="review-list">
    <h2 class="review-heading">Your Submitted Reviews</h2>
    <?php if ($reviews_result->num_rows > 0): ?>
      <?php while ($row = $reviews_result->fetch_assoc()): ?>
        <div class="review-card">
          <p><strong>Worker:</strong> <?= htmlspecialchars($row['fullname']) ?></p>
          <p><strong>Rating:</strong> <?= htmlspecialchars($row['rating']) ?> ⭐</p>
          <p><strong>Review:</strong> <?= htmlspecialchars($row['review_text']) ?></p>
          <p><strong>Date:</strong> <?= htmlspecialchars($row['review_date']) ?></p>

          <?php if (!empty($row['reply_text'])): ?>
            <div class="worker-reply">
              <strong>Worker Reply:</strong><br>
              <?= htmlspecialchars($row['reply_text']) ?>
              <br><em><?= htmlspecialchars($row['reply_date']) ?></em>
            </div>
          <?php endif; ?>

          <form method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
            <input type="hidden" name="delete_review_id" value="<?= $row['review_id'] ?>">
            <button type="submit" name="delete_review" class="delete-btn">Delete</button>
          </form>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-reviews-msg">You have not submitted any reviews yet.</p>
    <?php endif; ?>
  </section>
</main>

<div style="text-align:center;">
  <a href="CustomerDashboard1.php" class="back-dashboard"><i class="fas fa-arrow-left"></i> Dashboard</a>
</div>

<!-- Footer with animation 
<footer class="footer">
  <p>&copy; <?= date('Y') ?> Home Service Management System. All rights reserved.</p>
</footer>-->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.stars span');
    const ratingInput = document.getElementById('rating');

    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        ratingInput.value = index + 1;
        stars.forEach((s, i) => s.classList.toggle('selected', i <= index));
      });
    });

    const emojiToggle = document.getElementById('emoji-toggle');
    const emojiPicker = document.getElementById('emoji-picker');
    const reviewTextarea = document.getElementById('review_text');

    emojiToggle.addEventListener('click', () => {
      if (emojiPicker.style.display === "flex") {
        emojiPicker.style.display = "none";
        emojiToggle.textContent = "😀 Show Emojis";
        emojiToggle.setAttribute('aria-expanded', 'false');
      } else {
        emojiPicker.style.display = "flex";
        emojiToggle.textContent = "❌ Hide Emojis";
        emojiToggle.setAttribute('aria-expanded', 'true');
      }
    });

    emojiPicker.querySelectorAll('span').forEach(emoji => {
      emoji.addEventListener('click', () => {
        // Insert emoji at cursor position in textarea
        const start = reviewTextarea.selectionStart;
        const end = reviewTextarea.selectionEnd;
        const text = reviewTextarea.value;
        const emojiChar = emoji.textContent;
        reviewTextarea.value = text.substring(0, start) + emojiChar + text.substring(end);
        reviewTextarea.selectionStart = reviewTextarea.selectionEnd = start + emojiChar.length;
        reviewTextarea.focus();
      });
    });
  });
</script>

</body>
</html>
