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

if (!isset($_SESSION['customer_id'])) {
    header('Location: CustomerLogin1.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$query = "SELECT * FROM customers WHERE customer_id='$customer_id'";
$result = mysqli_query($conn, $query);
$customer = mysqli_fetch_assoc($result);

// Update Profile
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);

    $update_query = "UPDATE customers SET name='$name', email='$email', phone='$phone', address='$address' WHERE customer_id='$customer_id'";
    if (mysqli_query($conn, $update_query)) {
        header('Location: CustomerProfile1.php?update=success');
        exit();
    } else {
        echo "Error updating profile.";
    }
}

// Upload Profile Picture
if (isset($_POST['upload_profile_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed)) {
            if ($file_size <= 2 * 1024 * 1024) {
                $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
                $target_dir = 'Image/';
                if (move_uploaded_file($file_tmp, $target_dir . $new_file_name)) {
                    $update_picture_query = "UPDATE customers SET profile_picture='$new_file_name' WHERE customer_id='$customer_id'";
                    if (mysqli_query($conn, $update_picture_query)) {
                        header('Location: CustomerProfile1.php?upload=success');
                        exit();
                    } else {
                        echo "Error updating profile picture.";
                    }
                } else {
                    echo "Failed to upload file.";
                }
            } else {
                echo "File too large. Max 2MB.";
            }
        } else {
            echo "Invalid file type.";
        }
    }
}

// Change Password
if (isset($_POST['change_password'])) {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $query_password = "SELECT password FROM customers WHERE customer_id='$customer_id'";
    $result_password = mysqli_query($conn, $query_password);
    $row = mysqli_fetch_assoc($result_password);

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE customers SET password='$hashed_password' WHERE customer_id='$customer_id'";
            if (mysqli_query($conn, $update_password_query)) {
                header('Location: CustomerProfile1.php?password=success');
                exit();
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "New password and confirmation do not match.";
        }
    } else {
        echo "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
 
  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 10px;
     background: #fbecff;
    animation: fadeInBody 0.8s ease-in;
  }

  @keyframes fadeInBody {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  /* Header Styling */
header {
  background: linear-gradient(90deg,rgb(170, 0, 176),rgb(138, 61, 201));
  padding: 30px;
  text-align: center;
  color: white;
  animation: slideDown 1s ease-out;
}


  @keyframes slideDown {
    from {
      transform: translateY(-100%);
    }
    to {
      transform: translateY(0);
    }
  }

  .logo img {
    height: 50px;
    margin-right: 15px;
  }

  h1 {
    font-size: 24px;
    margin: 0;
    align-items: center;
  }

  h3{
    color: #630675;
    margin-bottom: 30px;
  }

  .success-message {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    margin: 10px auto;
    width: 80%;
    border-radius: 5px;
    text-align: center;
    animation: fadeInUp 0.5s ease-in-out;
  }

  .profile-grid {
    display: grid;
    grid-template-columns: 1fr 2fr 1.5fr;
    gap: 20px;
    margin: 30px auto;
    padding: 20px;
    max-width: 1200px;
  }

  .profile-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    animation: fadeInUp 0.7s ease;
    transition: transform 0.3s;
  }

  .profile-section:hover {
    transform: translateY(-4px);
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .profile-section img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
    transition: transform 0.3s ease-in-out;
  }

  .profile-section img:hover {
    transform: scale(1.05);
  }

  .form-group {
    margin-bottom: 15px;
  }

  label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
    color:rgb(166, 36, 192);
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="file"] {
    width: 97%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  input:focus {
    border-color: #9c27b0;
    box-shadow: 0 0 5px rgba(156, 39, 176, 0.5);
    outline: none;
  }

  .submit-btn {
    background-color: #8e24aa;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s ease;
  }

  .submit-btn:hover {
    background-color: #6a1b9a;
    transform: scale(1.05);
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

  footer {
    text-align: center;
    padding: 15px;
    background: #630675;
    margin-top: 30px;
    animation: fadeInUp 1s ease;
    color: white;
  }
</style>


</head>
<body>

<header>
  <h1>Profile Management</h1>
</header>

<?php if (isset($_GET['update'])): ?>
  <p class="success-message">Profile updated successfully!</p>
<?php endif; ?>
<?php if (isset($_GET['upload'])): ?>
  <p class="success-message">Profile picture uploaded successfully!</p>
<?php endif; ?>
<?php if (isset($_GET['password'])): ?>
  <p class="success-message">Password changed successfully!</p>
<?php endif; ?>

<div class="profile-grid">

  <!-- Left: Profile Picture -->
  <div class="profile-section">
    <h3>Profile Picture</h3>
    <img src="Image/<?php echo htmlspecialchars($customer['profile_picture'] ?: 'Icons_default.png'); ?>" alt="Profile">
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_picture" required>
      <br><br>
      <button type="submit" name="upload_profile_picture" class="submit-btn">Upload</button>
    </form>
  </div>

  <!-- Middle: Profile Info -->
  <div class="profile-section">
    <h3>Edit Profile</h3>
    <form method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
      </div>
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
      </div>
      <button type="submit" name="update_profile" class="submit-btn">Update Profile</button>
    </form>
  </div>

  <!-- Right: Change Password -->
  <div class="profile-section">
    <h3>Change Password</h3>
    <form method="POST">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" required>
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>
      </div>
      <button type="submit" name="change_password" class="submit-btn">Change Password</button>
    </form>
  </div>

</div>

<div style="text-align:center;">
  <a href="CustomerDashboard1.php" class="back-dashboard"><i class="fas fa-arrow-left"></i> Dashboard</a>
</div>

<!--<footer>
  <p>&copy; 2025 SPI Home Service Management System. All rights reserved.</p>
</footer>-->

</body>
</html>
