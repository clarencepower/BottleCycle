<?php
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu"; // Replace with your database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the latest bin status
$sql = "SELECT is_full FROM bin_status ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$response = [
    "status" => "error",
    "is_full" => null
];

if ($result && $row = $result->fetch_assoc()) {
    $response["status"] = "success";
    $response["is_full"] = $row['is_full'];
}

$conn->close();

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
