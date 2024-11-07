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
            background-image: url('../drawable/webbackground.jpg'); /* Set the background image */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the background image */
            background-repeat: no-repeat; /* Prevent repeating of the background image */
            color: #333;
        }

        .dashboard {
            display: flex;
            width: 100%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.5); /* Slightly transparent for a subtle effect */
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: width 0.3s ease; /* Smooth transition */
        }

        .sidebar.collapsed {
            width: 60px; /* Collapsed width to show only icons */
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: opacity 0.3s ease;
        }

        .logo {
            width: 40px; /* Adjusted for visibility when collapsed */
            height: auto;
            margin-right: 0; /* Center logo in collapsed view */
        }

        /* Hide text when sidebar is collapsed */
        .brand-info h1,
        .nav-links a span {
            display: inline; /* Show by default */
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .brand-info h1,
        .sidebar.collapsed .nav-links a span {
            display: none; /* Hide when collapsed */
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
            background-color: rgba(0, 128, 0, 0.3); /* Transparent green on hover */
            color: white; /* White text on hover */
        }

        .sidebar.collapsed .nav-links a {
            justify-content: center;
            text-align: center;
        }

        .sidebar.collapsed .nav-links a i {
            margin-right: 0; /* Center icons in collapsed state */
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
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent white background */
            backdrop-filter: blur(10px); /* Background blur */
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Shadow for depth */
            padding: 20px;
            flex: 1 1 300px;
            max-width: 100%;
            min-width: 250px;
            border: 1px solid rgba(255, 255, 255, 0.5); /* Optional: border for effect */
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
            animation: pulse 1.5s infinite; /* Adding pulse animation */
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
            animation: bounce 1s infinite; /* Adding bounce animation */
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
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent for glass effect */
            backdrop-filter: blur(10px); /* Blur for glass effect */
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
            background-color: rgba(255, 235, 204, 0.9); /* Light semi-transparent background */
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
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
       /* Add Bin Button */
       .add-bin-btn {
    padding: 10px 20px;
    background-color: #015515;
    color: white;
    border: none;
    border-radius: 112px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.add-bin-btn:hover {
    background-color: #218838;
}


/* Default modal styling for all modals */
.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    width: 100%;
    max-width: 400px;
    animation: slideDown 0.3s ease;
}

.modal-content {
    padding: 20px;
}

/* Specific styling for the Map Modal */
.map-modal-content {
    max-width: 1000px; /* Set specific width for map modal */
    width: 100%;       /* Make it responsive */
}


.close-btn {
    float: right;
    font-size: 18px;
    color: #ff3333;
    cursor: pointer;
}

h2 {
    font-size: 24px;
    color: #333333;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 14px;
    color: #555555;
}

.form-group input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #cccccc;
    font-size: 14px;
}

.submit-btn {
    width: 50%;
    padding: 10px;
    background-color: #015515;
    color: white;
    border: none;
    border-radius: 112px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #0056b3;
}

/* Success Message Styling */
.success-message {
    display: none;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    font-size: 16px;
    opacity: 0;
    animation: fadeInOut 3s forwards;
}

/* Animations */
@keyframes slideDown {
    from {
        transform: translate(-50%, -60%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%);
        opacity: 1;
    }
}

@keyframes fadeInOut {
    0% { opacity: 0; }
    10% { opacity: 1; }
    80% { opacity: 1; }
    100% { opacity: 0; }
}
/* Fixed size for bin items */
.bin-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.bin-item {
    width: 150px; /* Fixed width */
    height: 180px; /* Fixed height */
    position: relative;
    text-align: center;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 8px;
    background-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Delete icon button styling */
.delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: none;
    border: none;
    color: #002904;
    cursor: pointer;
    font-size: 16px;
}

.delete-btn i {
    font-size: 18px;
}

img {
    height: 80px;
    width: 80px;
    margin-top: 20px;
}

/* Delete Confirmation Modal Styling */
#deleteConfirmModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    width: 100%;
    max-width: 400px;
    animation: slideDown 0.3s ease;
}

#deleteConfirmModal .modal-content {
    padding: 20px;
    text-align: center;
}

#deleteConfirmModal h2 {
    font-size: 18px;
    color: #333;
    margin-bottom: 20px;
}

/* Modal Buttons */
.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.cancel-btn, .delete-confirm-btn {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-btn {
    background-color: #ddd;
    color: #333;
}

.cancel-btn:hover {
    background-color: #bbb;
}

.delete-confirm-btn {
    background-color: #ff4d4d;
    color: white;
}

.delete-confirm-btn:hover {
    background-color: #d43f3f;
}

element.style {
    width: 100%;
    height: 400px;
}
#mapsModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 1000px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .map-modal-content {
            padding: 20px;
        }

        #search-bar {
            margin-bottom: 10px;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #map {
            width: 100%;
            height: 400px;
            margin-top: 15px;
            border-radius: 10px;
        }
        #location-search {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }
        #map-container {
            position: relative;
            width: 100%;
            height: 400px;
            border-radius: 10px;
        }

        /* Position the Add Bin Button in the upper right corner */
.add-bin-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 10px 20px;
    background-color: #015515;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.add-bin-btn:hover {
    background-color: #218838;
}

/* Bin List Container */
.bin-list-container {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 200px;
    z-index: 1;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Button to toggle dropdown */
.bin-list-btn {
    width: 100%;
    background-color: #005709;
    color: white;
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Rotate icon when expanded */
.bin-list-container.expanded .fa-chevron-down {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

/* Dropdown list styling */
.bin-list-items {
    display: none;
    max-height: 150px; /* Scrollable height */
    overflow-y: auto;
    background-color: white;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 5px;
    padding: 5px 10px;
}

/* Display list items when expanded */
.bin-list-container.expanded .bin-list-items {
    display: block;
}

.bin-list-item {
    padding: 8px;
    color: #005709;
    font-weight: bold;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
}

.bin-list-item:hover {
    background-color: #f0f0f0;
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
                <!-- Location Search Bar -->
                <input type="text" id="location-search" placeholder="Search for location...">
              <!-- Map Modal with Collapsible Bin List Icon -->
              
    <div id="map-container">
        <div id="map"></div>

        <!-- Bin List Overlay Icon in Upper Left Corner with Dropdown Style -->
        <div class="bin-list-container">
            <button class="bin-list-btn" id="toggleBinList">
                Bottle Bin List <i class="fas fa-chevron-down"></i>
            </button>
            <div id="binList" class="bin-list-items"></div>
        </div>
        <button id="addBinBtn" class="add-bin-btn">+ Add Bottle Bin</button>
    </div>

                <!-- Add Bin Modal -->
                <div id="addBinModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h2>Register New Bottle Bin</h2>
                        <form id="addBinForm">
                            <div class="form-group">
                                <label for="binCode">Bottle Bin Code:</label>
                                <input type="text" id="binCode" name="binCode" required>
                            </div>
                            <div class="form-group">
                                <label for="binAddress">Bottle Bin Address:</label>
                                <input type="text" id="binAddress" name="binAddress" required>
                            </div>
                           
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <button type="submit" class="submit-btn">Add Bin</button>
                        </form>
                    </div>
                </div>

                <!-- Success Message -->
                <div id="successMessage" class="success-message">Bottle Bin Successfully Added!</div>
                <div id="binContainer" class="bin-container"></div>
                <div id="deleteConfirmModal" class="modal">
                    <div class="modal-content">
                        <h2>Are you sure you want to delete this bin?</h2>
                        <div class="modal-buttons">
                            <button id="cancelDelete" class="cancel-btn">Cancel</button>
                            <button id="confirmDelete" class="delete-confirm-btn">Delete</button>
                        </div>
                    </div>
                </div>
            </section>
            <script>
             // Select elements
const binListContainer = document.querySelector('.bin-list-container');
const toggleBinList = document.getElementById('toggleBinList');
const binList = document.getElementById('binList');

// Toggle bin list visibility on button click
toggleBinList.addEventListener('click', () => {
    binListContainer.classList.toggle('expanded');
});

// Load bins into the bin list
function loadBinList() {
    fetch('../bins/get_bins.php')
        .then(response => response.json())
        .then(data => {
            binList.innerHTML = ""; // Clear list before populating

            data.forEach(bin => {
                const binItem = document.createElement("div");
                binItem.classList.add("bin-list-item");
                binItem.innerText = bin.bin_code;
                binItem.dataset.lng = bin.longitude;
                binItem.dataset.lat = bin.latitude;

                // Click event to navigate to bin location
                binItem.addEventListener("click", () => {
                    const lng = parseFloat(binItem.dataset.lng);
                    const lat = parseFloat(binItem.dataset.lat);
                    map.flyTo({ center: [lng, lat], zoom: 15 });
                });

                binList.appendChild(binItem);
            });
        })
        .catch(error => console.error('Error loading bins:', error));
}

// Load bins when the page loads
document.addEventListener('DOMContentLoaded', loadBinList);


            </script>
    <!-- JavaScript for Sidebar Hover Effect and Time/Weather -->
    <script>
        const sidebar = document.querySelector('.sidebar');

        // Expand sidebar on hover
        sidebar.addEventListener('mouseenter', () => {
            sidebar.classList.remove('collapsed');
        });

        // Collapse sidebar when the mouse leaves
        sidebar.addEventListener('mouseleave', () => {
            sidebar.classList.add('collapsed');
        });

    
    </script>
   
    <script>
        // JavaScript to handle modal and form submission
        document.getElementById('addBinBtn').onclick = function () {
            document.getElementById('addBinModal').style.display = 'block';
        };
        
        document.querySelector('.close-btn').onclick = function () {
            document.getElementById('addBinModal').style.display = 'none';
        };
        
       // Handle form submission
document.getElementById('addBinForm').onsubmit = function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('../bins/add_bin.php', {  // Updated path to include the bins folder
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);  // Check for success or error message
        document.getElementById('addBinModal').style.display = 'none';
        loadBins();  // Reload the list of bins
    })
    .catch(error => console.error('Error:', error));
};

 // Function to load bins
 function loadBins() {
            fetch('../bins/get_bins.php')
                .then(response => response.json())
                .then(data => {
                    const binContainer = document.getElementById('binContainer');
                    binContainer.innerHTML = '';
                    data.forEach(bin => {
                        const binElement = document.createElement('div');
                        binElement.classList.add('bin-item');
                        binElement.innerHTML = `
                            <button class="delete-btn" onclick="deleteBin('${bin.bin_code}')">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="../drawable/trashbinlogo.png" alt="Bin Icon">
                            <p>Code: ${bin.bin_code}</p>
                            <p>Address: ${bin.bin_address}</p>
                        `;
                        binContainer.appendChild(binElement);
                    });
                })
                .catch(error => console.error('Error:', error));
        }



let binToDelete = null; // Variable to store the bin code for deletion

// Function to open the delete confirmation modal
function deleteBin(binCode) {
    binToDelete = binCode;
    document.getElementById('deleteConfirmModal').style.display = 'block';
}

// Cancel delete action and close modal
document.getElementById('cancelDelete').onclick = function() {
    document.getElementById('deleteConfirmModal').style.display = 'none';
    binToDelete = null;
};

// Confirm delete action and close modal
document.getElementById('confirmDelete').onclick = function() {
    if (binToDelete) {
        fetch(`../bins/delete_bin.php?binCode=${binToDelete}`, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Display success message or error
            document.getElementById('deleteConfirmModal').style.display = 'none';
            loadBins();  // Reload bins after deletion
        })
        .catch(error => console.error('Error:', error));
        binToDelete = null;
    }
};


// JavaScript to handle modal and form submission
document.getElementById('addBinBtn').onclick = function () {
    document.getElementById('addBinModal').style.display = 'block';
};

document.querySelector('.close-btn').onclick = function () {
    document.getElementById('addBinModal').style.display = 'none';
};

// Handle form submission
document.getElementById('addBinForm').onsubmit = function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('../bins/add_bin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('addBinModal').style.display = 'none';
        showSuccessMessage();
        loadBins();  // Reload the list of bins
    })
    .catch(error => console.error('Error:', error));
};

// Show success message
function showSuccessMessage() {
    const successMessage = document.getElementById('successMessage');
    successMessage.style.display = 'block';
    setTimeout(() => {
        successMessage.style.display = 'none';
    }, 3000);  // Hide after 3 seconds
}

        </script>
  <script>
    // Initialize Mapbox with your access token
    mapboxgl.accessToken = 'pk.eyJ1IjoiZ2VyYWxkLTE5IiwiYSI6ImNtMzFyM25vNzBzeDYybXB6aThta3dlbnQifQ.A1AjkdcWJ7g3YIRuxeSvwQ';

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [120.9842, 14.5995], // Center on Manila, Philippines
        zoom: 12
    });

    let marker = new mapboxgl.Marker();
    fetch('../bins/get_bins.php')
    .then(response => response.json())
    .then(data => {
        console.log("Fetched data:", data); // This should log your bin data array
        const binListContainer = document.getElementById("binList");
        binListContainer.innerHTML = ""; // Clear the list before populating

        data.forEach(bin => {
            const binItem = document.createElement("div");
            binItem.classList.add("bin-list-item");
            binItem.innerText = bin.bin_code;
            binItem.dataset.lng = bin.longitude;
            binItem.dataset.lat = bin.latitude;

            binItem.addEventListener("click", () => {
                const lng = parseFloat(binItem.dataset.lng);
                const lat = parseFloat(binItem.dataset.lat);
                map.flyTo({ center: [lng, lat], zoom: 15 });
                marker.setLngLat([lng, lat]).addTo(map);
            });

            binListContainer.appendChild(binItem); // Append bin to list
            addTrashBinMarker(bin.longitude, bin.latitude, bin.bin_code);
        });
    })
    .catch(error => console.error('Error loading bins:', error));

        function addTrashBinMarker(lng, lat, binCode) {
            const el = document.createElement('div');
            el.className = 'trash-bin-marker';
            el.style.backgroundImage = 'url(../drawable/trashbinlogo.png)';
            el.style.width = '30px';
            el.style.height = '30px';
            el.style.backgroundSize = '100%';

            new mapboxgl.Marker(el)
                .setLngLat([lng, lat])
                .setPopup(new mapboxgl.Popup({ offset: 25 }).setText(`Bin Code: ${binCode}`))
                .addTo(map);
        }

        window.onload = loadBinList;

    // Place marker on map click, fetch address, and set form latitude/longitude values
    map.on('click', (e) => {
        const { lng, lat } = e.lngLat;
        marker.setLngLat([lng, lat]).addTo(map);

        // Set the hidden form fields for latitude and longitude
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;

        // Reverse geocode to fetch address for the clicked location
        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`)
            .then(response => response.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    const address = data.features[0].place_name;
                    document.getElementById("binAddress").value = address; // Auto-fill address field
                } else {
                    alert("Address not found for this location.");
                }
            })
            .catch(error => console.error('Error fetching address:', error));
    });

    // Event listener for location search
    document.getElementById('location-search').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = e.target.value;

            // Forward geocode the address
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?access_token=${mapboxgl.accessToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const [lng, lat] = data.features[0].center;

                        // Navigate map to the searched location
                        map.flyTo({
                            center: [lng, lat],
                            zoom: 14
                        });

                        // Place marker at the searched location
                        marker.setLngLat([lng, lat]).addTo(map);

                        // Auto-fill the address in the binAddress field
                        document.getElementById("binAddress").value = data.features[0].place_name;

                        // Update hidden form fields with lat/lng
                        document.getElementById("latitude").value = lat;
                        document.getElementById("longitude").value = lng;
                    } else {
                        alert("Location not found.");
                    }
                })
                .catch(error => console.error('Error fetching location:', error));
        }
    });

    // Function to add a marker for a trash bin on the map
    function addTrashBinMarker(lng, lat, binCode) {
        const el = document.createElement('div');
        el.className = 'trash-bin-marker';
        el.style.backgroundImage = 'url(../drawable/trashbinlogo.png)';
        el.style.width = '30px';
        el.style.height = '30px';
        el.style.backgroundSize = '100%';

        new mapboxgl.Marker(el)
            .setLngLat([lng, lat])
            .setPopup(new mapboxgl.Popup({ offset: 25 }).setText(`Bin Code: ${binCode}`))
            .addTo(map);
    }

    // Fetch saved bottle bins and add them to the map on page load
    function loadBins() {
        fetch('../bins/get_bins.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(bin => {
                    addTrashBinMarker(bin.longitude, bin.latitude, bin.bin_code);
                });
            })
            .catch(error => console.error('Error loading bins:', error));
    }

    window.onload = loadBins;

    // Handle form submission to add a new bin
    document.getElementById('addBinForm').onsubmit = function (event) {
        event.preventDefault();
        const binCode = document.getElementById("binCode").value;
        const address = document.getElementById("binAddress").value;
        const lat = document.getElementById("latitude").value;
        const lng = document.getElementById("longitude").value;

        if (!lat || !lng) {
            alert("Please select a location on the map.");
            return;
        }

        fetch('../bins/add_bin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ binCode, address, lat, lng })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTrashBinMarker(lng, lat, binCode);
                document.getElementById('addBinModal').style.display = 'none';
                showSuccessMessage();
            } else {
                console.error('Error saving bin:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Show success message
    function showSuccessMessage() {
        const successMessage = document.getElementById('successMessage');
        successMessage.style.display = 'block';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }
</script>
            
    
        
</body>
</html>
