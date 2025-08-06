<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: AminLogin1.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "home_service_db";
$conn = new mysqli($host, $user, $pass, $db);

// Delete suspicious work proof
if (isset($_GET['action']) && $_GET['action'] == 'delete_proof' && isset($_GET['proof_id'])) {
    $proofId = intval($_GET['proof_id']);
    $conn->query("DELETE FROM worker_proofs WHERE id = $proofId");
    echo "<script>alert('Proof deleted successfully'); window.location='ManageWorkers.php';</script>";
    exit();
}

// Search filter
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM workers WHERE fullname LIKE CONCAT('%', ?, '%') OR skill LIKE CONCAT('%', ?, '%') ORDER BY created_at DESC");
    $stmt->bind_param("ss", $search, $search);
} else {
    $stmt = $conn->prepare("SELECT * FROM workers ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Workers</title>
    <style>
    /* General reset and background */
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color:rgb(254, 242, 255);
        background-size: cover;
        color: #333;
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .container {
        max-width: 1100px;
        margin: 60px auto;
        background: rgba(255, 255, 255, 0.97);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        animation: fadeIn 1.2s ease-in-out;
    }

    h1 {
        text-align: center;
        color:rgb(72, 44, 80);
        margin-bottom: 20px;
    }

    /* Search bar */
    .search-bar {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .search-bar input[type="text"] {
        padding: 10px;
        width: 280px;
        border-radius: 6px 0 0 6px;
        border: 1px solid #ccc;
    }

    .search-bar button {
        padding: 10px 16px;
        border: none;
        background-color:rgb(130, 41, 185);
        color: white;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        transition: background 0.3s;
    }

    .search-bar button:hover {
        background-color:rgb(93, 31, 141);
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color:rgb(64, 44, 80);
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .action-btns a {
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 13px;
        margin-right: 5px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .view-btn    { background-color: #8e44ad; }
    .edit-btn    { background-color: #2980b9; }
    .delete-btn  { background-color: #e74c3c; }
    .approve-btn { background-color: #27ae60; }
    .reject-btn  { background-color: #c0392b; }

    .action-btns a:hover {
        transform: scale(1.05);
        opacity: 0.9;
    }

    .status-approved {
        color: green;
        font-weight: bold;
    }

    .status-pending {
        color: orange;
        font-weight: bold;
    }

    /* Modal styling */
    #profileModal {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        overflow: auto;
        animation: fadeIn 0.5s ease-in;
    }

    #modalContent {
        background-color: #fff;
        margin: 5% auto;
        padding: 30px;
        width: 90%;
        max-width: 600px;
        border-radius: 10px;
        position: relative;
        animation: fadeIn 0.8s ease;
        max-height: 80vh;
        overflow-y: auto;
    }

    #modalClose {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
    }

    /* Go back button */
    .top-btns {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
    }

    .top-btns a {
        background-color:rgb(80, 52, 94);
        color: white;
        padding: 10px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s, transform 0.2s;
    }

    .top-btns a:hover {
        background-color:rgb(71, 44, 80);
        transform: scale(1.05);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-bar {
            flex-direction: column;
            align-items: center;
        }

        .search-bar input[type="text"], .search-bar button {
            width: 100%;
            border-radius: 6px;
            margin: 5px 0;
        }

        .action-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        th, td {
            font-size: 14px;
        }

        .top-btns {
            justify-content: center;
        }
    }
</style>

</head>
<body>

<div class="container">
    <div class="top-btns">
        <a href="AdminDashboard1.php">← Back to Dashboard</a>
    </div>
    <h1>Manage Workers</h1>

    <div class="search-bar">
        <form method="GET" action="ManageWorkers.php">
            <input type="text" name="search" placeholder="Search by name or skill" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Skill</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusClass = ($row['status'] === 'Approved') ? "status-approved" : "status-pending";
                echo "<tr>
                    <td>{$count}</td>
                    <td>" . htmlspecialchars($row['fullname']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['skill']) . "</td>
                    <td class='$statusClass'>" . ucfirst($row['status']) . "</td>
                    <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                    <td class='action-btns'>
                        <a class='view-btn' href='#' onclick='viewProfile(" . $row['worker_id'] . ")'>View</a>";
                
                if ($row['status'] === 'Pending') {
                    echo "<a class='approve-btn' href='WorkerAction.php?action=approve&worker_id={$row['worker_id']}'>Approve</a>";
                    echo "<a class='reject-btn' href='WorkerAction.php?action=reject&worker_id={$row['worker_id']}' onclick=\"return confirm('Reject and delete this worker?');\">Reject</a>";
                }

                echo "<a class='edit-btn' href='WorkerAction.php?action=edit&worker_id={$row['worker_id']}'>Edit</a>
                      <a class='delete-btn' href='WorkerAction.php?action=delete&worker_id={$row['worker_id']}' onclick=\"return confirm('Are you sure you want to delete this worker?');\">Delete</a>
                    </td>
                </tr>";
                $count++;
            }
        } else {
            echo "<tr><td colspan='7'>No workers found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal Popup -->
<div id="profileModal">
    <div id="modalContent">
        <span id="modalClose" onclick="document.getElementById('profileModal').style.display='none'">&times;</span>
        <div id="profileContent">Loading...</div>
    </div>
</div>

<script>
function viewProfile(workerId) {
    fetch('ViewWorkerProfile.php?worker_id=' + workerId)
    .then(res => res.text())
    .then(html => {
        document.getElementById('profileContent').innerHTML = html;
        document.getElementById('profileModal').style.display = 'block';
    });
}
</script>

</body>
</html>
