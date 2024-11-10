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

// Function to check if the count is new or different before inserting
function insertIfNewCount($conn, $table, $count) {
    // Query the last inserted count for the specific bottle size
    $stmt = $conn->prepare("SELECT count FROM $table ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($last_count);
    $stmt->fetch();
    $stmt->close();

    // Only insert the new count if it's different from the last count
    if ($count !== $last_count) {
        $stmt = $conn->prepare("INSERT INTO $table (count) VALUES (?)");
        $stmt->bind_param("i", $count);
        $stmt->execute();
        $stmt->close();
    }
}

// Insert data for each bottle size if the count has changed
if ($small_count !== null) {
    insertIfNewCount($conn, "small_bottle_counts", $small_count);
}

if ($medium_count !== null) {
    insertIfNewCount($conn, "medium_bottle_counts", $medium_count);
}

if ($large_count !== null) {
    insertIfNewCount($conn, "large_bottle_counts", $large_count);
}

// Check if the bin status has changed before inserting
function insertIfNewStatus($conn, $table, $status) {
    $stmt = $conn->prepare("SELECT is_full FROM $table ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($last_status);
    $stmt->fetch();
    $stmt->close();

    // Only insert the new status if it's different from the last status
    if ($status !== $last_status) {
        $stmt = $conn->prepare("INSERT INTO $table (is_full) VALUES (?)");
        $stmt->bind_param("i", $status);
        $stmt->execute();
        $stmt->close();
    }
}

if ($is_full !== null) {
    insertIfNewStatus($conn, "bin_status", $is_full);
}

// Close the database connection
$conn->close();

echo "Data updated successfully";
?>
