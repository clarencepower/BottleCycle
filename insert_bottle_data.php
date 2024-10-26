<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from ESP32
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if bottle_size and count fields are set
    if (isset($_POST['bottle_size']) && isset($_POST['count'])) {
        $bottle_size = $_POST['bottle_size'];
        $count = (int)$_POST['count'];  // Ensure count is an integer

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO bottle_data (bottle_size, count) VALUES (?, ?)");
        $stmt->bind_param("si", $bottle_size, $count);

        // Execute the query
        if ($stmt->execute()) {
            echo "Data inserted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Missing bottle_size or count data";
    }
}

$conn->close();
?>
