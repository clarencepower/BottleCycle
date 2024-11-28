<?php
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the latest count from each table
$small_result = $conn->query("SELECT small_bottle_counts FROM ctu_0001 ORDER BY timestamp DESC LIMIT 1");
$medium_result = $conn->query("SELECT medium_bottle_counts FROM ctu_0001 ORDER BY timestamp DESC LIMIT 1");
$large_result = $conn->query("SELECT large_bottle_counts FROM ctu_0001 ORDER BY timestamp DESC LIMIT 1");

// Fetch the counts or default to 0 if not available
$small_count = $small_result->fetch_assoc()['small_bottle_counts'] ?? 0;
$medium_count = $medium_result->fetch_assoc()['medium_bottle_counts'] ?? 0;
$large_count = $large_result->fetch_assoc()['large_bottle_counts'] ?? 0;

// Calculate the total
$total_count = $small_count + $medium_count + $large_count;

// Output the results as JSON
echo json_encode([
    "small" => $small_count,
    "medium" => $medium_count,
    "large" => $large_count,
    "total" => $total_count
]);

$conn->close();
?>
