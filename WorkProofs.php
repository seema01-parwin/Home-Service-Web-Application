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
}

// Check if worker is logged in
if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$msg = "";

// Upload new proof
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["proof_image"])) {
    $uploadDir = "uploads/proofs/";
    $fileName = basename($_FILES["proof_image"]["name"]);
    $targetPath = $uploadDir . time() . "_" . $fileName;

    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $targetPath)) {
            $insert = "INSERT INTO worker_proofs (worker_id, image_path, uploaded_at) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($insert);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("is", $worker_id, $targetPath);
            $stmt->execute();
            $msg = "Proof uploaded successfully.";
        } else {
            $msg = "Failed to upload image.";
        }
    } else {
        $msg = "Only JPG, PNG, and GIF files are allowed.";
    }
}


// Delete proof
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $proofId = $_GET['delete'];
    $check = $conn->prepare("SELECT * FROM work_proofs WHERE id = ? AND worker_id = ?");
    $check->bind_param("ii", $proofId, $worker_id);
    $check->execute();
    $proof = $check->get_result()->fetch_assoc();

    if ($proof) {
        unlink($proof['proof_image']); // delete image file
        $del = $conn->prepare("DELETE FROM work_proofs WHERE id = ?");
        $del->bind_param("i", $proofId);
        $del->execute();
        $msg = "Proof deleted.";
    }
}

// Fetch all proofs of this worker
$proofs = $conn->prepare("SELECT * FROM worker_proofs WHERE worker_id = ? ORDER BY uploaded_at DESC");

$proofs->bind_param("i", $worker_id);
$proofs->execute();
$result = $proofs->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Work Proof Upload | Worker Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(253, 228, 253),rgb(240, 201, 255));
            color: #333;
            line-height: 1.6;
        }

        /* Header */
        header {
            background:rgb(85, 24, 121);
            background: linear-gradient(90deg,rgb(147, 1, 152),rgb(145, 67, 208));
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.8s ease-out;
        }

        header h1 {
            font-size: 24px;
        }

        /* Navigation */
        nav {
            display: flex;
            gap: 25px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        nav a:hover {
            color:rgb(240, 215, 255);
        }

        /* Proof Container */
        .proof-container {
            padding: 30px 40px;
            animation: fadeIn 1s ease-in;
        }

        .proof-container h2 {
            margin-bottom: 20px;
            color:rgb(125, 44, 186);
        }

        .upload-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .upload-box input[type="file"] {
            padding: 5px;
            margin-right: 10px;
        }

        .upload-box button {
            padding: 8px 20px;
            background:rgb(143, 57, 208);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .upload-box button:hover {
            background:rgb(60, 22, 93);
        }

        .message {
            color: green;
            margin: 15px 0;
            font-weight: bold;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            animation: zoomIn 0.6s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 130px;
            object-fit: cover;
            border-radius: 5px;
        }

        .delete-btn {
            display: inline-block;
            margin-top: 8px;
            color: #d9534f;
            text-decoration: none;
            font-size: 14px;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeIn {
            0% {opacity: 0; transform: translateY(20px);}
            100% {opacity: 1; transform: translateY(0);}
        }

        @keyframes slideDown {
            0% {opacity: 0; transform: translateY(-20px);}
            100% {opacity: 1; transform: translateY(0);}
        }

        @keyframes zoomIn {
            0% {transform: scale(0.9); opacity: 0;}
            100% {transform: scale(1); opacity: 1;}
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }

            .proof-container {
                padding: 20px;
            }

            .upload-box button {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Header + Navigation -->
<header>
    <h1><i class="fas fa-tools"></i> SP Home Services</h1>
    <nav>
        <a href="WorkerDashboard1.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="WorkerBookings.php"><i class="fas fa-calendar-check"></i> My Bookings</a>
        
       
        <a href="WorkerLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<!-- Main Content -->
<div class="proof-container">
    <h2><i class="fas fa-image"></i> Upload Work Proof</h2>

    <?php if ($msg): ?>
        <p class="message"><?= $msg ?></p>
    <?php endif; ?>

    <div class="upload-box">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="proof_image" required>
            <button type="submit"><i class="fas fa-upload"></i> Upload Proof</button>
        </form>
    </div>

    <h3>My Uploaded Proofs</h3>
    <div class="gallery">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="gallery-item">
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Work Proof">
                    <div><?= date("Y-m-d", strtotime($row['uploaded_at'])) ?></div>
                    <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this proof?')">
                        <i class="fas fa-trash-alt"></i> Delete
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No proofs uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

