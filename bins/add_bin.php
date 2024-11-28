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

$stmt = $conn->prepare("INSERT INTO bottle_bins (bin_code, address, latitude, longitude) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdd", $binCode, $address, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
    $tableName = "bin_" . strtoupper($binCode); // Table name based on bin code
$sql = "CREATE TABLE IF NOT EXISTS $tableName (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    small_bottle_counts INT(11) DEFAULT NULL,
    medium_bottle_counts INT(11) DEFAULT NULL,
    large_bottle_counts INT(11) DEFAULT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)";
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert data"]);
}
// SQL to create a new table based on bin code



$stmt->close();
$conn->close();
?>
