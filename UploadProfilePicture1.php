<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: CustomerLogin1.php');
    exit();
}

if (isset($_POST['upload'])) {
    $customer_id = $_SESSION['customer_id'];

    // Check if a file is selected
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];

        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($file_ext, $allowed)) {
            if ($file_size <= 2 * 1024 * 1024) { // 2MB size limit
                // Create a unique file name
                $new_file_name = uniqid('profile_', true) . '.' . $file_ext;

                // Define the target directory (Image folder)
                $target_dir = 'Image/'; 

                // Upload the file to 'Image' folder
                if (move_uploaded_file($file_tmp, $target_dir . $new_file_name)) {
                    // Update database with the new profile picture
                    $update_query = "UPDATE customers SET profile_picture='$new_file_name' WHERE customer_id='$customer_id'";
                    if (mysqli_query($conn, $update_query)) {
                        header('Location: CustomerDashboard1.php?upload=success');
                        exit();
                    } else {
                        echo "Database update failed.";
                    }
                } else {
                    echo "Failed to upload the file.";
                }
            } else {
                echo "File is too large! Maximum 2MB allowed.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF allowed.";
        }
    } else {
        echo "Please select a file to upload.";
    }
} else {
    header('Location: CustomerDashboard1.php');
    exit();
}
?>
