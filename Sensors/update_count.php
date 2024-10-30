<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle_ctu";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['count'])) {
    $count = intval($_GET['count']);
    $sql = "INSERT INTO BottleCounts (count) VALUES ($count)";
    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Count not received";
}

$conn->close();
?>
