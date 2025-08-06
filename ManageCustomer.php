<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AdminLogin1.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_service_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search, Sorting, and Pagination handling
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "created_at";
$order = isset($_GET['order']) && $_GET['order'] === "asc" ? "ASC" : "DESC";
$validSortColumns = ['name', 'email', 'created_at'];
if (!in_array($sort, $validSortColumns)) {
    $sort = 'created_at';
}
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Prepare query with search, sorting, and pagination
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE name LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%') ORDER BY $sort $order LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $search, $search, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM customers ORDER BY $sort $order LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Count total rows for pagination
if (!empty($search)) {
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM customers WHERE name LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%')");
    $countStmt->bind_param("ss", $search, $search);
} else {
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM customers");
}



$countStmt->execute();
$totalCount = $countStmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($totalCount / $limit);
$countStmt->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <!-- Include Bootstrap CSS and jQuery -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #fbecff;
        color: #333;
    }

    h2 {
        font-weight: 600;
        color:rgb(72, 44, 80);
    }

    .btn {
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .form-inline input.form-control {
        width: 250px;
        max-width: 100%;
    }

    table {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    th {
        background-color:rgb(74, 52, 94);
        color: #fff;
        text-align: center;
        vertical-align: middle !important;
    }

    td {
        vertical-align: middle !important;
        text-align: center;
    }

    .table td, .table th {
        border: 1px solid #dee2e6;
        padding: 12px;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: capitalize;
        display: inline-block;
        animation: fadeIn 0.4s ease-in;
    }

    .status-badge.active {
        background-color: #2ecc71;
        color: white;
    }

    .status-badge.inactive {
        background-color: #e74c3c;
        color: white;
    }

    .page-item.active .page-link {
        background-color:rgb(69, 44, 80);
        border-color:rgb(63, 44, 80);
        color: white;
    }

    .page-link {
        color:rgb(66, 44, 80);
    }

    .page-link:hover {
        background-color: #f0f0f0;
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        background-color:rgb(63, 44, 80);
        color: #fff;
    }

    .modal-body img {
        border: 3px solidrgb(152, 52, 219);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    tr {
        animation: fadeIn 0.4s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .form-inline input.form-control {
            width: 100%;
            margin-bottom: 10px;
        }
        .form-inline {
            flex-direction: column;
            align-items: stretch;
        }
        .d-flex {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Customers</h2>
        <div>
            <a href="ExportCustomers.php" class="btn btn-info">Export CSV</a>
            <a href="AdminDashboard1.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <form method="GET" action="ManageCustomer.php" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <form method="POST" action="BulkDelete.php" id="bulkDeleteForm">
        <button type="submit" class="btn btn-danger mb-3" onclick="return confirm('Are you sure you want to delete selected customers?');">Delete Selected</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th><a href="?sort=name&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">Full Name</a></th>
                    <th><a href="?sort=email&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">Email</a></th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th><a href="?sort=created_at&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">Registered On</a></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $count = ($page - 1) * $limit + 1;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusBadge = "<span class='status-badge " . ($row['status'] === 'active' ? "active" : "inactive") . "'>" . ucfirst($row['status']) . "</span>";
                    echo "<tr>
                        <td><input type='checkbox' name='customer_ids[]' value='{$row['customer_id']}'></td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                        <td>$statusBadge</td>
                        <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                        <td>
                            <button type='button' class='btn btn-info btn-sm viewBtn' data-id='{$row['customer_id']}'>View</button>
                            <a href='EditCustomer.php?customer_id={$row['customer_id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='ToggleStatus.php?customer_id={$row['customer_id']}' class='btn btn-secondary btn-sm'>" . ($row['status'] === 'active' ? 'Deactivate' : 'Activate') . "</a>
                            <a href='DeleteCustomer.php?customer_id={$row['customer_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this customer?');\">Delete</a>
                        </td>
                    </tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='7'>No customers found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </form>

    <!-- Pagination Links -->
    <nav>
        <ul class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $activeClass = ($i == $page) ? "active" : "";
                echo "<li class='page-item $activeClass'><a class='page-link' href='?page=$i&search=" . urlencode($search) . "&sort=$sort&order=$order'>$i</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span id="customerName"></span>'s Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <div class="text-center mb-3">
        <img id="customerImage" src="" alt="Profile Picture" class="rounded-circle" width="120" height="120">
    </div>
    <p><strong>Email:</strong> <span id="customerEmail"></span></p>
    <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
    <p><strong>Address:</strong> <span id="customerAddress"></span></p>
    <p><strong>Status:</strong> <span id="customerStatus"></span></p>
    <p><strong>Registered On:</strong> <span id="customerRegistered"></span></p>
    </div>

    </div>
  </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script>
    // Select/Deselect all checkboxes
    $('#selectAll').click(function() {
        $('input[name="ids[]"]').prop('checked', this.checked);
    });

    // View customer details in modal
    $('.viewBtn').click(function() {
        var customerId = $(this).data('id');
        $.ajax({
            url: 'GetCustomerDetails.php',
            type: 'GET',
            data: { id: customerId },
            success: function(data) {
    var customer = JSON.parse(data);
    $('#customerName').text(customer.name);
    $('#customerEmail').text(customer.email);
    $('#customerPhone').text(customer.phone);
    $('#customerStatus').text(customer.status);
    $('#customerRegistered').text(customer.created_at);
    $('#customerAddress').text(customer.address);
    
    if (customer.profile_picture) {
        $('#customerImage').attr('src', 'Image/' + customer.profile_picture);
    } else {
        $('#customerImage').attr('src', 'default-profile.png');
    }

    $('#customerModal').modal('show');
}

        });
    });
</script>

</body>
</html>
