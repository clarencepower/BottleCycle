<?php
require 'config.php';

if(isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirm_password"];

    // Prepare SQL query to prevent SQL injection
    $duplicate = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $duplicate->bind_param("s", $email);
    $duplicate->execute();
    $result = $duplicate->get_result();

    if($result->num_rows > 0) {
        echo "<script> alert('Email is already taken'); </script>";
    } else {
        if($password == $confirmpassword) {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $query->bind_param("ss", $email, $hashedPassword);

            if($query->execute()) {
                echo "<script> alert('Registration Successful'); </script>";
            } else {
                echo "<script> alert('Error during registration'); </script>";
            }
        } else {
            echo "<script> alert('Password does not match'); </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bottle Cycle</title>
    <link rel="stylesheet" href="login-register.css">
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;

            if (password !== confirm_password) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="logo.png" alt="Bottle Cycle Logo" class="logo">
            <div class="brand-info">
                <h1>BOTTLE CYCLE</h1>
                <p>Smart Arduino Based Plastic Bottle Bin</p>
            </div>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="loginpage.html">Log in</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-section">
            <div class="login-box">
                <h2>Register</h2>
                <!-- Define action to point to this PHP file -->
                <form action="register.php" method="POST" onsubmit="return validateForm()">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="email" id="email" name="email" placeholder="Enter email" required>
                        </div>
                    </div>
    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                        </div>
                    </div>
    
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>
    
                    <button type="submit" name="submit" class="login-button">Register</button>
                </form>
    
                <a href="login.php" class="register-link">Login</a>
            </div>
        </section>
    </main>
</body>
</html>
