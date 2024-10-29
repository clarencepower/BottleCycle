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

// Query to get the latest bin status
$query = "SELECT is_full FROM bin_status ORDER BY id DESC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Get the latest is_full status
$is_full = $result ? $result['is_full'] : 0;

// Insert a new record into history if bin is full
if ($is_full == 1) {
    $insertQuery = "INSERT INTO bin_status_history (is_full) VALUES (:is_full)";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->execute(['is_full' => $is_full]);
}

// Return the latest bin status as JSON
header('Content-Type: application/json');
echo json_encode($result);
?>
