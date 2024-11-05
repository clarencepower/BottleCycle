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

// Query to fetch daily total counts for each bottle size and the overall total
$query = "
    SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
           COALESCE(SUM(CASE WHEN table_name = 'small_bottle_counts' THEN count ELSE 0 END), 0) AS small_bottles,
           COALESCE(SUM(CASE WHEN table_name = 'medium_bottle_counts' THEN count ELSE 0 END), 0) AS medium_bottles,
           COALESCE(SUM(CASE WHEN table_name = 'large_bottle_counts' THEN count ELSE 0 END), 0) AS large_bottles,
           COALESCE(SUM(count), 0) AS total_bottles
    FROM (
        SELECT timestamp, count, 'small_bottle_counts' AS table_name FROM small_bottle_counts
        UNION ALL
        SELECT timestamp, count, 'medium_bottle_counts' AS table_name FROM medium_bottle_counts
        UNION ALL
        SELECT timestamp, count, 'large_bottle_counts' AS table_name FROM large_bottle_counts
    ) AS all_counts
";

// Apply date filtering if a specific date is requested
if ($dateFilter) {
    $query .= " WHERE DATE(timestamp) = '$dateFilter'";
}

// Group by date and order by descending date
$query .= " GROUP BY date ORDER BY date DESC";

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
