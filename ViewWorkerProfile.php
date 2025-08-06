<?php
if (!isset($_GET['worker_id'])) {
    echo "<p>Invalid worker ID.</p>";
    exit();
}

$workerId = intval($_GET['worker_id']); // Fixed: key should be 'worker_id'

$conn = new mysqli("localhost", "root", "", "home_service_db");
if ($conn->connect_error) {
    echo "<p>Database connection failed.</p>";
    exit();
}

// Fetch approved worker info
$stmt = $conn->prepare("SELECT * FROM workers WHERE worker_id = ? AND status = 'Approved'");
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<p>Worker not found or not approved.</p>";
    exit();
}
$worker = $result->fetch_assoc();
$stmt->close();

// Fetch average rating
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE worker_id = ?");
$stmt->bind_param("i", $workerId);
$stmt->execute();
$ratingResult = $stmt->get_result()->fetch_assoc();
$avgRating = round($ratingResult['avg_rating'], 1) ?: "N/A";
$stmt->close();

// Fetch work proofs
$stmt = $conn->prepare("SELECT * FROM worker_proofs WHERE worker_id = ?");
$stmt->bind_param("i", $workerId);
$stmt->execute();
$proofs = $stmt->get_result();
$stmt->close();
?>


<style>
    .profile-section {
        font-family: Arial, sans-serif;
    }
    .profile-section h2 {
        margin-bottom: 10px;
    }
    .profile-info img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 2px solid #ddd;
    }
    .info-list {
        margin-top: 10px;
        line-height: 1.6;
    }
    .info-list strong {
        display: inline-block;
        width: 130px;
    }
    .proof-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    .proof-card {
        width: 120px;
        position: relative;
    }
    .proof-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .delete-proof {
        position: absolute;
        top: 2px;
        right: 4px;
        background: red;
        color: #fff;
        font-size: 12px;
        border: none;
        border-radius: 3px;
        padding: 2px 4px;
        cursor: pointer;
    }

    .proof-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.proof-card {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 8px;
    width: 200px;
    background: #f9f9f9;
    position: relative;
}
.delete-proof {
    position: absolute;
    top: 5px;
    right: 5px;
    background: red;
    color: white;
    border: none;
    padding: 4px 8px;
    cursor: pointer;
    border-radius: 4px;
}

</style>

<div class="profile-section">
    <h2><?= htmlspecialchars($worker['fullname']) ?> - Profile</h2>
    <div class="profile-info">
    <img src="<?php echo htmlspecialchars($worker['profile_picture'] ?? 'Image/Icons_default.png'); ?>" width="150" height="150">
        <div class="info-list">
            <p><strong>Email:</strong> <?= htmlspecialchars($worker['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($worker['phone']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($worker['skill']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($worker['address']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($worker['status'])) ?></p>
            <p><strong>Rating:</strong> <?= $avgRating ?> ⭐</p>
        </div>
    </div>

<h3>Work Proofs</h3>
<div class="proof-gallery">
    <?php while ($proof = $proofs->fetch_assoc()): ?>
        <div class="proof-card">
            <img src="<?= htmlspecialchars($proof['image_path']) ?>" alt="Proof" style="width:100%; max-height:200px; object-fit:cover;">
            <form method="POST" action="WorkerAction.php" onsubmit="return confirm('Delete this proof?');">
                <input type="hidden" name="action" value="delete_proof">
                <input type="hidden" name="proof_id" value="<?= $proof['id'] ?>">
                <input type="hidden" name="worker_id" value="<?= $workerId ?>">
                <button type="submit" class="delete-proof">X</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

