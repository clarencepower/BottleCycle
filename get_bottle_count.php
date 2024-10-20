<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the most recent count from the database based on the latest ID
$sql = "SELECT count FROM bottle_counts ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestCount = $row['count'];
    echo $latestCount;
} else {
    echo "0"; // If no data found, return 0
}

$conn->close();
?>
