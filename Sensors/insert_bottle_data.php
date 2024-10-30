<?php
// Database credentials
$servername = "localhost";
$username = "root"; // change if different
$password = ""; // change if different
$dbname = "bottlecycle-ctu"; // replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if POST data is set
if (isset($_POST['bottle_size']) && isset($_POST['count'])) {
    $bottle_size = $_POST['bottle_size'];
    $count = intval($_POST['count']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bottle_data (bottle_size, count) VALUES (?, ?)");
    $stmt->bind_param("si", $bottle_size, $count);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Data successfully inserted";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid input data";
}

$conn->close();
?>
