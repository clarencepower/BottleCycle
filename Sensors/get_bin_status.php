<?php

$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu";

// Set the response type to JSON
header('Content-Type: application/json');

// Database connection (update with your DB credentials)
$mysqli = new mysqli($servername, $username , $password, $dbname);

// SQL query to get the 15 latest bin status records based on 'id'
$sql = "SELECT bin_id, is_full, timestamp FROM bin_status ORDER BY id DESC LIMIT 1";

$result = $mysqli->query($sql);

// Check if the query was successful
if ($result) {
    $status_data = [];
    while ($row = $result->fetch_assoc()) {
        $status = ($row['is_full'] == 1) ? 'This Bin is Full' : 'Bin was Collected';
        $status_data[] = [
            'bin_id' => $row['bin_id'],
            'status' => $status,
            'timestamp' => $row['timestamp']
        ];
    }
    echo json_encode($status_data); // Return the result as a JSON response
} else {
    echo json_encode(['error' => 'Failed to fetch data']);
}

// Close the database connection
$mysqli->close();
?>
