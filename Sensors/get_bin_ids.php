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

// Query to get distinct bin_codes from the bin_summary table
$sql = "SELECT DISTINCT bin_code FROM bin_summary";
$result = $conn->query($sql);

$bins = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bins[] = $row;  // Collect bin_codes into an array
    }
} else {
    echo json_encode(["error" => "No bins found"]);
    exit;
}

echo json_encode($bins);  // Return bin codes as JSON
$conn->close();
?>
