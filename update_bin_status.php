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

// Get the bin status from the request (1 if full, 0 if not full)
$binFull = isset($_GET['is_full']) ? intval($_GET['is_full']) : null;

if ($binFull !== null) {
    // Get the latest status from the bin_status table
    $result = $conn->query("SELECT is_full FROM bin_status ORDER BY id DESC LIMIT 1");
    $lastStatus = $result->fetch_assoc()['is_full'] ?? null;

    // Only insert a new row if the status has changed from the last recorded status
    if ($lastStatus !== $binFull) {
        $stmt = $conn->prepare("INSERT INTO bin_status (is_full) VALUES (?)");
        $stmt->bind_param("i", $binFull);
        $stmt->execute();
        $stmt->close();
        echo "Bin status updated to " . ($binFull ? "full (1)" : "not full (0)") . ".";
    } else {
        echo "No change in bin status. Current status is already " . ($lastStatus ? "full (1)" : "not full (0)") . ".";
    }
} else {
    echo "Error: bin status not provided.";
}

$conn->close();
?>
