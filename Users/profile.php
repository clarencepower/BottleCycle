
<?php
require '../config.php';
require '../auth.php';



if (isset($_POST["submit"])) {
    $userId = $_SESSION["user_id"];
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($currentPassword, $user["password"])) {
        if ($newPassword === $confirmPassword) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedNewPassword, $userId);

            if ($updateStmt->execute()) {
                echo "<script>alert('Password changed successfully');</script>";
            } else {
                echo "<script>alert('Error updating password');</script>";
            }
        } else {
            echo "<script>alert('New passwords do not match');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="drawable/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- FullCalendar CSS and JS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.js'></script>

    <!-- Mapbox CSS and JS -->
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>

    <style>
       /* Global Styles */
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            height: 100vh;
            background-image: url('../drawable/webbackground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
        }

        .dashboard {
            display: flex;
            width: 100%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 60px;
            overflow: hidden;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: opacity 0.3s ease;
        }

        .logo {
            width: 40px;
            height: auto;
        }

        /* Toggle Button Styling */
        .toggle-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        /* Hide text when sidebar is collapsed */
        .brand-info h1,
        .nav-links a span {
            display: inline;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .brand-info h1,
        .sidebar.collapsed .nav-links a span {
            display: none;
        }

        /* Navigation Links */
        .nav-links {
            list-style: none;
            width: 100%;
            padding: 0;
        }

        .nav-links li {
            margin: 10px 0;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-links a i {
            font-size: 20px;
            margin-right: 15px;
        }

        .nav-links a:hover {
            background-color: rgba(0, 128, 0, 0.3);
            color: white;
        }

        .sidebar.collapsed .nav-links a {
            justify-content: center;
            text-align: center;
        }

        .sidebar.collapsed .nav-links a i {
            margin-right: 0;
            display: block;
            text-align: center;
        }

        /* Main Content Styling */
        .content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            overflow-y: auto;
        }

        /* Widget Styling with Glassmorphism */
        .widget {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 300px;
            max-width: 100%;
            min-width: 250px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .widget h4, .widget h2 {
            font-size: 18px;
            color: #002f05;
            margin-bottom: 15px;
        }


        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
 

        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
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
        * Login Section */
.login-section {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100vh - 80px);
}

.login-box {
    background: rgba(255, 255, 255, 0.9);
    padding: 40px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.login-box h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.input-field {
    display: flex;
    align-items: center;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
}

.input-field i {
    margin-right: 10px;
    color: #888;
}

.input-field input {
    border: none;
    outline: none;
    flex-grow: 1;
}

.login-button {
    background-color: #004d00;
    color: white;
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
}

.login-button:hover {
    background-color: #003300;
}

.register-link {
    display: block;
    margin-top: 20px;
    color: #333;
    text-decoration: none;
}

.register-link:hover {
    text-decoration: underline;
}

 </style>
</head>
<body class="bod">

    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar collapsed">
            <div class="logo-container">
                <img src="../drawable/logo.png" alt="Bottle Cycle Logo" class="logo">
            </div>
            <nav>
            <ul class="nav-links">
                    <li><a href="../Users/dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li><a href="../Users/bin_notification.php"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                    <li><a href="../Users/profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="../Users/bottlebins.php"><i class="fas fa-trash-alt"></i> <span>Bottle Bin</span></a></li>
                    <li><a href="../Users/reports.php"><i class="fas fa-file-alt"></i> <span>Reports</span></a></li>
                    <li><a href="../Users/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <section class="widget time-widget">
            <section class="change-password-section">
            <div class="change-password-box">
                <h2>Change Password</h2>
                <form action="../Users/profile.php" method="POST">
                    <div class="input-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
  

                    


                    <div class="input-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="submit" class="change-password-button">Change Password</button>
                </form>
            </div>
        </section>
            </section>
            
       
            <script>
             <!-- JavaScript for Sidebar Toggle and Other Functions -->
                
                    function toggleSidebar() {
                        const sidebar = document.querySelector('.sidebar');
                        sidebar.classList.toggle('collapsed');
                    }
            
                    // Expand sidebar on hover
                    const sidebar = document.querySelector('.sidebar');
                    sidebar.addEventListener('mouseenter', () => {
                        sidebar.classList.remove('collapsed');
                    });
            
                    // Collapse sidebar when the mouse leaves
                    sidebar.addEventListener('mouseleave', () => {
                        sidebar.classList.add('collapsed');
                    });
            </script>
    
            
            
            
                
    
        
</body>
</html>
