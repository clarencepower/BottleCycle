<?php
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest counts for each bottle size
$sqlSmall = "SELECT count FROM small_bottle_counts ORDER BY id DESC LIMIT 1";
$sqlMedium = "SELECT count FROM medium_bottle_counts ORDER BY id DESC LIMIT 1";
$sqlLarge = "SELECT count FROM large_bottle_counts ORDER BY id DESC LIMIT 1";

$smallCount = $conn->query($sqlSmall)->fetch_assoc()['count'] ?? 0;
$mediumCount = $conn->query($sqlMedium)->fetch_assoc()['count'] ?? 0;
$largeCount = $conn->query($sqlLarge)->fetch_assoc()['count'] ?? 0;

$totalCount =  $smallCount + $mediumCount + $largeCount ;
$conn->close();

// Return data in JSON format
echo json_encode([
    'small' => $smallCount,
    'medium' => $mediumCount,
    'large' => $largeCount,
    'total' => $totalCount
]);
?>
