<?php
// Database connection credentials
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
// Set the response type to JSON
header('Content-Type: application/json');

// Check the connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// SQL query to get all bin summary data
$sql = "SELECT bin_code, total_small, total_medium, total_large, total_bottles, timestamp FROM bin_summary ORDER BY timestamp DESC";

$result = $mysqli->query($sql);

// Check if the query was successful
if ($result) {
    $bins_data = [];
    
    // Fetch all rows
    while ($row = $result->fetch_assoc()) {
        // Convert timestamp to 12-hour format
        $dateTime = new DateTime($row['timestamp']);
        $formattedTimestamp = $dateTime->format('Y-m-d h:i:s A'); // 'h' for 12-hour format with leading zero, 'A' for AM/PM

        // Add formatted timestamp to the row data
        $row['formatted_timestamp'] = $formattedTimestamp;
        
        // Add the row to the data array
        $bins_data[] = $row;
    }

    // Return the data as JSON
    echo json_encode($bins_data);
} else {
    echo json_encode(['error' => 'Failed to fetch data']);
}

// Close the database connection
$mysqli->close();
?>
