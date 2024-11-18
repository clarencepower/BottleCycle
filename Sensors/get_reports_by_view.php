<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters
$viewBy = isset($_GET['viewBy']) ? $conn->real_escape_string($_GET['viewBy']) : 'day';
$year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : date('Y');

// Determine date format and grouping level based on viewBy
$dateFormat = "%Y-%m-%d";
$groupBy = "DATE(timestamp)";
if ($viewBy === 'month') {
    $dateFormat = "%Y-%m";
    $groupBy = "DATE_FORMAT(timestamp, '%Y-%m')";
} elseif ($viewBy === 'week') {
    $dateFormat = "CONCAT(YEAR(timestamp), '-', WEEK(timestamp))"; // Group by week
    $groupBy = "YEAR(timestamp), WEEK(timestamp)";
} elseif ($viewBy === 'year') {
    $dateFormat = "%Y";
    $groupBy = "YEAR(timestamp)";
}

// Fetch the latest data for each source table using subqueries for latest IDs
$query = "
    SELECT 
        DATE_FORMAT(combined.timestamp, '$dateFormat') AS date,
        SUM(CASE WHEN combined.source = 'small' THEN combined.count ELSE 0 END) AS small_bottles,
        SUM(CASE WHEN combined.source = 'medium' THEN combined.count ELSE 0 END) AS medium_bottles,
        SUM(CASE WHEN combined.source = 'large' THEN combined.count ELSE 0 END) AS large_bottles
    FROM (
        SELECT s.timestamp, s.count, 'small' AS source
        FROM small_bottle_counts s
        WHERE s.id IN (SELECT MAX(id) FROM small_bottle_counts GROUP BY DATE(timestamp))
        
        UNION ALL

        SELECT m.timestamp, m.count, 'medium' AS source
        FROM medium_bottle_counts m
        WHERE m.id IN (SELECT MAX(id) FROM medium_bottle_counts GROUP BY DATE(timestamp))
        
        UNION ALL

        SELECT l.timestamp, l.count, 'large' AS source
        FROM large_bottle_counts l
        WHERE l.id IN (SELECT MAX(id) FROM large_bottle_counts GROUP BY DATE(timestamp))
    ) AS combined
    WHERE YEAR(combined.timestamp) = '$year'
    GROUP BY $groupBy
    ORDER BY combined.timestamp ASC";

// Execute query
$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'date' => $row['date'],
            'small_bottles' => (int)$row['small_bottles'],
            'medium_bottles' => (int)$row['medium_bottles'],
            'large_bottles' => (int)$row['large_bottles'],
            'total_bottles' => (int)$row['small_bottles'] + (int)$row['medium_bottles'] + (int)$row['large_bottles']
        ];
    }
}

// Output the results as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
