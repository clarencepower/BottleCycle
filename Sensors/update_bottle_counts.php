<?php
// Database credentials
$servername = "localhost";  // Change this to your MySQL server
$username = "root";         // Change this to your MySQL username
$password = "";             // Change this to your MySQL password
$dbname = "bottlecycle-ctu";    // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from URL parameters
$bin_id = $_GET['bin_id'];
$size_type = $_GET['size_type'];
$quantity = $_GET['quantity'];
$is_full = isset($_GET['is_full']) ? $_GET['is_full'] : null;

// If bin status is provided, update the bin status
if ($is_full !== null) {
    $stmt = $conn->prepare("INSERT INTO bin_status (bin_id, is_full) VALUES (?, ?)");
    $stmt->bind_param("si", $bin_id, $is_full);
    if ($stmt->execute()) {
        echo "Bin status updated successfully";
    } else {
        echo "Error updating bin status: " . $stmt->error;
    }
    $stmt->close();
}

// Insert bottle count data (only if size_type and quantity are provided)
if ($size_type && isset($quantity)) {
    $stmt = $conn->prepare("INSERT INTO bottle_bin_data (bin_id, size_type, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $bin_id, $size_type, $quantity);
    
    if ($stmt->execute()) {
        echo "Bottle count inserted successfully";
    } else {
        echo "Error inserting bottle count: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch summed quantities for each bin_id, size_type, and date (timestamp per day)
$sql = "
    SELECT bin_id, 
           DATE(timestamp) AS date,
           SUM(CASE WHEN size_type = 'small' THEN quantity ELSE 0 END) AS total_small,
           SUM(CASE WHEN size_type = 'medium' THEN quantity ELSE 0 END) AS total_medium,
           SUM(CASE WHEN size_type = 'large' THEN quantity ELSE 0 END) AS total_large
    FROM bottle_bin_data
    GROUP BY bin_id, DATE(timestamp)
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each row and insert or update into the bin_summary table
    while ($row = $result->fetch_assoc()) {
        $binId = $row['bin_id'];
        $date = $row['date'];
        $totalSmall = $row['total_small'];
        $totalMedium = $row['total_medium'];
        $totalLarge = $row['total_large'];
        
        // Calculate total bottles by adding up small, medium, and large bottles
        $totalBottles = $totalSmall + $totalMedium + $totalLarge;
        
        // Check if a record already exists for the same bin_id and date
        $checkSql = "SELECT * FROM bin_summary WHERE bin_code = '$binId' AND DATE(timestamp) = '$date'";
        $checkResult = $conn->query($checkSql);
        
        if ($checkResult->num_rows > 0) {
            // If record exists, update it
            $updateSql = "
                UPDATE bin_summary
                SET total_small = $totalSmall, 
                    total_medium = $totalMedium,
                    total_large = $totalLarge,
                    total_bottles = $totalBottles,
                    timestamp = CURRENT_TIMESTAMP
                WHERE bin_code = '$binId' AND DATE(timestamp) = '$date'
            ";
            if ($conn->query($updateSql) === TRUE) {
                echo "Record updated successfully for bin_id: $binId on $date\n";
            } else {
                echo "Error: " . $updateSql . "\n" . $conn->error;
            }
        } else {
            // If no record exists, insert a new one
            $insertSql = "
                INSERT INTO bin_summary (bin_code, total_small, total_medium, total_large, total_bottles, timestamp)
                VALUES ('$binId', $totalSmall, $totalMedium, $totalLarge, $totalBottles, CURRENT_TIMESTAMP)
            ";
            if ($conn->query($insertSql) === TRUE) {
                echo "New record created successfully for bin_id: $binId on $date\n";
            } else {
                echo "Error: " . $insertSql . "\n" . $conn->error;
            }
        }
    }
} else {
    echo "0 results";
}


// Close connection
$conn->close();
?>
