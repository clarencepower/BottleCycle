<?php
require '../config.php';
// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$binCode = $_POST['binCode'];
$binAddress = $_POST['binAddress'];
$labelStatus = $_POST['labelStatus'];

// Insert the new bin into the database
$sql = "INSERT INTO bottle_bins (bin_code, bin_address, label_status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $binCode, $binAddress, $labelStatus);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Bottle bin added successfully!";
?>
