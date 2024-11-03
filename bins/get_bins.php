<?php
require '../config.php';
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT bin_code, address, latitude, longitude FROM bottle_bins";
$result = $conn->query($sql);

$bins = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bins[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($bins);
?>
