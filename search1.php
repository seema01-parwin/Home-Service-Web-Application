<?php
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

$searchQuery = "";
if (isset($_GET['query'])) {
  $searchQuery = $conn->real_escape_string($_GET['query']);
}

$sql = "SELECT * FROM services WHERE service_name LIKE '%$searchQuery%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>

* {
  font-family: 'Poppins', sans-serif;
}

/* Global styles */
body {
  margin: 0;
  background: #fbecff;
  color: #333;
}

/* Header */
header {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  background: #630675;
  color: white;
  padding: 20px 20px;
}

/* Navigation */
nav ul {
  display: flex;
  gap: 20px;
  list-style: none;
  margin: 0;
  padding: 0;
}

nav ul li a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 5px;
}

nav ul li a:hover {
  color: #f3b2ff;
}

/* Features */
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 30px;
  padding: 10px 30px;
  padding-bottom: 45px;
  padding-top: 50px;
}

.feature-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.feature-box:hover{
  background-color:rgb(119, 51, 126);
  color:rgb(255, 255, 255);
}

.feature-box i {
  font-size: 50px;
  color:rgb(234, 115, 245);
}

.feature-box h3 {
  margin: 10px 0;
  font-size: 1.2em;
}

</style>
</head>
<body>

<header>
  <h1>Search Results</h1>
  <nav>
    <ul>
      <li><a href="Home.php" class="logout-button"><i class="fas fa-home"></i> Home</a></li>
    </ul>
  </nav>
</header>

<section class="features">
<?php
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<div class="feature-box">';
    echo '<i class="' . htmlspecialchars($row["icon_class"]) . '"></i>';
    echo '<h3>' . htmlspecialchars($row["service_name"]) . '</h3>';
    echo '<p><strong>Description:</strong> ' . htmlspecialchars($row["description"]) . '</p>';
    echo '<p><strong>Price:</strong> $' . htmlspecialchars($row["price"]) . '</p>';
    echo '<p><strong>Rate Type:</strong> ' . htmlspecialchars($row["service_rate"]) . '</p>';
    echo '<p><strong>Default Duration:</strong> ' . htmlspecialchars($row["service_duration"]) . '</p>';
    echo '<p><strong>Service Rules:</strong> ' . nl2br(htmlspecialchars($row["service_rules"])) . '</p>';
    echo '</div>';
  }
} else {
  echo "<p>No services found matching your search.</p>";
}
?>
</section>

</body>
</html>

<?php
$conn->close();
?>
