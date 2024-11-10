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

// Query to fetch latest daily total counts for each bottle size and the overall total
$query = "
    SELECT all_dates.date,
           COALESCE(small_counts.small_bottles, 0) AS small_bottles,
           COALESCE(medium_counts.medium_bottles, 0) AS medium_bottles,
           COALESCE(large_counts.large_bottles, 0) AS large_bottles,
           (COALESCE(small_counts.small_bottles, 0) + COALESCE(medium_counts.medium_bottles, 0) + COALESCE(large_counts.large_bottles, 0)) AS total_bottles
    FROM (
        -- Get all unique dates from all tables
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM small_bottle_counts
        UNION
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM medium_bottle_counts
        UNION
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM large_bottle_counts
    ) AS all_dates
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS small_bottles
        FROM small_bottle_counts AS t1
        WHERE id = (SELECT MAX(id) FROM small_bottle_counts WHERE DATE(timestamp) = DATE(t1.timestamp))
    ) AS small_counts ON all_dates.date = small_counts.date
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS medium_bottles
        FROM medium_bottle_counts AS t2
        WHERE id = (SELECT MAX(id) FROM medium_bottle_counts WHERE DATE(timestamp) = DATE(t2.timestamp))
    ) AS medium_counts ON all_dates.date = medium_counts.date
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS large_bottles
        FROM large_bottle_counts AS t3
        WHERE id = (SELECT MAX(id) FROM large_bottle_counts WHERE DATE(timestamp) = DATE(t3.timestamp))
    ) AS large_counts ON all_dates.date = large_counts.date
";

// Apply date filtering if a specific date is requested
if ($dateFilter) {
    $query .= " WHERE all_dates.date = '$dateFilter'";
}

// Order by date descending
$query .= " ORDER BY all_dates.date DESC";

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
