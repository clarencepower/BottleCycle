<?php
session_start();
require '../config.php';
require '../auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../drawable/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- FullCalendar CSS and JS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.js'></script>
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

        /* Specific Widget Styling */
        .time-widget {
            text-align: center;
        }

        .time-widget h3 {
            font-size: 36px;
            color: #005709;
            animation: pulse 1.5s infinite;
        }

        .weather {
            margin-top: 15px;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        .weather img {
            margin-bottom: 10px;
            width: 150px;
            height: 150px;
            animation: bounce 1s infinite;
        }

        #temperature {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        #weather-description {
            font-size: 16px;
            color: #666;
            text-transform: capitalize;
        }

        .bottle-count-widget .bottle-box {
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            text-align: center;
        }

        .bottle-count-widget .bottle-box h5 {
            color: #002f05;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .notification-widget .notification {
            background-color: rgba(255, 235, 204, 0.9);
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .notification-widget .notification p {
            margin-bottom: 5px;
        }

        .calendar-widget iframe {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Notification Icon with Badge */
        .notification-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            color: #ffdd57;
            font-size: 24px;
            cursor: pointer;
        }

        .badge {
            background-color: #ff3e3e;
            color: white;
            padding: 3px 8px;
            border-radius: 50%;
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 12px;
            display: inline-block;
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
         /* Notification Widget Styling */
    .notification-widget {
        background: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 350px;
        margin-top: 10px;
    }

    .notification-widget h4 {
        font-size: 20px;
        color: #002f05;
        margin-bottom: 15px;
        font-weight: bold;
    }

    #notification-container {
        max-height: 200px; /* Limit container height for recent notifications */
        overflow-y: auto;
    }

    .notification-item {
        background-color: #ffe5e5;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .notification-item p {
        margin: 5px 0;
        font-size: 16px;
        color: #333;
    }

    .notification-item .notification-title {
        font-weight: bold;
        font-size: 16px;
        color: #d9534f;
    }

    .notification-item .notification-timestamp {
        font-size: 12px;
        color: #666;
    }

     /* Notification Widget Styling with Glassmorphism */
     .notification-widget {
        background: rgba(255, 255, 255, 0.1); /* Light transparent background */
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px); /* Frosted glass effect */
        border: 1px solid rgba(255, 255, 255, 0.2);
        max-width: 350px;
        margin-top: 10px;
    }

    #notification-container {
        max-height: 200px; /* Limit container height for recent notifications */
        overflow-y: auto;
    }

    .notification-item {
        background: rgba(255, 255, 255, 0.858); /* Semi-transparent background */
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(6px); /* Frosted glass effect on individual items */
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
    }

    .notification-item p {
        margin: 5px 0;
        font-size: 16px;
        color: #0e0e0e;
    }

    .notification-item .notification-title {
        font-weight: bold;
        font-size: 16px;
        color: #ff7070; /* Red accent color */
    }


    .notification-icon .badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #ff3e3e;
        color: white;
        padding: 3px 7px;
        border-radius: 50%;
        font-size: 12px;
        display: inline-block;
    }
   /* Flash Effect for Notification Content */
.flash {
    animation: flash 1s ease-in-out 0s 3; /* Flash 3 times */
}

/* Flash Animation Keyframes */
@keyframes flash {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Notification Item Slide and Fade In */
.notification-item {
    opacity: 0;
    transform: translateY(20px);
    animation: slideFadeIn 0.6s ease forwards;
}

/* Slide and Fade In Animation */
@keyframes slideFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Modern Notification Badge Animation */
#notification-badge.pulse {
    animation: pulseBadge 1s ease-in-out infinite;
}

/* Badge Pulse Animation */
@keyframes pulseBadge {
    0% { transform: scale(1); background-color: #ff3e3e; }
    50% { transform: scale(1.1); background-color: #ff7070; }
    100% { transform: scale(1); background-color: #ff3e3e; }
}

.notification-item.full {
    background-color: red; /* Red for Full bins */
}

.notification-item.picked-up {
    background-color: green; /* Green for Picked Up bins */
}
.notification-item {
    padding: 10px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.notification-title {
    font-weight: bold;
}

.notification-timestamp {
    font-size: 0.8em;
    color: gray;
}

.notification-item p {
    margin: 5px 0;
}

/* Full Bin (red) */
.notification-item.full {
    background-color: #ffdddd;
    border-left: 5px solid red;
}

/* Picked Up Bin (green) */
.notification-item.picked-up {
    background-color: #ddffdd;
    border-left: 5px solid green;
}
  
    </style>
</head>
<body>
<audio id="notification-sound" src="../drawable/notif.mp3" preload="auto"></audio>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar collapsed">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
                
            </button>
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
       <!-- Audio for Notification -->
    <audio id="notification-sound" src="../drawable/notif.mp3" preload="auto"></audio>
            <!-- Time and Weather Widget -->
            <section class="widget time-widget">
                <h2>Welcome</h2>
                <div class="time">
                    <h3 id="current-time">Loading...</h3>
                    <p id="day-of-week">Loading...</p>
                    <p id="location" >Location: Loading...</p>
                </div>
                <div class="weather">
                    <img id="weather-icon" src="" alt="Weather Icon" style="display: none;">
                    <p id="temperature">Loading...</p>
                    <p id="weather-description">Loading...</p>
                </div>
            </section>

            <!-- Bottles Collected Widget -->
            <section class="widget bottle-count-widget">
                <div class="bottle-size">
                    <h4>Bottles Collected</h4>
                    <!-- Bin Selection Dropdown -->
                    <button onclick="fetchAllBinsData()">All Bins</button>
    <select id="bin-dropdown" onchange="fetchDataByBin()">
        <option value="">Select Bin</option>
        <!-- The bin options will be populated dynamically -->
    </select>
                    <div class="bottle-box">
                        <h5>Small Bottles</h5>
                        <p id="small-bottle-count">Loading...</p>
                    </div>
                    <div class="bottle-box">
                        <h5>Medium Bottles</h5>
                        <p id="medium-bottle-count">Loading...</p>
                    </div>
                    <div class="bottle-box">
                        <h5>Large Bottles</h5>
                        <p id="large-bottle-count">Loading...</p>
                    </div>
                    <div class="bottle-box">
                        <h5>Total Bottles Collected</h5>
                        <p id="total-counts">Loading...</p>
                    </div>
                </div>
            </section>

            <!-- Notifications Widget -->
           
             <!-- Notifications Widget -->
<section class="widget notification-widget">
    <h4>Notifications</h4>
    <div id="notification-container">
        <div id="notification-content">
            <!-- "Full" status records will be dynamically populated here -->
        </div>
    </div>
    <!-- Notification Icon with Badge -->
    <div class="notification-icon">
        <i class="fas fa-bell"></i>
        <span id="notification-badge" class="badge">0</span>
    </div>
</section>

        

            <!-- Calendar Widget -->
            <section class="widget calendar-widget">
                <h4>Philippines Event Calendar</h4>
                <iframe src="https://calendar.google.com/calendar/embed?src=en.philippines%23holiday%40group.v.calendar.google.com&ctz=Asia%2FManila"
                        style="border: 0" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
            </section>
            
            
        </main>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    let previousLatestTimestamp = null; // Store the timestamp of the latest notification
    const notificationSound = document.getElementById("notification-sound");

    function fetchBinStatus() {
        fetch('../Sensors/fetch_bin_status.php')  // Path to your PHP script
            .then(response => response.json())
            .then(data => {
                const notificationContent = document.getElementById("notification-content");
                notificationContent.innerHTML = ""; // Clear existing content

                // Get the first 5 "Full" or "Picked Up" bin notifications
                const fullOrPickedUpNotifications = data.full_or_picked_up_bins.slice(0, 5);

                // Check if there's a new notification with a different timestamp
                if (fullOrPickedUpNotifications.length > 0) {
                    const latestTimestamp = fullOrPickedUpNotifications[0].timestamp;

                    if (previousLatestTimestamp !== latestTimestamp) {
                        // Play notification sound for the new timestamp
                        playNotificationSound();

                        // Update the previous timestamp
                        previousLatestTimestamp = latestTimestamp;
                    }
                }

                // Display the recent "Full" or "Picked Up" notifications
                fullOrPickedUpNotifications.forEach(record => {
                    const notificationItem = document.createElement("div");
                    notificationItem.classList.add("notification-item");

                    const title = document.createElement("p");
                    title.classList.add("notification-title");

                    // Handle Full vs. Picked Up status
                    if (record.is_full === 1) {
                        title.textContent = `Bottle Bin ${record.bin_id} is Full`; // Dynamic bin_id for Full
                    } else {
                        title.textContent = `Bottle Bin ${record.bin_id} was Picked Up`; // Dynamic bin_id for Picked Up
                    }

                    const message = document.createElement("p");
                    message.textContent = record.is_full === 1
                        ? "Please empty to avoid overflow."  // Message for Full bin
                        : "Bin has been emptied.";           // Message for Picked Up bin

                    const timestamp = document.createElement("p");
                    timestamp.classList.add("notification-timestamp");
                    timestamp.textContent = `Timestamp: ${record.timestamp}`;

                    notificationItem.appendChild(title);
                    notificationItem.appendChild(message);
                    notificationItem.appendChild(timestamp);

                    notificationContent.appendChild(notificationItem);
                });

                // Update the notification badge count
                const notificationBadge = document.getElementById("notification-badge");
                notificationBadge.textContent = fullOrPickedUpNotifications.length;
                notificationBadge.style.display = fullOrPickedUpNotifications.length > 0 ? "inline-block" : "none";
            })
            .catch(error => console.error("Error fetching bin status:", error));
    }

    function playNotificationSound() {
        notificationSound.currentTime = 0;
        notificationSound.play().catch((error) => {
            console.error("Error playing notification sound:", error);
        });
    }

    // Initial fetch and set interval
    fetchBinStatus();
    setInterval(fetchBinStatus, 10000); // Fetch every 10 seconds
});



    </script>
        

    <!-- JavaScript for Sidebar Toggle and Other Functions -->
    <script>
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

        // Display Philippine Time
        function updatePhilippineTime() {
            const options = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const now = new Date();
            document.getElementById('current-time').textContent = formatter.format(now);

            const dayOptions = { weekday: 'long', timeZone: 'Asia/Manila' };
            const dayFormatter = new Intl.DateTimeFormat('en-US', dayOptions);
            document.getElementById('day-of-week').textContent = dayFormatter.format(now);
        }

        setInterval(updatePhilippineTime, 1000); // Update time every second

        // Get user's location and fetch weather data
        async function getLocationAndFetchWeather() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Get location name using reverse geocoding
                    const locationResponse = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=YOUR_API_KEY&units=metric`);
                    const locationData = await locationResponse.json();
                    document.getElementById('location').textContent = `Location: ${locationData.name}`;

                    // Fetch weather data for user's location
                    fetchWeather(lat, lon);
                }, (error) => {
                    console.error("Geolocation error:", error);
                    document.getElementById('location').textContent = 'Location: Unable to retrieve';
                });
            } else {
                document.getElementById('location').textContent = 'Geolocation not supported';
            }
        }

        // Fetch weather data for specific coordinates
        async function fetchWeather(lat, lon) {
            const apiKey = '7e193e5c921b6f40aa3afd8dc13273eb'; // Replace with your OpenWeather API key
            const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                // Update weather details
                const temp = data.main.temp;
                const weatherDescription = data.weather[0].description;
                const iconCode = data.weather[0].icon;
                const iconUrl = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

                document.getElementById('temperature').textContent = `${temp}°C`;
                document.getElementById('weather-description').textContent = weatherDescription.charAt(0).toUpperCase() + weatherDescription.slice(1);
                const weatherIcon = document.getElementById('weather-icon');
                weatherIcon.src = iconUrl;
                weatherIcon.style.display = 'inline';
            } catch (error) {
                console.error("Error fetching weather data:", error);
                document.getElementById('temperature').textContent = 'Unable to load weather data';
                document.getElementById('weather-description').textContent = '';
            }
        }

        getLocationAndFetchWeather(); // Get user's location and fetch weather
        setInterval(getLocationAndFetchWeather, 600000); // Update weather every 10 minutes

        // Function to fetch bottle counts and update the widget
// Fetch bin IDs for dropdown selection
function fetchBinIds() {
        fetch('../Sensors/get_bin_ids.php')
            .then(response => response.json())
            .then(data => {
                const binDropdown = document.getElementById('bin-dropdown');
                data.forEach(bin => {
                    const option = document.createElement('option');
                    option.value = bin.bin_code;
                    option.textContent = bin.bin_code;
                    binDropdown.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching bin IDs:', error));
    }

    // Fetch data for the selected bin and populate the bottle counts
    function fetchDataByBin() {
        const binId = document.getElementById('bin-dropdown').value;

        if (binId) {
            const url = `../Sensors/get_daily_reports.php?bin_code=${binId}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update bottle counts
                    if (data && data.length > 0) {
                        const latestData = data[0]; // Assuming the first entry contains the latest data
                        document.getElementById('small-bottle-count').textContent = latestData.total_small || 0;
                        document.getElementById('medium-bottle-count').textContent = latestData.total_medium || 0;
                        document.getElementById('large-bottle-count').textContent = latestData.total_large || 0;
                        document.getElementById('total-counts').textContent = latestData.total_bottles || 0;
                    } else {
                        document.getElementById('small-bottle-count').textContent = '0';
                        document.getElementById('medium-bottle-count').textContent = '0';
                        document.getElementById('large-bottle-count').textContent = '0';
                        document.getElementById('total-counts').textContent = '0';
                    }
                })
                .catch(error => console.error('Error fetching bin data:', error));
        }
    }

    // Initialize by fetching bin IDs
    fetchBinIds();

    // Function to fetch total bottles collected for all bins
function fetchAllBinsData() {
    // Make an AJAX call to the server to fetch the total bottle data
    fetch('../Sensors/fetch_all_bins.php') // Replace with your actual API endpoint
        .then(response => response.json())
        .then(data => {
            // Update the UI with the total data
            document.getElementById('small-bottle-count').innerText = data.total_small || '0';
            document.getElementById('medium-bottle-count').innerText = data.total_medium || '0';
            document.getElementById('large-bottle-count').innerText = data.total_large || '0';
            document.getElementById('total-counts').innerText = data.total_bottles || '0';
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            // Handle error, possibly display a message to the user
        });
}

    </script>
</body>
</html>
