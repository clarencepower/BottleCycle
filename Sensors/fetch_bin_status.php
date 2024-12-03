<?php
// Database connection credentials
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name

// Create connection
// Database connection (update with your DB credentials)
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// SQL query to get the 15 latest bin status records based on 'id'
$sql = "SELECT id, bin_id, is_full, timestamp FROM bin_status ORDER BY id DESC LIMIT 15";

$result = $mysqli->query($sql);

// Check if the query was successful
if ($result) {
    $status_data = [];
    $full_or_picked_up_bins = [];
    
    // Loop through the results and add both "Full" and "Picked Up" bins to the array
    while ($row = $result->fetch_assoc()) {
        $status_data[] = $row;
        $full_or_picked_up_bins[] = $row;  // Include both full and picked up bins
    }

    echo json_encode([
        'status_data' => $status_data,
        'full_or_picked_up_bins' => $full_or_picked_up_bins  // Send both statuses
    ]);
} else {
    echo json_encode(['error' => 'Failed to fetch data']);
}

// Close the database connection
$mysqli->close();
?>