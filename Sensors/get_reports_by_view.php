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

// Get parameters
$viewBy = isset($_GET['viewBy']) ? $conn->real_escape_string($_GET['viewBy']) : 'day';
$year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : date('Y');
$binCode = isset($_GET['binCode']) ? $conn->real_escape_string($_GET['binCode']) : ''; // Get binCode parameter

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

// Build the main query with binCode filtering
$query = "
    SELECT 
        DATE_FORMAT(timestamp, '$dateFormat') AS date,
        SUM(total_small) AS small_bottles,
        SUM(total_medium) AS medium_bottles,
        SUM(total_large) AS large_bottles,
        SUM(total_bottles) AS total_bottles
    FROM bottles_collected
    WHERE YEAR(timestamp) = '$year'
";

if (!empty($binCode)) {
    $query .= " AND bin_code = '$binCode'";
}

$query .= "
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
            'total_bottles' => (int)$row['total_bottles']
        ];
    }
}

// Output the results as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
