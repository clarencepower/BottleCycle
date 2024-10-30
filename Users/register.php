<?php
require '../config.php';

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
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showSuccessPopover();
                        });
                      </script>";
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
    <link rel="shortcut icon" type="x-icon" href="../drawable/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bottle Cycle</title>
    <link rel="stylesheet" href="../css/login-register.css">
    <style>
               body {
            font-family: 'Arial', sans-serif;
            background: url('../drawable/webbackground.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #000;
        }
        nav {
    margin-left: auto;
}

.nav-links {
    display: flex;
    list-style: none;
    padding: left 30px;
}

.nav-links li {
    margin-left: 60px;
}

.nav-links a {
    text-decoration: none;
    color: #002409;
    font-size: 16px;
    font-weight: bold;
}


        /* Popover Styling */
        .popover {
            display: none; /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
        }

        .popover.show {
            display: block;
            animation: fadeIn 0.10s ease-in-out;
        }

        /* Checkmark Animation */
        .checkmark {
            font-size: 50px;
            color: green;
            animation: bounce 1s ease-in-out;
        }

        /* Success message styling */
        .success-message {
            font-size: 18px;
            margin-top: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: scale(0.9);
            }
            50% {
                transform: scale(1.1);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../drawable/logo.png" alt="Bottle Cycle Logo" class="logo">
            <div class="brand-info">
                <h1>BOTTLE CYCLE</h1>
                <p>Smart Plastic Bottle Bin</p>
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

    <!-- Popover for Successful Registration -->
    <div id="success-popover" class="popover">
        <div class="checkmark">✔</div>
        <div class="success-message">Successfully Registered!</div>
    </div>

    <script>
        function showSuccessPopover() {
            const popover = document.getElementById("success-popover");
            popover.classList.add("show");

            // Hide the popover after 2 seconds and redirect to login page
            setTimeout(() => {
                popover.classList.remove("show");
                window.location.href = 'login.php'; // Redirect to login page
            }, 2000);
        }

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
</body>
</html>
