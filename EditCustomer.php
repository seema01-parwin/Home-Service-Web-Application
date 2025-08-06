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

if (!isset($_GET['customer_id']) || !is_numeric($_GET['customer_id'])) {
    header("Location: ManageCustomer.php");
    exit();
}

$customer_id = $_GET['customer_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($fullname && $email && $phone) {
        $stmt = $conn->prepare("UPDATE customers SET name=?, email=?, phone=?, address=? WHERE customer_id=?");
        $stmt->bind_param("ssssi", $fullname, $email, $phone, $address, $customer_id);
        if ($stmt->execute()) {
            $success = "Customer updated successfully.";
        } else {
            $error = "Update failed. Try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all required fields.";
    }
}

$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    header("Location: ManageCustomer.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset and base styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f7e9ff, #e6d2ff);
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            animation: fadeIn 1.5s ease;
        }

        h1 {
            color: #6c2b84;
            text-align: center;
            margin-bottom: 25px;
            animation: slideDown 1s ease-in-out;
        }

        .logout-btn {
            display: block;
            background: #8e44ad;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            width: fit-content;
            margin: 0 auto 25px;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #a64bc3;
        }

        .form-box {
            background: #fff;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            animation: slideUp 1s ease;
        }

        .form-box label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        .form-box input,
        .form-box textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border 0.3s;
        }

        .form-box input:focus,
        .form-box textarea:focus {
            border-color: #a64bc3;
            outline: none;
        }

        .form-box button {
            background: #a64bc3;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        .form-box button:hover {
            background: #8e44ad;
        }

        .success, .error {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        @media (max-width: 600px) {
            .form-box {
                padding: 20px;
            }

            .logout-btn {
                width: 100%;
                margin-bottom: 15px;
            }
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        @keyframes slideDown {
            from {transform: translateY(-30px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }

        @keyframes slideUp {
            from {transform: translateY(30px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }
    </style>
</head>
<body>

<h1>Edit Customer</h1>
<a href="ManageCustomer.php" class="logout-btn">← Back to Customer List</a>

<div class="form-box">
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($customer['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>

        <label>Address</label>
        <textarea name="address" rows="3"><?= htmlspecialchars($customer['address']) ?></textarea>

        <button type="submit">Update Customer</button>
    </form>
</div>

</body>
</html>
