<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a specific date is requested
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : null;

// Query to fetch the latest daily total counts from the new `ctu_0001` table
$query = "
    SELECT DATE(timestamp) AS date,
           COALESCE(small_bottle_counts, 0) AS small_bottles,
           COALESCE(medium_bottle_counts, 0) AS medium_bottles,
           COALESCE(large_bottle_counts, 0) AS large_bottles,
           (COALESCE(small_bottle_counts, 0) + COALESCE(medium_bottle_counts, 0) + COALESCE(large_bottle_counts, 0)) AS total_bottles
    FROM ctu_0001
";

// Apply date filtering if a specific date is requested
if ($dateFilter) {
    $query .= " WHERE DATE(timestamp) = '$dateFilter'";
}

// Order by date descending
$query .= " ORDER BY DATE(timestamp) DESC";

// Execute the query
$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'date' => $row['date'],
            'small_bottles' => $row['small_bottles'],
            'medium_bottles' => $row['medium_bottles'],
            'large_bottles' => $row['large_bottles'],
            'total_bottles' => $row['total_bottles']
        ];
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
