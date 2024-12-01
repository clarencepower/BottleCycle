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

// Get the bin_code and date from the query parameters
$bin_code = isset($_GET['bin_code']) ? $_GET['bin_code'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Query to fetch data from the bin_summary table for the selected bin_code and date
$query = "
    SELECT DATE(timestamp) AS date,
           SUM(total_small) AS total_small,
           SUM(total_medium) AS total_medium,
           SUM(total_large) AS total_large,
           SUM(total_bottles) AS total_bottles
    FROM bin_summary
    WHERE bin_code = ?";

if ($date) {
    $query .= " AND DATE(timestamp) = ?";
}

$query .= " GROUP BY bin_code, DATE(timestamp)";  // Group by bin_code and date to aggregate data

$stmt = $conn->prepare($query);
if ($date) {
    $stmt->bind_param("ss", $bin_code, $date);
} else {
    $stmt->bind_param("s", $bin_code);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);  // Return the data as JSON

$stmt->close();
$conn->close();
?>
