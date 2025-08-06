<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: CustomerLogin1.php");
    exit();
}

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


$customer_id = $_SESSION['customer_id'];
$errors = [];
$success = "";

// Fetch active services
$services = [];
$sql = "SELECT service_id, service_name FROM services WHERE active = 1";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Handle AJAX request to fetch workers by service_name (skill)
if (isset($_GET['action']) && $_GET['action'] === 'get_workers' && isset($_GET['service_name'])) {
    $service_name = $_GET['service_name'];
    $stmt = $conn->prepare("SELECT worker_id, fullname FROM workers WHERE skill = ? AND status = 'Approved'");
    $stmt->bind_param("s", $service_name);
    $stmt->execute();
    $res = $stmt->get_result();
    $workers = [];
    while ($row = $res->fetch_assoc()) {
        $workers[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($workers);
    exit();
}

// Input sanitization
$service_id = isset($_POST['service']) ? intval($_POST['service']) : 0;
$worker_id = isset($_POST['worker']) ? intval($_POST['worker']) : 0;
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$payment_method = trim($_POST['payment_method'] ?? '');
$service_address = trim($_POST['service_address'] ?? '');
$city = trim($_POST['city'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$customer_note = trim($_POST['customer_note'] ?? '');

    if (!$service_id || !$worker_id || !$date || !$time || !$payment_method) {
        $errors[] = "";
    } else {
        // Validate service and worker relationship
        $stmt = $conn->prepare("SELECT service_name FROM services WHERE service_id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $serviceRes = $stmt->get_result();
        if ($serviceRes->num_rows === 0) {
            $errors[] = "Invalid service selected.";
        } else {
            $service_name = $serviceRes->fetch_assoc()['service_name'];
            // Check if worker matches the skill
            $stmt2 = $conn->prepare("SELECT * FROM workers WHERE worker_id = ? AND skill = ? AND status = 'Approved'");
            $stmt2->bind_param("is", $worker_id, $service_name);
            $stmt2->execute();
            $workerRes = $stmt2->get_result();
            if ($workerRes->num_rows === 0) {
                $errors[] = "Selected worker is not valid for the chosen service.";
            }
        }
        $stmt->close();
        $stmt2->close();

        // Date & time validation could be enhanced here

        if (empty($errors)) {
            $booking_datetime = $date . ' ' . $time;

           // Insert booking without location
$insert_sql = "INSERT INTO bookings 
    (customer_id, worker_id, payment_method, service_id, date, time, booking_datetime, status, booking_status, 
     service_address, city, postal_code, customer_note) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 'Pending', ?, ?, ?, ?)";

$stmt = $conn->prepare($insert_sql);

if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param(
    "iisssssssss",
    $customer_id,
    $worker_id,
    $payment_method,
    $service_id,
    $date,
    $time,
    $booking_datetime,
    $service_address,
    $city,
    $postal_code,
    $customer_note
);


            if ($stmt->execute()) {
                $success = "Booking submitted successfully and awaiting admin approval.";
            } else {
                $errors[] = "Failed to submit booking. Please try again.";
            }
            $stmt->close();
        }
    }

// ... keep previous PHP code here ... 

// Add two new AJAX handlers:

if (isset($_GET['action']) && $_GET['action'] === 'get_service_details' && isset($_GET['service_id'])) {
    $service_id = (int)$_GET['service_id'];
    $stmt = $conn->prepare("SELECT description, price, service_rate, service_duration, service_rules FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $serviceDetails = $res->fetch_assoc() ?: [];
    header('Content-Type: application/json');
    echo json_encode($serviceDetails);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'get_worker_details' && isset($_GET['worker_id'])) {
    $worker_id = (int)$_GET['worker_id'];

    $stmt = $conn->prepare("SELECT fullname, phone, profile_picture FROM workers WHERE worker_id = ?");
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $workerRes = $stmt->get_result();
    $worker = $workerRes->fetch_assoc();

    $stmt2 = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE worker_id = ?");
    $stmt2->bind_param("i", $worker_id);
    $stmt2->execute();
    $ratingRes = $stmt2->get_result();
    $ratingRow = $ratingRes->fetch_assoc();
    $avgRating = $ratingRow['avg_rating'] ?? 0;

    $response = [
        'fullname' => $worker['fullname'] ?? '',
        'phone' => $worker['phone'] ?? '',
        'profile_picture' => $worker['profile_picture'] ?? '',
        'avg_rating' => round(floatval($avgRating), 2)
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Book Service</title>
<style>
 
    /* Global Styling */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
    height: 85%;
    margin: 0;
}

/* Wrapper that contains all page content */
.wrapper {
    min-height: 100%;
    display: flex;
    flex-direction: column;
}

/* Content grows to fill available space */
.content {
    flex: 1;
}

    body {
    font-family: Arial, sans-serif;
   background: #fbecff;
    padding: 20px;

  }

    /* Header styling */
header {
            background: linear-gradient(90deg,rgb(170, 0, 176),rgb(138, 61, 201));
            color: white;
            padding: 30px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
   
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        header h2 {
            font-size: 1.8rem;
        }

        nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color:rgb(219, 161, 255);
        }
/* Responsive for smaller screens */
@media (max-width: 768px) {
    header {
        flex-direction: column;
        text-align: center;
    }

    header nav {
        margin-top: 10px;
        flex-direction: column;
        gap: 10px;
    }
}



    .container {
      max-width: 700px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: scale(0.95);}
      to {opacity: 1; transform: scale(1);}
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color:rgb(99, 19, 148);
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }

    select, input[type="date"], input[type="time"], input[type="text"], textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .step {
      display: none;
    }

    .step.active {
      display: block;
    }

    .buttons {
      margin-top: 25px;
      text-align: center;
    }

    button {
      background-color:rgb(101, 20, 163);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color:rgb(165, 78, 219);
    }

    .error {
      color: red;
      margin-bottom: 15px;
    }

    .success {
      color: green;
      margin-bottom: 15px;
      font-weight: bold;
    }

    #serviceDetails, #workerDetails {
      background-color: #f1f1f1;
      padding: 10px;
      border-radius: 6px;
      margin-top: 10px;
    }

    #workerDetails img {
      max-width: 80px;
      height: auto;
      border-radius: 8px;
    }

    @media screen and (max-width: 768px) {
      .container {
        margin: 20px;
        padding: 20px;
      }

      header, footer {
        font-size: 16px;
        padding: 10px;
      }

      button {
        width: 100%;
        margin-bottom: 10px;
      }
    }

    /* Footer Styling */
footer {
    background: #630675;
    color: white;
    text-align: center;
    padding: 2em;
    font-size: 14px;
}

/* Responsive font and spacing */
@media (max-width: 768px) {
    footer {
        font-size: 13px;
        padding: 12px 15px;
    }
}


</style>
</head>
<body>

<header>
    <h1>Book Services</h1>
    <nav>
        <a href="CustomerDashboard1.php">Dashboard</a>
       <a href="CustomerBookings.php">My Bookings</a>
        <a href="CustomerLogout1.php">Logout</a>
    </nav>
</header>


  

  <?php
    if ($errors) {
        echo '<div class="error">'.implode("<br>", $errors).'</div>';
    }
    if ($success) {
        echo '<div class="success">'.$success.'</div>';
    }
  ?>

  <div class="wrapper">
  <div class="content">

<div class="container">


  <?php
  if ($errors) {
      echo '<div class="error">'.implode("<br>", $errors).'</div>';
  }
  if ($success) {
      echo '<div class="success">'.$success.'</div>';
  }
  ?>

  <form id="bookingForm" method="POST" action="">

    <!-- Step 1: Select Service -->
    <div class="step active" id="step1">
      <div class="form-group">
        <label for="service">Select Service</label>
        <select id="service" name="service" required>
          <option value="">-- Select Service --</option>
          <?php foreach ($services as $service): ?>
            <option value="<?= $service['service_id'] ?>"><?= htmlspecialchars($service['service_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <div id="serviceDetails" style="background:#f0f0f0; padding:10px; margin-top:10px; border-radius:5px; display:none;">
  <h3>Service Details</h3>
  <p><strong>Description:</strong> <span id="desc"></span></p>
  <p><strong>Price:</strong> LKR <span id="price"></span></p>
  <p><strong>Service Rate:</strong> <span id="rate"></span></p>
  <p><strong>Duration:</strong> <span id="duration"></span></p>
  <p><strong>Rules:</strong> <span id="rules"></span></p>
</div>

      </div>
      <div class="buttons">
        <button type="button" id="next1">Next</button>
      </div>
    </div>

    <!-- Step 2: Select Worker -->
    <div class="step" id="step2">
      <div class="form-group">
        <label for="worker">Select Worker</label>
        <select id="worker" name="worker" required>
          <option value="">-- Select Worker --</option>
        </select>
        <div id="workerDetails" style="display:none; border: 1px solid #ccc; padding: 10px; margin-top: 10px;">
  <h4>Worker Details</h4>
  <img id="workerPic" src="" alt="Profile Picture" width="100" style="border-radius: 10px;"><br>
  <p><strong>Name:</strong> <span id="workerName"></span></p>
  <p><strong>Phone:</strong> <span id="workerPhone"></span></p>
  <p><strong>Average Rating:</strong> <span id="workerRating"></span></p>
  </div>


      </div>
      <div class="buttons">
        <button type="button" id="prev2">Previous</button>
        <button type="button" id="next2">Next</button>
      </div>
    </div>

    <!-- Step 3: Date & Time -->
    <div class="step" id="step3">
      <div class="form-group">
        <label for="date">Select Date</label>
        <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-group">
        <label for="time">Select Time</label>
        <input type="time" id="time" name="time" required>
      </div>
      <div class="form-group">
  <label for="service_address">Service Address</label>
  <input type="text" id="service_address" name="service_address" required>
</div>

<div class="form-group">
  <label for="city">City</label>
  <input type="text" id="city" name="city" required>
</div>

<div class="form-group">
  <label for="postal_code">Postal Code</label>
  <input type="text" id="postal_code" name="postal_code" required>
</div>


<div class="form-group">
  <label for="customer_note">Customer Note (optional)</label>
  <textarea id="customer_note" name="customer_note" rows="3" placeholder="Any instructions for the worker..."></textarea>
</div>

      <div class="buttons">
        <button type="button" id="prev3">Previous</button>
        <button type="button" id="next3">Next</button>
      </div>
    </div>

    <!-- Step 4: Payment Method -->
    <div class="step" id="step4">
      <div class="form-group">
        <label>Payment Method</label>
        <div class="payment-methods">
          <label>
            <input type="radio" name="payment_method" value="Cash on Service" required> Cash on Service
          </label>
          <label>
            <input type="radio" name="payment_method" value="Card" required> Card
            <!-- Sri Lankan card images -->
            <img src="images/visa.png" alt="Visa" title="Visa">
            <img src="images/mastercard.png" alt="MasterCard" title="MasterCard">
            <img src="images/amex.png" alt="American Express" title="American Express">
            <img src="images/slt_card.png" alt="SLT Card" title="SLT Card"> <!-- example local card -->
          </label>
        </div>
      </div>
      <div class="buttons">
        <button type="button" id="prev4">Previous</button>
        <button type="submit" id="submitBtn">Submit</button>
      </div>
    </div>
  </form>
</div>
          </div>
          </div>

<!--<footer>
    &copy; <?= date("Y") ?> SPI Home Service Management System. All rights reserved.
</footer>-->

<script>
  // Multi-step form JS
  let currentStep = 1;
  const totalSteps = 4;

  function showStep(step) {
    document.querySelectorAll('.step').forEach((el) => el.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
  }

  // Next buttons
  document.getElementById('next1').addEventListener('click', () => {
    const service = document.getElementById('service').value;
    if (!service) {
      alert('Please select a service.');
      return;
    }
    // Load workers for the selected service
    fetch('BookServices.php?action=get_workers&service_name=' + encodeURIComponent(document.querySelector('#service option:checked').text))
      .then(res => res.json())
      .then(data => {
        const workerSelect = document.getElementById('worker');
        workerSelect.innerHTML = '<option value="">-- Select Worker --</option>';
        data.forEach(worker => {
          const option = document.createElement('option');
          option.value = worker.worker_id;
          option.textContent = worker.fullname;
          workerSelect.appendChild(option);
        });
        currentStep = 2;
        showStep(currentStep);
      })
      .catch(() => {
        alert('Failed to load workers.');
      });
  });

  // Next from worker selection
  document.getElementById('next2').addEventListener('click', () => {
    const worker = document.getElementById('worker').value;
    if (!worker) {
      alert('Please select a worker.');
      return;
    }
    currentStep = 3;
    showStep(currentStep);
  });

  // Next from date & time
  document.getElementById('next3').addEventListener('click', () => {
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    if (!date || !time) {
      alert('Please select date and time.');
      return;
    }
    currentStep = 4;
    showStep(currentStep);
  });

  // Previous buttons
  document.getElementById('prev2').addEventListener('click', () => {
    currentStep = 1;
    showStep(currentStep);
  });
  document.getElementById('prev3').addEventListener('click', () => {
    currentStep = 2;
    showStep(currentStep);
  });
  document.getElementById('prev4').addEventListener('click', () => {
    currentStep = 3;
    showStep(currentStep);
  });
  // After service select, fetch and show service details
document.getElementById('service').addEventListener('change', function() {
  const serviceId = this.value;
  if (!serviceId) {
    document.getElementById('serviceDetails').style.display = 'none';
    return;
  }
  fetch('BookServices.php?action=get_service_details&service_id=' + serviceId)
    .then(res => res.json())
    .then(data => {
      if (data) {
        document.getElementById('desc').textContent = data.description || 'N/A';
        document.getElementById('price').textContent = data.price || 'N/A';
        document.getElementById('rate').textContent = data.service_rate || 'N/A';
        document.getElementById('duration').textContent = data.service_duration || 'N/A';
        document.getElementById('rules').textContent = data.service_rules || 'N/A';
        document.getElementById('serviceDetails').style.display = 'block';
      } else {
        document.getElementById('serviceDetails').style.display = 'none';
      }
    });
});

// Modify next1 button event to fetch workers, show details
document.getElementById('next1').addEventListener('click', () => {
  const serviceSelect = document.getElementById('service');
  const serviceId = serviceSelect.value;
  const serviceName = serviceSelect.options[serviceSelect.selectedIndex].text;

  if (!serviceId) {
    alert('Please select a service.');
    return;
  }

  // Fetch workers for selected service skill
  fetch('BookServices.php?action=get_workers&service_name=' + encodeURIComponent(serviceName))
    .then(res => res.json())
    .then(data => {
      const workerSelect = document.getElementById('worker');
      workerSelect.innerHTML = '<option value="">-- Select Worker --</option>';
      data.forEach(worker => {
        const option = document.createElement('option');
        option.value = worker.worker_id;
        option.textContent = worker.fullname;
        workerSelect.appendChild(option);
      });

      // Hide worker details section initially
      document.getElementById('workerDetails').style.display = 'none';

      currentStep = 2;
      showStep(currentStep);
    })
    .catch(() => {
      alert('Failed to load workers.');
    });
});

// After worker select, fetch and show worker details
document.getElementById('worker').addEventListener('change', function() {
  const workerId = this.value;
  if (!workerId) {
    document.getElementById('workerDetails').style.display = 'none';
    return;
  }

  fetch('BookServices.php?action=get_worker_details&worker_id=' + workerId)
    .then(res => res.json())
    .then(data => {
      if (data) {
        document.getElementById('workerName').textContent = data.fullname || 'N/A';
        document.getElementById('workerPhone').textContent = data.phone || 'N/A';
        document.getElementById('workerRating').textContent = data.avg_rating || '0';

           if (data.profile_picture) {
                    document.getElementById('workerPic').src = data.profile_picture;

                    document.getElementById('workerPic').style.display = 'block';
                } else {
                    document.getElementById('workerPic').style.display = 'none';
                }
        
        document.getElementById('workerDetails').style.display = 'block';
      } else {
        document.getElementById('workerDetails').style.display = 'none';
      }
    });
});

//optional 
  document.getElementById("next1").addEventListener("click", function() {
    document.getElementById("step1").classList.remove("active");
    document.getElementById("step2").classList.add("active");
  });
  document.getElementById("prev2").addEventListener("click", function() {
    document.getElementById("step2").classList.remove("active");
    document.getElementById("step1").classList.add("active");
  });
  document.getElementById("next2").addEventListener("click", function() {
    document.getElementById("step2").classList.remove("active");
    document.getElementById("step3").classList.add("active");
  });
</script>

</body>
</html>
