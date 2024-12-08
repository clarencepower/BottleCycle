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
.modal {
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 8px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .drop{
        background-color: #005709;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    }
    #bin-dropdown {
    width: 150px;  /* Adjust width as needed */
    background-color: #005709;
    color: white;
    padding: 10px;
    font-size: 13px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: left;
    appearance: none;
    /* Remove arrow for consistent look (optional) */
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
 <!-- View By Button -->
 <button id="viewByBtn" onclick="openModal()">View By</button>

<!-- Modal Structure -->
<div id="viewByModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h4>Select View Type</h4>
        <label for="viewByDropdown">View By:</label>
        <select class="drop" id="viewByDropdown" onchange="fetchDataByView()">
            <option value="day">Day</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
        </select>

        <label for="yearDropdown">Year:</label>
        <select class="drop" id="yearDropdown" onchange="fetchDataByYear()">
            <!-- Populate this dynamically with available years -->
        </select>

        <label for="binCodeDropdown">Bin Code:</label>
        <select class="drop" id="binCodeDropdown" onchange="fetchDataByView()">
            <!-- Bin codes will be dynamically populated -->
        </select>

        <div id="viewByResults">
            <!-- Results will be displayed here -->
        </div>
    </div>
</div>
       <!-- <button onclick="searchByDate()">Search</button> -->

                <!-- Bin Selection Dropdown -->
                <label for="bin-dropdown">Select Bin:</label>
                <select id="bin-dropdown" onchange="fetchDataByBin()">
                    <option value="">Select Bin</option>
                    <!-- The bin options will be populated dynamically -->
                </select>

                <!-- Generate Report Button -->
                <button onclick="generatePDFReport()">Generate Report</button>
                <button id="display-all-bins" onclick="displayAllBins()">Display All Bins</button>
                <button onclick="generatePDF()">Generate Report All Bins</button>
            </div>

            <div id="report-table-container">
                <table id="report-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th id="bin-code-header">Bin Code</th>
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
</main>

<script>
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

// Fetch data for the selected bin and populate the table
function fetchDataByBin() {
    const binId = document.getElementById('bin-dropdown').value;
    const date = document.getElementById('date-search').value;

    if (binId) {
        const url = `../Sensors/get_daily_reports.php?bin_code=${binId}&date=${date}`;
        fetch(url)
            .then(response => response.json())
            .then(data => populateTable(data))
            .catch(error => console.error('Error fetching data for selected bin:', error));
    }
}

// Display all bins when the "Display All Bins" button is clicked
function displayAllBins() {
    const date = document.getElementById('date-search').value;

    const url = `../Sensors/fetch_bottles_collected.php?date=${date}`; // Fetch all bins if no bin code is selected
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Show the "Bin Code" column when displaying all bins
            document.getElementById('bin-code-header').style.display = 'table-cell';
            populateTable(data);
        })
        .catch(error => console.error('Error fetching all bins data:', error));
}

// Function to populate the table with fetched data
function populateTable(data) {
    const tableBody = document.getElementById('report-table-body');
    tableBody.innerHTML = ''; // Clear the table body

    data.forEach(row => {
        const tr = document.createElement('tr');
       
        // Create table cells dynamically for each row
        const dateCell = document.createElement('td');
        dateCell.textContent = row.formatted_timestamp;
        tr.appendChild(dateCell);

        const binCodeCell = document.createElement('td');
        binCodeCell.textContent = row.bin_code;
        tr.appendChild(binCodeCell);

        const totalSmallCell = document.createElement('td');
        totalSmallCell.textContent = row.total_small;
        tr.appendChild(totalSmallCell);

        const totalMediumCell = document.createElement('td');
        totalMediumCell.textContent = row.total_medium;
        tr.appendChild(totalMediumCell);

        const totalLargeCell = document.createElement('td');
        totalLargeCell.textContent = row.total_large;
        tr.appendChild(totalLargeCell);

        const totalBottlesCell = document.createElement('td');
        totalBottlesCell.textContent = row.total_bottles;
        tr.appendChild(totalBottlesCell);

        tableBody.appendChild(tr);
    });
}

// Generate PDF Report
function generatePDFReport() {
    const binId = document.getElementById('bin-dropdown').value;
    const date = document.getElementById('date-search').value;
    

    if (binId) {
        const url = `../Users/generate_report.php?bin_code=${binId}&date=${date}`;
        window.location.href = url;  // Trigger the report generation by redirecting
    } else {
        alert("Please select a bin.");
    }
}

// Initialize by fetching bin IDs
fetchBinIds();
</script>

<script>
        function generatePDF() {
            fetch('../Sensors/fetch_bottles_collected.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error fetching data');
                    } else {
                        // Pass the fetched data to the PHP script for PDF generation
                        let formData = new FormData();
                        formData.append('bins_data', JSON.stringify(data));
                        
                        fetch('../Users/generate_pdf.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.blob())
                        .then(blob => {
                            // Create a link to download the PDF
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'bin_summary_report.pdf';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
  <script>
        // Open the Modal
        function openModal() {
            document.getElementById("viewByModal").style.display = "block";
            populateYearDropdown(); // Populate the year dropdown
            populateBinCodeDropdown(); // Populate the bin code dropdown
        }

        // Close the Modal
        function closeModal() {
            document.getElementById("viewByModal").style.display = "none";
        }

        // Populate Year Dropdown
        function populateYearDropdown() {
            const yearDropdown = document.getElementById("yearDropdown");
            const currentYear = new Date().getFullYear();
            yearDropdown.innerHTML = "";

            for (let year = currentYear; year >= currentYear - 10; year--) {
                const option = document.createElement("option");
                option.value = year;
                option.textContent = year;
                yearDropdown.appendChild(option);
            }
        }

        // Populate Bin Code Dropdown
        async function populateBinCodeDropdown() {
            const binCodeDropdown = document.getElementById("binCodeDropdown");
            binCodeDropdown.innerHTML = "<option value=''>Select Bin Code</option>"; // Add default option

            try {
                const response = await fetch('../Sensors/get_bin_ids.php'); // Adjust path if necessary
                const bins = await response.json();

                if (bins.error) {
                    console.error("Error:", bins.error);
                    return;
                }

                bins.forEach(bin => {
                    const option = document.createElement("option");
                    option.value = bin.bin_code;
                    option.textContent = bin.bin_code;
                    binCodeDropdown.appendChild(option);
                });
            } catch (error) {
                console.error("Error fetching bin codes:", error);
            }
        }

        // Fetch Data Based on Filters
        function fetchDataByView() {
            const viewBy = document.getElementById("viewByDropdown").value;
            const year = document.getElementById("yearDropdown").value;
            const binCode = document.getElementById("binCodeDropdown").value;

            console.log(`Fetching data for viewBy: ${viewBy}, year: ${year}, binCode: ${binCode}`);

            fetch(`../Sensors/get_reports_by_view.php?viewBy=${viewBy}&year=${year}&binCode=${binCode}`)
                .then(response => {
                    console.log("Raw response:", response);
                    return response.json();
                })
                .then(data => {
                    console.log("Parsed data:", data);
                    displayResults(data, viewBy);
                })
                .catch(error => console.error("Error fetching data:", error));
        }

        function displayResults(data, viewBy) {
            console.log("Data to display:", data);
            const resultsContainer = document.getElementById("viewByResults");
            resultsContainer.innerHTML = ""; // Clear previous results

            if (data.length === 0) {
                resultsContainer.innerHTML = "<p>No data available for the selected period.</p>";
                return;
            }

            const table = document.createElement("table");
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>${viewBy === "day" ? "Date" : viewBy === "month" ? "Month" : viewBy === "week" ? "Week" : "Year"}</th>
                        <th>Small Bottles</th>
                        <th>Medium Bottles</th>
                        <th>Large Bottles</th>
                        <th>Total Bottles</th>
                    </tr>
                </thead>
            `;

            const tbody = document.createElement("tbody");
            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.date}</td>
                    <td>${row.small_bottles}</td>
                    <td>${row.medium_bottles}</td>
                    <td>${row.large_bottles}</td>
                    <td>${row.total_bottles}</td>
                `;
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);
            resultsContainer.appendChild(table);
        }
    </script>
</body>
