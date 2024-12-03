<?php
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

// Function to fetch summed quantities for each bin_id, size_type, and date
function fetchSummedQuantities($conn) {
    $sql = "
        SELECT bin_id, 
               DATE(timestamp) AS date,
               SUM(CASE WHEN size_type = 'small' THEN quantity ELSE 0 END) AS total_small,
               SUM(CASE WHEN size_type = 'medium' THEN quantity ELSE 0 END) AS total_medium,
               SUM(CASE WHEN size_type = 'large' THEN quantity ELSE 0 END) AS total_large
        FROM bottle_bin_data
        GROUP BY bin_id, DATE(timestamp)
    ";

    // Execute the query and return the result
    return $conn->query($sql);
}

// Function to insert or update the bin summary
function insertOrUpdateBinSummary($conn, $binId, $date, $totalSmall, $totalMedium, $totalLarge) {
    // Calculate total bottles
    $totalBottles = $totalSmall + $totalMedium + $totalLarge;

    // Use prepared statements to prevent SQL injection
    // Check if a record already exists for the same bin_id and date
    $checkSql = "SELECT * FROM bin_summary WHERE bin_code = ? AND DATE(timestamp) = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ss", $binId, $date); // 'ss' stands for two strings (bin_id and date)
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        // If record exists, update it
        $updateSql = "
            UPDATE bin_summary
            SET total_small = ?, 
                total_medium = ?,
                total_large = ?,
                total_bottles = ?,
                timestamp = CURRENT_TIMESTAMP
            WHERE bin_code = ? AND DATE(timestamp) = ?
        ";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iiiiis", $totalSmall, $totalMedium, $totalLarge, $totalBottles, $binId, $date);

        if ($updateStmt->execute()) {
            echo "Record updated successfully for bin_id: $binId on $date\n";
        } else {
            echo "Error: " . $updateStmt->error;
        }
        $updateStmt->close();
    } else {
        // If no record exists, insert a new one
        $insertSql = "
            INSERT INTO bin_summary (bin_code, total_small, total_medium, total_large, total_bottles, timestamp)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("siiii", $binId, $totalSmall, $totalMedium, $totalLarge, $totalBottles);

        if ($insertStmt->execute()) {
            echo "Record inserted successfully for bin_id: $binId on $date\n";
        } else {
            echo "Error: " . $insertStmt->error;
        }
        $insertStmt->close();
    }
    $stmt->close();
}

// Fetch summed quantities for all bins
$result = fetchSummedQuantities($conn);

if ($result->num_rows > 0) {
    // Loop through each row and insert or update into the bin_summary table
    while ($row = $result->fetch_assoc()) {
        $binId = $row['bin_id'];
        $date = $row['date'];
        $totalSmall = $row['total_small'];
        $totalMedium = $row['total_medium'];
        $totalLarge = $row['total_large'];

        // Call the function to insert or update the bin summary
        insertOrUpdateBinSummary($conn, $binId, $date, $totalSmall, $totalMedium, $totalLarge);
    }
} else {
    echo "No data found for bin summary.\n";
}

// Close the connection
$conn->close();
?>
