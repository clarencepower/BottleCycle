<?php
require '../config.php';

// Check if a session is already started before calling session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare SQL query to prevent SQL injection
    $query = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    // Check if the email exists in the database
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Start session and save user data
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $row["id"];   // Store user ID in session
            $_SESSION["email"] = $email;

            // Redirect to dashboard.php after successful login
            header("Location: ../Users/dashboard.php");
            exit(); // Ensure the script stops executing after the redirect
        } else {
            echo "<script> alert('Incorrect Password'); </script>";
        }
    } else {
        echo "<script> alert('Email Not Registered'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../drawable/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bottle Cycle</title>
    <link rel="stylesheet" href="../css/login-register.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('../drawable/webbackground.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #000;
        }
        </style>
    
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../drawable/logo.png" alt="Bottle Cycle Logo" class="logo">
            <div class="brand-info">
                <h1>BOTTLE CYCLE</h1>
                <p>Smart  Plastic Bottle Bin</p>
            </div>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="../Public/index.html">Home</a></li>
                <li><a href="../Public/aboutus.html">About Us</a></li>
                <li><a href="login.php">Log in</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-section">
            <div class="login-box">
                <h2>Login</h2>
                <form method="POST" action="">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="login-button">Login</button>
                </form>

                <a href="../Users/register.php" class="register-link">Register Account</a>
            </div>
        </section>
    </main>
</body>
</html>
