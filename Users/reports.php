<?php
require '../config.php';
require '../auth.php';
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
            width: 50px;
            height: 50px;
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
         /* Styling for the notification bell and badge */
         .notification-container {
            position: relative;
            display: inline-block;
        }

        .bell-icon {
            font-size: 32px;
            cursor: pointer;
        }

        .badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            font-size: 14px;
            display: none;
        }

        .notification-message {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
            font-weight: bold;
        }

        /* Styling for the history table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            width: 80%;
            margin: auto;
            margin-top: 50px;
        }
        /* Search Container Styling */
.search-container {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    gap: 10px;
}

.search-container label {
    font-size: 16px;
    color: #333;
}

.search-container input[type="date"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.search-container button {
    padding: 8px 12px;
    background-color: #005709;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-container button:hover {
    background-color: #004108;
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
                <section class="widget report-widget">
                    <h4>Daily Bottle Collection Report</h4>
                    <div class="search-container">
                        <label for="date-search">Search by Date:</label>
                        <input type="date" id="date-search">
                        <button onclick="searchByDate()">Search</button>
                    </div>
                    <div id="report-table-container">
                        <table id="report-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Small Bottles</th>
                                    <th>Total Medium Bottles</th>
                                    <th>Total Large Bottles</th>
                                    <th>Total Bottles</th>
                                </tr>
                            </thead>
                            <tbody id="report-table-body">
                                <!-- Rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <canvas id="report-chart" width="400" height="200"></canvas>
                    </div>
                </section>

            </section>
            
       <!-- Link to Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <script>
       // Function to fetch and display reports based on a specific date
       function searchByDate() {
    const date = document.getElementById("date-search").value;
    if (!date) {
        alert("Please select a date to search.");
        return;
    }

    fetch(`../Sensors/get_daily_reports.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                populateReportTable(data);  // Display the data in the table
                renderReportChart(data);    // Update the chart with the data
            } else {
                // If no data is returned, clear the table and chart, and show a message
                document.getElementById('report-table-body').innerHTML = '<tr><td colspan="5">No records found for this date.</td></tr>';
                renderReportChart([]);  // Clear the chart
            }
        })
        .catch(error => console.error('Error fetching daily report:', error));
}
// Function to fetch all daily report data
function fetchDailyReport() {
    fetch('../Sensors/get_daily_reports.php')
        .then(response => response.json())
        .then(data => {
            populateReportTable(data);
            renderReportChart(data);
        })
        .catch(error => console.error('Error fetching daily report:', error));
}

// Populate report table with data
function populateReportTable(data) {
    const tableBody = document.getElementById('report-table-body');
    tableBody.innerHTML = '';  // Clear previous rows

    data.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.date}</td>
            <td>${row.small_bottles}</td>
            <td>${row.medium_bottles}</td>
            <td>${row.large_bottles}</td>
            <td>${row.total_bottles}</td>
        `;
        tableBody.appendChild(tr);
    });
}

// Render chart with report data
function renderReportChart(data) {
    const dates = data.map(row => row.date);
    const smallBottles = data.map(row => row.small_bottles);
    const mediumBottles = data.map(row => row.medium_bottles);
    const largeBottles = data.map(row => row.large_bottles);
    const totalBottles = data.map(row => row.total_bottles);

    const ctx = document.getElementById('report-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Small Bottles',
                    data: smallBottles,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                },
                {
                    label: 'Medium Bottles',
                    data: mediumBottles,
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                },
                {
                    label: 'Large Bottles',
                    data: largeBottles,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                },
                {
                    label: 'Total Bottles',
                    data: totalBottles,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Bottles Collected' } }
            }
        }
    });
}

// Fetch all report data immediately when the page loads
fetchDailyReport();
    </script>
            
                
    
        
</body>
</html>
