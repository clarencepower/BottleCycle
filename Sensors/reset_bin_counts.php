<?php
// reset_bin_counts.php
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bin_id = $_POST['bin_id']; // Get bin_id from the request

    // Check if the latest is_full for the given bin_id is 4
    $query = "SELECT is_full FROM bin_status WHERE bin_id = ? ORDER BY timestamp DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bin_id);
    $stmt->execute();
    $stmt->bind_result($is_full);
    $stmt->fetch();
    $stmt->close();

    if ($is_full == 3) {
        // Reset counts for the bin_id
        $resetQuery = "UPDATE bin_summary SET total_small = 0, total_medium = 0, total_large = 0 WHERE bin_id = ?";
        $resetStmt = $conn->prepare($resetQuery);
        $resetStmt->bind_param("s", $bin_id);
        $resetStmt->execute();
        $resetStmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Counts reset successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Bin is not full or invalid bin_id.']);
    }
}
?>