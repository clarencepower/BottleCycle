<?php
// Database connection credentials
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name


// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Retrieve data from GET request
$small = isset($_GET['small']) ? (int)$_GET['small'] : 0;
$medium = isset($_GET['medium']) ? (int)$_GET['medium'] : 0;
$large = isset($_GET['large']) ? (int)$_GET['large'] : 0;

$response = array();

// Insert data into respective tables
$sqlSmall = "INSERT INTO small_bottles (count) VALUES ($small)";
$sqlMedium = "INSERT INTO medium_bottles (count) VALUES ($medium)";
$sqlLarge = "INSERT INTO large_bottles (count) VALUES ($large)";

// Execute the queries and check for success
if ($conn->query($sqlSmall) === TRUE) {
    $response['small'] = "Small bottle data inserted successfully";
} else {
    $response['small'] = "Error: " . $conn->error;
}

if ($conn->query($sqlMedium) === TRUE) {
    $response['medium'] = "Medium bottle data inserted successfully";
} else {
    $response['medium'] = "Error: " . $conn->error;
}

if ($conn->query($sqlLarge) === TRUE) {
    $response['large'] = "Large bottle data inserted successfully";
} else {
    $response['large'] = "Error: " . $conn->error;
}

// Output response in JSON format for debugging
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
