<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from URL parameters
$bin_id = $_GET['bin_id'];
$size_type = $_GET['size_type'];
$quantity = $_GET['quantity'];
$is_full = isset($_GET['is_full']) ? $_GET['is_full'] : null;

// Start a transaction to batch queries and ensure atomicity
$conn->begin_transaction();

try {
    // If bin status is provided, update the bin status
    if ($is_full !== null) {
        // Use a single statement for status update (if not exists or if needed)
        $stmt = $conn->prepare("INSERT INTO bin_status (bin_id, is_full) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_full = ?");
        $stmt->bind_param("sii", $bin_id, $is_full, $is_full);
        $stmt->execute();
        $stmt->close();
    }

    // Insert bottle count data (only if size_type and quantity are provided)
    if ($size_type && isset($quantity)) {
        $stmt = $conn->prepare("INSERT INTO bottle_bin_data (bin_id, size_type, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $bin_id, $size_type, $quantity);
        $stmt->execute();
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();
    echo "Data inserted/updated successfully";

} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $conn->rollback();
    echo "Error occurred: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
