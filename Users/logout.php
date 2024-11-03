<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to index.html after logout
header("Location: ../Public/index.html");
exit();
?>
