<?php
require '../config.php';
// Database connection
$conn = new mysqli("localhost", "root", "", "bottlecycle-ctu");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Check if binCode parameter is provided
if (isset($_GET['binCode'])) {
    $binCode = $_GET['binCode'];

    // Prepare SQL statement to delete bin by binCode
    $stmt = $pdo->prepare("DELETE FROM bins WHERE bin_code = :bin_code");
    $stmt->bindParam(':bin_code', $binCode);

    try {
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Bin deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete bin."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "binCode parameter missing."]);
}
?>
