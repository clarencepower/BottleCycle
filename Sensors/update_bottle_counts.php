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
function insertIfNewCount($conn, $small_count, $medium_count, $large_count) {
    // Query the last inserted values for the counts
    $stmt = $conn->prepare("SELECT small_bottle_counts, medium_bottle_counts, large_bottle_counts FROM CTU_0001 ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($last_small_count, $last_medium_count, $last_large_count);
    $stmt->fetch();
    $stmt->close();

    // Only insert the new counts if they differ from the last values
    if ($small_count !== $last_small_count || $medium_count !== $last_medium_count || $large_count !== $last_large_count) {
        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO CTU_0001 (small_bottle_counts, medium_bottle_counts, large_bottle_counts) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $small_count, $medium_count, $large_count);
        $stmt->execute();
        $stmt->close();
    }
}

// Insert data if it's different from the last recorded values
if ($small_count !== null || $medium_count !== null || $large_count !== null) {
    insertIfNewCount($conn, $small_count, $medium_count, $large_count);
}

// Function to check and insert bin status if it has changed
function insertIfNewStatus($conn, $status) {
    // Query the last inserted bin status
    $stmt = $conn->prepare("SELECT is_full FROM bin_status ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($last_status);
    $stmt->fetch();
    $stmt->close();

    // Only insert the new status if it's different from the last status
    if ($status !== $last_status) {
        // Prepare the insert statement for bin status
        $stmt = $conn->prepare("INSERT INTO bin_status (is_full) VALUES (?)");
        $stmt->bind_param("i", $status);
        $stmt->execute();
        $stmt->close();
    }
}

// Insert bin status if it has changed
if ($is_full !== null) {
    insertIfNewStatus($conn, $is_full);
}

// Close the database connection
$conn->close();

echo "Data updated successfully";
?>
