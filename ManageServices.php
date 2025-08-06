<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Add Service
if (isset($_POST['add_service'])) {
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $service_rate = $conn->real_escape_string($_POST['service_rate']);
    $service_duration = $conn->real_escape_string($_POST['service_duration']);
    $service_rules = $conn->real_escape_string($_POST['service_rules']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);

    $sql = "INSERT INTO services (service_name, description, price, service_rate, service_duration, service_rules, icon_class)
            VALUES ('$service_name', '$description', $price, '$service_rate', '$service_duration', '$service_rules', '$icon_class')";
    if ($conn->query($sql)) {
        $message = "Service added successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Edit Service
if (isset($_POST['edit_service'])) {
    $service_id = intval($_POST['service_id']);
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $service_rate = $conn->real_escape_string($_POST['service_rate']);
    $service_duration = $conn->real_escape_string($_POST['service_duration']);
    $service_rules = $conn->real_escape_string($_POST['service_rules']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);

    $sql = "UPDATE services SET 
                service_name='$service_name',
                description='$description',
                price=$price,
                service_rate='$service_rate',
                service_duration='$service_duration',
                service_rules='$service_rules',
                icon_class='$icon_class'
            WHERE service_id=$service_id";
    if ($conn->query($sql)) {
        $message = "Service updated successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Delete Service
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $sql = "DELETE FROM services WHERE service_id=$delete_id";
    if ($conn->query($sql)) {
        $message = "Service deleted successfully.";
    } else {
        $error = "Error deleting service: " . $conn->error;
    }
}

// Filter/Search
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Fetch services with optional search filter
$sql = "SELECT * FROM services WHERE service_name LIKE '%$searchQuery%' ORDER BY service_name ASC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Services</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
 /* Reset and base styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
  background: #fbecff;
  padding: 20px;
  color: #333;
}

/* Headings */
h1 {
  font-size: 32px;
  margin-bottom: 20px;
  color: #222;
  animation: fadeInDown 0.5s ease;
}

/* Buttons */
.btn {
  background-color:rgb(161, 116, 177);
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}
.btn:hover {
  background-color:rgb(84, 32, 105);
  transform: scale(1.05);
}
.btn-danger {
  background-color: #dc3545;
}
.btn-danger:hover {
  background-color: #b02a37;
}

/* Message styles */
.message, .error {
  padding: 10px 15px;
  margin-bottom: 15px;
  border-radius: 5px;
  font-weight: 500;
}
.message {
  background-color: #d4edda;
  color: #155724;
}
.error {
  background-color: #f8d7da;
  color: #721c24;
}

/* Search bar */
.search-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
  align-items: center;
}
.search-bar input[type="text"] {
  flex: 1;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  min-width: 220px;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  border-radius: 8px;
  overflow: hidden;
  animation: fadeInUp 0.5s ease;
}
th, td {
  text-align: left;
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
}
th {
  background-color:rgb(81, 37, 120);
  color: white;
}
td i {
  color: #555;
}
.btn-group {
  display: flex;
  gap: 8px;
}
.inline {
  display: inline;
}

/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
  animation: fadeIn 0.3s ease;
}
.modal-content {
  background-color: #fff;
  margin: auto;
  padding: 25px;
  border: 1px solid #ccc;
  width: 95%;
  max-width: 600px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  animation: slideIn 0.4s ease;
}
.modal-content h2 {
  margin-bottom: 20px;
  color: #333;
}
.modal-content label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
}
.modal-content input, 
.modal-content textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
.modal-content button {
  margin-top: 10px;
}

/* Close icon */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  cursor: pointer;
}
.close:hover {
  color: #000;
}

/* Responsive */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  thead {
    display: none;
  }
  tr {
    margin-bottom: 15px;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border-radius: 8px;
    padding: 10px;
  }
  td {
    padding: 10px;
    position: relative;
  }
  td::before {
    content: attr(data-label);
    font-weight: bold;
    display: block;
    margin-bottom: 4px;
    color: #555;
  }
}

/* Animations */
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
  from {opacity: 0}
  to {opacity: 1}
}
@keyframes slideIn {
  from { transform: translateY(-50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

</style>
</head>
<body>

<button onclick="window.location.href='AdminDashboard1.php'" class="btn" style="margin-bottom: 20px;">
  ← Go Back to Dashboard
</button>


<h1>Manage Services</h1>

<?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
<?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

<!-- Search Form -->
<form method="get" class="search-bar">
    <input type="text" name="search" placeholder="Search by Service Name" value="<?= htmlspecialchars($searchQuery) ?>" />
    <button type="submit" class="btn"><i class="fas fa-search"></i> Search</button>
    <button type="button" class="btn" id="addServiceBtn" style="margin-left:auto;"><i class="fas fa-plus"></i> Add Service</button>
</form>

<!-- Services Table -->
<table>
    <thead>
        <tr>
            <th>Icon</th>
            <th>Service Name</th>
            <th>Description</th>
            <th>Price ($)</th>
            <th>Rate Type</th>
            <th>Duration</th>
            <th>Rules</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><i class="<?= htmlspecialchars($row['icon_class']) ?>" style="font-size: 24px;"></i></td>
            <td><?= htmlspecialchars($row['service_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= htmlspecialchars($row['service_rate']) ?></td>
            <td><?= htmlspecialchars($row['service_duration']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['service_rules'])) ?></td>
            <td>
                <div class="btn-group">
                    <button class="btn editBtn"
                    data-id="<?= $row['service_id'] ?>"
                    data-name="<?= htmlspecialchars($row['service_name']) ?>"
                    data-desc="<?= htmlspecialchars($row['description']) ?>"
                    data-price="<?= $row['price'] ?>"
                    data-rate="<?= htmlspecialchars($row['service_rate']) ?>"
                    data-duration="<?= htmlspecialchars($row['service_duration']) ?>"
                    data-rules="<?= htmlspecialchars($row['service_rules']) ?>"
                    data-icon="<?= htmlspecialchars($row['icon_class']) ?>"
                    >
                    <i class="fas fa-edit"></i>
                    </button>

                    <form method="get" action="" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
                    <input type="hidden" name="delete" value="<?= $row['service_id'] ?>">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center;">No services found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Add Service Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <span class="close" id="addClose">&times;</span>
    <h2>Add New Service</h2>
    <form method="post" action="">
      <label for="service_name">Service Name</label>
      <input type="text" id="service_name" name="service_name" required>

      <label for="description">Description</label>
      <textarea id="description" name="description" rows="3" required></textarea>

      <label for="price">Price ($)</label>
      <input type="number" id="price" name="price" step="0.01" min="0" required>

      <label for="service_rate">Service Rate (e.g. Per Hour, Per Visit)</label>
      <input type="text" id="service_rate" name="service_rate" required>

      <label for="service_duration">Service Duration</label>
      <input type="text" id="service_duration" name="service_duration" required>

      <label for="service_rules">Service Rules</label>
      <textarea id="service_rules" name="service_rules" rows="3" required></textarea>

      <label for="icon_class">Icon Class (FontAwesome)</label>
      <input type="text" id="icon_class" name="icon_class" placeholder="e.g. fa-solid fa-bolt" required>

      <button type="submit" class="btn" name="add_service">Add Service</button>
    </form>
  </div>
</div>

<!-- Edit Service Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" id="editClose">&times;</span>
    <h2>Edit Service</h2>
    <form method="post" action="">
      <input type="hidden" id="edit_service_id" name="service_id">

      <label for="edit_service_name">Service Name</label>
      <input type="text" id="edit_service_name" name="service_name" required>

      <label for="edit_description">Description</label>
      <textarea id="edit_description" name="description" rows="3" required></textarea>

      <label for="edit_price">Price ($)</label>
      <input type="number" id="edit_price" name="price" step="0.01" min="0" required>

      <label for="edit_service_rate">Service Rate (e.g. Per Hour, Per Visit)</label>
      <input type="text" id="edit_service_rate" name="service_rate" required>

      <label for="edit_service_duration">Service Duration</label>
      <input type="text" id="edit_service_duration" name="service_duration" required>

      <label for="edit_service_rules">Service Rules</label>
      <textarea id="edit_service_rules" name="service_rules" rows="3" required></textarea>

      <label for="edit_icon_class">Icon Class (FontAwesome)</label>
      <input type="text" id="edit_icon_class" name="icon_class" required>

      <button type="submit" class="btn" name="edit_service">Update Service</button>
    </form>
  </div>
</div>

<script>
    // Modal handling
    var addModal = document.getElementById("addModal");
    var addBtn = document.getElementById("addServiceBtn");
    var addClose = document.getElementById("addClose");

    addBtn.onclick = function() {
      addModal.style.display = "block";
    }
    addClose.onclick = function() {
      addModal.style.display = "none";
    }

    // Edit modal
    var editModal = document.getElementById("editModal");
    var editClose = document.getElementById("editClose");

    editClose.onclick = function() {
      editModal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == addModal) {
        addModal.style.display = "none";
      }
      if (event.target == editModal) {
        editModal.style.display = "none";
      }
    }

    // Fill edit modal with data
    var editButtons = document.querySelectorAll(".editBtn");
    editButtons.forEach(function(btn) {
        btn.addEventListener("click", function() {
            document.getElementById("edit_service_id").value = this.dataset.id;
            document.getElementById("edit_service_name").value = this.dataset.name;
            document.getElementById("edit_description").value = this.dataset.desc;
            document.getElementById("edit_price").value = this.dataset.price;
            document.getElementById("edit_service_rate").value = this.dataset.rate;
            document.getElementById("edit_service_duration").value = this.dataset.duration;
            document.getElementById("edit_service_rules").value = this.dataset.rules;
            document.getElementById("edit_icon_class").value = this.dataset.icon;
            editModal.style.display = "block";
        });
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
