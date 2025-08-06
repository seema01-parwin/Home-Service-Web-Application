<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$faq_result = $conn->query("SELECT * FROM faqs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help & FAQs - Customer Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #4a148c;
            --secondary: #7b1fa2;
            --bg: #f4f6f9;
            --white: #ffffff;
            --text: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 10px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--bg);
            color: var(--text);
        }

        header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: var(--white);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        header h1 {
            font-size: 24px;
            margin: 0;
        }

        .dashboard-btn {
            background: #7b1fa2;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .dashboard-btn:hover {
            background:rgb(99, 33, 171);
        }

        .faq-container {
            max-width: 900px;
            margin: 40px auto;
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        }

        .faq-container h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 30px;
        }

        .faq-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .faq-question {
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            padding-right: 30px;
            color: var(--primary);
        }

        .faq-question::after {
            content: "+";
            position: absolute;
            right: 0;
            font-size: 22px;
            transition: transform 0.3s ease;
        }

        .faq-question.active::after {
            content: "-";
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            color: #444;
            padding-left: 10px;
            margin-top: 5px;
            line-height: 1.6;
        }

        .faq-answer.show {
            max-height: 500px;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-btn {
                margin-top: 10px;
            }

            .faq-question {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Help & FAQs</h1>
    <a href="CustomerDashboard1.php" class="dashboard-btn">Back to Dashboard</a>
</header>

<div class="faq-container">
    <h2>Frequently Asked Questions</h2>

    <?php if ($faq_result && $faq_result->num_rows > 0): ?>
        <?php while ($row = $faq_result->fetch_assoc()): ?>
            <div class="faq-item">
                <div class="faq-question"><?= htmlspecialchars($row['question']) ?></div>
                <div class="faq-answer"><?= nl2br(htmlspecialchars($row['answer'])) ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No FAQs found.</p>
    <?php endif; ?>
</div>

<script>
    // JavaScript Accordion
    const questions = document.querySelectorAll(".faq-question");

    questions.forEach((question) => {
        question.addEventListener("click", () => {
            question.classList.toggle("active");
            const answer = question.nextElementSibling;
            answer.classList.toggle("show");
        });
    });
</script>

</body>
</html>
