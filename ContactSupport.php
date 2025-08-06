<?php 
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
session_start();

// Add Contact
if (isset($_POST['add_contact'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $working_hours = $_POST['working_hours'];

    $stmt = $conn->prepare("INSERT INTO contacts (email, phone, address, working_hours) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $phone, $address, $working_hours);
    $stmt->execute();
    $stmt->close();
}

// Delete Contact
if (isset($_POST['delete_contact'])) {
    $id = $_POST['contact_id'];
    $stmt = $conn->prepare("DELETE FROM contacts WHERE contact_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Add FAQ
if (isset($_POST['add_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $stmt = $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
    $stmt->bind_param("ss", $question, $answer);
    $stmt->execute();
    $stmt->close();
}

// Delete FAQ
if (isset($_POST['delete_faq'])) {
    $id = $_POST['faq_id'];
    $stmt = $conn->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch contacts
$contact_result = $conn->query("SELECT * FROM contacts ORDER BY contact_id DESC");

// Fetch FAQs
$faq_result = $conn->query("SELECT * FROM faqs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Contact & Support</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 20px;
           background: #fbecff;
            color: #333;
        }

        h2 {
            text-align: center;
            margin: 40px 0 20px;
            color: #222;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: auto;
        }

        .section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section h3 {
            margin-top: 0;
            color:rgb(89, 34, 139);
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            background:rgb(89, 20, 142);
            color: white;
            border: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        button:hover {
            background:rgb(85, 28, 122);
        }

        .delete-btn {
            background: #dc3545;
        }

        .delete-btn:hover {
            background: #a71d2a;
        }

        .card-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .card {
            background: #f9f9f9;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.01);
        }

        .card strong {
            color: #444;
        }

        .action-form {
            display: inline;
        }

        .no-data {
            color: #888;
            font-style: italic;
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

<h2>Admin Contact & Support</h2>
<div class="container">

    <!-- Contact Section -->
    <div class="section">
        <h3>Add New Contact</h3>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="working_hours" placeholder="Working Hours (e.g. Mon-Fri 9am-6pm)" required>
            <button type="submit" name="add_contact">Add Contact</button>
        </form>

        <h3>Contact List</h3>
        <div class="card-list">
            <?php if ($contact_result->num_rows > 0): ?>
                <?php while ($row = $contact_result->fetch_assoc()): ?>
                    <div class="card">
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                        <p><strong>Working Hours:</strong> <?= htmlspecialchars($row['working_hours']) ?></p>
                        <form method="POST" class="action-form" onsubmit="return confirm('Delete this contact?');">
                            <input type="hidden" name="contact_id" value="<?= $row['contact_id'] ?>">
                            <button type="submit" name="delete_contact" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No contacts found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="section">
        <h3>Add New FAQ</h3>
        <form method="POST">
            <input type="text" name="question" placeholder="Question" required>
            <textarea name="answer" placeholder="Answer" rows="3" required></textarea>
            <button type="submit" name="add_faq">Add FAQ</button>
        </form>

        <h3>FAQs</h3>
        <div class="card-list">
            <?php if ($faq_result->num_rows > 0): ?>
                <?php while ($faq = $faq_result->fetch_assoc()): ?>
                    <div class="card">
                        <p><strong>Q:</strong> <?= htmlspecialchars($faq['question']) ?></p>
                        <p><strong>A:</strong> <?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
                        <form method="POST" class="action-form" onsubmit="return confirm('Delete this FAQ?');">
                            <input type="hidden" name="faq_id" value="<?= $faq['id'] ?>">
                            <button type="submit" name="delete_faq" class="delete-btn">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No FAQs found.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<a href="AdminDashboard1.php" class="back-btn">← Back to Dashboard</a>


</body>
</html>
