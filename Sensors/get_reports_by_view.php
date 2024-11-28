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

// Fetch data from the `ctu_0001` table, considering the viewBy grouping
$query = "
    SELECT 
        DATE_FORMAT(timestamp, '$dateFormat') AS date,
        SUM(small_bottle_counts) AS small_bottles,
        SUM(medium_bottle_counts) AS medium_bottles,
        SUM(large_bottle_counts) AS large_bottles
    FROM ctu_0001
    WHERE YEAR(timestamp) = '$year'
    GROUP BY $groupBy
    ORDER BY $groupBy ASC";

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
