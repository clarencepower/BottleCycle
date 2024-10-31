<?php
require '../config.php';
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT bin_code, bin_address FROM bottle_bins";
$result = $conn->query($sql);

$bins = [];
while ($row = $result->fetch_assoc()) {
    $bins[] = $row;
}

echo json_encode($bins);

$conn->close();
?>
