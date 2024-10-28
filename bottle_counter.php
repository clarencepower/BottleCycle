<?php
// Database connection credentials
$servername = "localhost";
$username = "root";  // Default user for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "bottlecycle-ctu";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the count from POST data
if (isset($_POST['count'])) {
    $count = $_POST['count'];

    // Insert count into bottle_counter
    $sql = "INSERT INTO bottle_counts (count) VALUES ('$count')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "No data received";
}

$conn->close();
?>
