<?php
require '../config.php';
// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the bin code from the query string
$binCode = $_GET['binCode'];

// Prepare and execute the delete query
$sql = "DELETE FROM bottle_bins WHERE bin_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $binCode);

if ($stmt->execute()) {
    echo "Bottle bin deleted successfully!";
} else {
    echo "Error deleting bin: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
