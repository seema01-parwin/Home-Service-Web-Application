<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "home_service_db";
$conn = new mysqli($host, $user, $pass, $db);

$action = $_GET['action'] ?? '';
$worker_id = isset($_GET['worker_id']) ? intval($_GET['worker_id']) : 0;


if ($worker_id <= 0) {
    header("Location: ManageWorkers.php?error=invalid_id");
    exit();
}

// Approve Worker
if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE workers SET status='approved' WHERE worker_id=?
");
    $stmt->bind_param("i", $worker_id);
    if ($stmt->execute()) {
        header("Location: ManageWorkers.php?success=approved");
    } else {
        header("Location: ManageWorkers.php?error=approve_failed");
    }
    exit();
}

// Reject Worker
if ($action === 'reject') {
    $stmt = $conn->prepare("DELETE FROM workers WHERE id=? AND status='Pending'");
    $stmt->bind_param("i", $worker_id);
    if ($stmt->execute()) {
        header("Location: ManageWorkers.php?success=rejected");
    } else {
        header("Location: ManageWorkers.php?error=reject_failed");
    }
    exit();
}

// Delete Worker
if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM workers WHERE id=?");
    $stmt->bind_param("i", $worker_id);
    if ($stmt->execute()) {
        header("Location: ManageWorkers.php?success=deleted");
    } else {
        header("Location: ManageWorkers.php?error=delete_failed");
    }
    exit();
}

// Edit Worker - Display Form or Handle Post
if ($action === 'edit') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $skill = trim($_POST['skill']);
        $address = trim($_POST['address']);

        if ($fullname && $email && $phone && $skill) {
            $stmt = $conn->prepare("UPDATE workers SET fullname=?, email=?, phone=?, skill=?, address=? WHERE worker_id=?");
            $stmt->bind_param("sssssi", $fullname, $email, $phone, $skill, $address, $worker_id);
            if ($stmt->execute()) {
                header("Location: ManageWorkers.php?success=updated");
                exit();
            } else {
                $error = "Update failed.";
            }
        } else {
            $error = "All fields are required.";
        }
    }

    // DELETE WORK PROOF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_proof') {
    $proofId = intval($_POST['proof_id']);
    $workerId = intval($_POST['worker_id']);

    $stmt = $conn->prepare("SELECT image_path FROM worker_proofs WHERE id = ?");
    $stmt->bind_param("i", $proofId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $img = $res->fetch_assoc()['image_path'];
        if (file_exists($img)) unlink($img);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM worker_proofs WHERE id = ?");
    $stmt->bind_param("i", $proofId);
    $stmt->execute();
    $stmt->close();

    header("Location: javascript://history.go(-1)");
    exit();
}


    // Get worker details
    $stmt = $conn->prepare("SELECT * FROM workers WHERE worker_id=?");
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $worker = $result->fetch_assoc();

    if (!$worker) {
        header("Location: ManageWorkers.php?error=not_found");
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Edit Worker</title>
        <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #fbecff;
        margin: 0;
        padding: 0;
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-box {
        max-width: 600px;
        margin: 60px auto;
        padding: 30px;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        transition: all 0.4s ease;
        animation: slideUp 0.6s ease-in-out;
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    h2 {
        text-align: center;
        color:rgb(129, 64, 155);
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
        color:rgb(117, 55, 142);
    }

    .form-box input,
    .form-box textarea {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: border-color 0.3s, box-shadow 0.3s;
        font-size: 15px;
    }

    .form-box input:focus,
    .form-box textarea:focus {
        border-color:rgb(177, 52, 219);
        box-shadow: 0 0 8px rgba(161, 52, 219, 0.3);
        outline: none;
    }

    .form-box button {
        width: 100%;
        padding: 12px;
        background:rgb(141, 52, 219);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    .form-box button:hover {
        background:rgb(118, 41, 185);
        transform: scale(1.02);
    }

    .form-box a {
        display: block;
        text-align: center;
        margin-top: 20px;
        text-decoration: none;
        color:rgb(125, 41, 185);
        transition: color 0.3s;
    }

    .form-box a:hover {
        color:rgb(88, 28, 128);
    }

    .error {
        color: #e74c3c;
        background: #fbeaea;
        padding: 10px;
        border-left: 5px solid #e74c3c;
        border-radius: 6px;
        margin-bottom: 15px;
        animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
        100% { transform: translateX(0); }
    }
</style>

    </head>
    <body>
    <div class="form-box">
        <h2>Edit Worker</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($worker['fullname']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($worker['email']) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($worker['phone']) ?>" required>

            <label>Skill/Category</label>
            <input type="text" name="skill" value="<?= htmlspecialchars($worker['skill']) ?>" required>

            <label>Address</label>
            <textarea name="address"><?= htmlspecialchars($worker['address']) ?></textarea>

            <button type="submit">Update Worker</button>
        </form>
        <br>
        <a href="ManageWorkers.php">← Back to Workers</a>
    </div>
    </body>
    </html>
    <?php
    exit();
}

// Fallback
header("Location: ManageWorkers.php");
exit();
