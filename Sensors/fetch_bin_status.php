<?php
// Database connection credentials
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name

// Create connection
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

    // Loop through the results and add status based on 'is_full' value
    while ($row = $result->fetch_assoc()) {
        $status = "";
        
        // Determine the status based on 'is_full'
        if ($row['is_full'] == 1) {
            $status = "Full";
        } elseif ($row['is_full'] == 2) {
            $status = "Medium Level";
        } elseif ($row['is_full'] == 0) {
            $status = "Picked Up";
        }

        // Add the record with status information to the response
        $full_or_picked_up_bins[] = [
            'bin_id' => $row['bin_id'],
            'status' => $status,
            'timestamp' => $row['timestamp']
        ];
    }

    // Return a JSON response with the status data
    echo json_encode([
        'full_or_picked_up_bins' => $full_or_picked_up_bins
    ]);
} else {
    // If the query fails, return an error message
    echo json_encode(['error' => 'Failed to fetch data']);
}
?>
