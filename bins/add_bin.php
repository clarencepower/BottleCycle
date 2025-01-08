<?php
require '../config.php';
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

$binCode = $data['binCode'];
$address = $data['address'];
$latitude = $data['lat'];
$longitude = $data['lng'];

// Insert bin details into the bottle_bins table
$stmt = $conn->prepare("INSERT INTO bottle_bins (bin_code, address, latitude, longitude) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdd", $binCode, $address, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Bin created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert data"]);
}

$stmt->close();
$conn->close();
?>