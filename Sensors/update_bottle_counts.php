<?php
$servername = "localhost";
$username = "root";  // Default username for local server
$password = "";      // Default password for local server
$dbname = "bottlecycle-ctu";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from URL parameters
$small_count = isset($_GET['small_count']) ? intval($_GET['small_count']) : null;
$medium_count = isset($_GET['medium_count']) ? intval($_GET['medium_count']) : null;
$large_count = isset($_GET['large_count']) ? intval($_GET['large_count']) : null;
$is_full = isset($_GET['is_full']) ? intval($_GET['is_full']) : null; // Modified to handle as integer

// Insert data based on bottle size
if ($small_count !== null) {
    $stmt = $conn->prepare("INSERT INTO small_bottle_counts (count) VALUES (?)");
    $stmt->bind_param("i", $small_count);
    $stmt->execute();
    $stmt->close();
}

if ($medium_count !== null) {
    $stmt = $conn->prepare("INSERT INTO medium_bottle_counts (count) VALUES (?)");
    $stmt->bind_param("i", $medium_count);
    $stmt->execute();
    $stmt->close();
}

if ($large_count !== null) {
    $stmt = $conn->prepare("INSERT INTO large_bottle_counts (count) VALUES (?)");
    $stmt->bind_param("i", $large_count);
    $stmt->execute();
    $stmt->close();
}

if ($is_full !== null) {
    $stmt = $conn->prepare("INSERT INTO bin_status (is_full) VALUES (?)");
    $stmt->bind_param("i", $is_full); // Insert the integer status value
    $stmt->execute();
    $stmt->close();
}

// Close the database connection
$conn->close();

echo "Data updated successfully";
?>
