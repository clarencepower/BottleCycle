<?php

$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "bottlecycle-ctu";

// Set the response type to JSON
header('Content-Type: application/json');

// Database connection (update with your DB credentials)
$mysqli = new mysqli($servername, $username , $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// SQL query to get all bin status records (without limiting)
$sql = "SELECT bin_id, is_full, timestamp FROM bin_status ORDER BY id DESC";

$result = $mysqli->query($sql);

// Check if the query was successful
if ($result) {
    $status_data = [];
    while ($row = $result->fetch_assoc()) {
        // Determine the bin status based on 'is_full' value
        if ($row['is_full'] == 0) {
            $status = 'Bin was Collected';
        } elseif ($row['is_full'] == 1) {
            $status = 'Bin is Full';
        } elseif ($row['is_full'] == 2) {
            $status = 'Bin is in Medium Level';
        } else {
            $status = 'Unknown Status'; // In case 'is_full' has unexpected value
        }
        
        // Append each record to the status_data array
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
