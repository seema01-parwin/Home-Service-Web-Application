<?php
session_start();
$admin_id = 1; // Replace this with session value like: $_SESSION['admin_id'];

$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle update
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $profile_picture = $_FILES['profile_picture'];

    // Handle profile picture upload
    $filename = "";
    if ($profile_picture['name']) {
        $ext = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
        $filename = "admin_" . time() . "." . $ext;
        move_uploaded_file($profile_picture['tmp_name'], "uploads/" . $filename);
    }

    // Build SQL update query
    $sql = "UPDATE admins SET username = ?, ";
    $params = [$username];
    $types = "s";

    if (!empty($new_password)) {
        $sql .= "password = ?, ";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        $types .= "s";
    }

    if ($filename) {
        $sql .= "profile_picture = ?, ";
        $params[] = $filename;
        $types .= "s";
    }

    $sql = rtrim($sql, ", ") . " WHERE admin_id = ?";
    $params[] = $admin_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    $message = "Profile updated successfully!";
}

// Fetch admin data
$result = $conn->query("SELECT * FROM admins WHERE admin_id = $admin_id");
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile Management</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fbecff;
            padding: 30px;
        }
        .container {
            background: #fff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color:rgb(119, 0, 255);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solidrgb(170, 0, 255);
            margin-bottom: 10px;
        }
        button {
            margin-top: 25px;
            padding: 12px;
            width: 100%;
            background:rgb(191, 0, 255);
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background:rgb(113, 0, 179);
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin-top: 15px;
            border-radius: 6px;
        }
        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 42px;
            cursor: pointer;
            color: #888;
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
    <h2>Admin Profile</h2>

    <?php if (isset($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($admin['profile_picture']): ?>
            <img src="uploads/<?= $admin['profile_picture'] ?>" class="profile-pic" alt="Admin Picture">
        <?php else: ?>
            <img src="https://via.placeholder.com/120" class="profile-pic" alt="No Picture">
        <?php endif; ?>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

        <label>New Password:</label>
        <div class="password-wrapper">
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <label>Change Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>
<a href="AdminDashboard1.php" class="back-btn">← Back to Dashboard</a>
<script>
function togglePassword() {
    const input = document.getElementById("new_password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
