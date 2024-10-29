<?php
// Database connection
$host = 'localhost';
$dbname = 'bottlecycle-ctu';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Query to get all records from bin_status_history
$query = "SELECT * FROM bin_status_history ORDER BY timestamp DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode($result);
?>
