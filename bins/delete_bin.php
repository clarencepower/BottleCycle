<?php
require '../config.php';

if (isset($_GET['binCode'])) {
    $binCode = $_GET['binCode'];

    $stmt = $conn->prepare("DELETE FROM bottle_bins WHERE bin_code = ?");
    $stmt->bind_param("s", $binCode);

    if ($stmt->execute()) {
        echo "Bottle bin deleted successfully.";
    } else {
        echo "Error deleting bottle bin.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
