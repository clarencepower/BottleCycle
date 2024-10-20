<?php
// Database connection settings
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

// Check if count is passed from ESP32
if (isset($_GET['count'])) {
  $bottle_counts = $_GET['count'];

  // Insert data into database
  $sql = "INSERT INTO bottle_counts (count) VALUES ('$bottle_counts')";
  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
} else {
  echo "No data received!";
}

$conn->close();
?>
