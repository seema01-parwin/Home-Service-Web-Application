<?php 
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['worker_id'])) {
    header("Location: WorkerLogin1.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];

// Fetch worker data
$sql = "SELECT * FROM workers WHERE worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

// Fetch average rating
$ratingQuery = "SELECT ROUND(AVG(rating), 1) as avg_rating FROM reviews WHERE worker_id = $worker_id";
$ratingResult = mysqli_query($conn, $ratingQuery);
$ratingData = mysqli_fetch_assoc($ratingResult);
$avg_rating = $ratingData['avg_rating'] ?? 'No ratings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f7f7;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            background: linear-gradient(to right, #9100bc, #f08fff);
            color: #fff;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 50px;
        }

        .go-back {
            background:  #9100bc;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .go-back:hover {
            background: purple;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            padding: 40px;
        }

        .card {
            background: linear-gradient(to bottom, #c354e0, #f4caff);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease forwards;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2, h3 {
            color: #fff;
            border-bottom: 2px solid #fff;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            color: #fff;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
        }

        button {
            background-color: #9100bc;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #750b9e;
        }

        .profile-pic {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-pic img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #fff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .rating {
            text-align: center;
            color: #fff;
            font-size: 18px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .grid-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
   
        <h1>Profile Management</h1>
    </div>
    <a href="WorkerDashboard1.php" class="go-back"><i class="fas fa-arrow-left"></i> Go Back</a>
</header>

<div class="grid-container">

    <!-- Section 1: Profile Overview -->
    <div class="card">
        <h2>Welcome, <?php echo htmlspecialchars($worker['fullname']); ?>!</h2>
        <div class="profile-pic">
            <img src="<?php echo htmlspecialchars($worker['profile_picture'] ?? 'Image/Icons_default.png'); ?>">
            <form action="UploadWorkerProfilePicture.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*" required>
                <button type="submit">Update Picture</button>
            </form>
        </div>
        <div class="rating"><strong>Average Rating:</strong> <?php echo $avg_rating; ?> ⭐</div>
    </div>

    <!-- Section 2: Profile Update -->
    <div class="card">
        <h3>Update Profile</h3>
        <form action="UpdateWorkerProfile.php" method="POST">
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($worker['fullname']); ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($worker['email']); ?>" required>
            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($worker['phone']); ?>" required>
            <label>Address:</label>
            <textarea name="address" required><?php echo htmlspecialchars($worker['address']); ?></textarea>
            <label>Skill:</label>
            <input type="text" name="skill" value="<?php echo htmlspecialchars($worker['skill']); ?>" required>
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <!-- Section 3: Password + Availability -->
    <div class="card">
        <h3>Change Password</h3>
        <form action="ChangeWorkerPassword.php" method="POST">
            <label>Old Password:</label>
            <input type="password" name="old_password" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
            <button type="submit">Change Password</button>
        </form>

        <h3>Availability</h3>
        <form action="UpdateWorkerAvailability.php" method="POST">
            <label>Available Days:</label>
            <select name="availability" required>
                <option value="Mon - Sat">Mon - Sat</option>
                <option value="Mon - Fri">Mon - Fri</option>
                <option value="Weekends Only">Weekends Only</option>
                <option value="Custom">Custom</option>
            </select>
            <label>Working Hours:</label>
            <input type="text" name="working_hours" placeholder="e.g., 9:00 a.m - 5:00 p.m" required>
            <button type="submit">Update Availability</button>
        </form>
    </div>
</div>

</body>
</html>
