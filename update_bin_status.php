<?php
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get bin status (1 or 0) from the request
$binFull = isset($_GET['is_full']) ? intval($_GET['is_full']) : 0;

// Insert bin status into bin_status table
$stmt = $conn->prepare("INSERT INTO bin_status (is_full) VALUES (?)");
$stmt->bind_param("i", $binFull);
$stmt->execute();
$stmt->close();

$conn->close();

echo "Bin status updated successfully";
?>
