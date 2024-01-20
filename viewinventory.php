<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        #dashboard {
            display: flex;
            height: 100vh; /* Use viewport height to make sure the layout covers the entire screen */
        }

        #sidebar {
            width: 150px;
            background-color: #333;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            position: fixed; /* Fix the sidebar position */
            height: 100%;
        }

        #content {
            flex: 1;
            padding: 20px;
            margin-left: 170px; /* Adjust content area margin to accommodate the fixed sidebar */
        }

        #sidebar a {
            color: #fff;
            text-decoration: none;
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        #sidebar i {
            margin-right: 10px;
        }

        #sidebar a:not(:last-child) {
            margin-bottom: 60px;
        }

        #user-info {
            display: none;
            color: #fff;
            margin-top: 5px;
            padding: 10px;
            background-color: #555;
            border-radius: 15px;
        }

        #content {
            flex: 1;
            padding: 20px;
        }

        #main-entry-table-container {
            margin-top: 20px;
        }

        #main-entry-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        #main-entry-table th,
        #main-entry-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        #main-entry-table th {
            background-color: #3498db;
            color: #fff;
        }

        #main-entry-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .more-info-button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 2px 2px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #5cb85c;
            color: white;
        }

        .modal-body {
            padding: 2px 16px;
        }

        #search-bar {
            margin-top: 20px;
        }

        #search-bar label {
            margin-right: 10px;
        }

        #search-bar input {
            margin-right: 10px;
        }

        #search-bar button {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div id="dashboard">
        <div id="sidebar">
            <div class="welcome-message" id="welcome-message"></div>
            <a href="#" id="user-icon" onclick="toggleUserInfo();"><i class="fas fa-user"></i> User</a>
            <div id="user-info">
                <!-- User info will be displayed here using JavaScript -->
            </div>
            <a href="notifications.html"><i class="fas fa-bell"></i> Notifications</a>
            <a href="mpesa-c2b.html"><i class="fas fa-coins"></i> Mpesa C2B</a>
            <a href="mpesa-b2b.html"><i class="fas fa-exchange-alt"></i> Mpesa B2B</a>
            <a href="home.html" onclick="redirectToPage('home.html');"><i class="fas fa-home"></i> Dashboard</a>
            <a href="#" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div id="content">
            <!-- Store Selection Options -->
            <div id="store-selection">
                <label for="store-type">Select Store Type:</label>
                <select id="store-type" onchange="handleStoreTypeChange()">
                    <option value="mainstore">Main Store</option>
                    <?php
                    // Check if there are satellite stores in the session
                    $satelliteStores = $_SESSION['satellite_stores'] ?? null;

                    if ($satelliteStores) {
                        echo '<option value="satellite">Satellite Stores</option>';
                    }
                    ?>
                </select>

                <!-- Satellite Store Buttons (hidden by default) -->
                <div id="satellite-buttons" class="satellite-buttons" style="display: none;">
                    <?php
                    foreach ($satelliteStores as $satelliteStore) {
                        echo '<button class="satellite-button" onclick="handleSatelliteButtonClick(\'' . $satelliteStore['location_name'] . '\')">' . $satelliteStore['location_name'] . '</button>';
                    }
                    ?>
                </div>
            </div>

            <!-- Search Bar -->
            <div id="search-bar">
                <label for="product-search">Search Product:</label>
                <input type="text" id="product-search" placeholder="Enter product name">
                <button onclick="searchProduct()">Search</button>
            </div>

            <!-- Inventory Table -->
            <h1>Welcome to Inventory Management</h1>
            <p>This is where your inventory data will be displayed.</p>

            <table id="main-entry-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Total Quantity</th>
                        <th>Quantity Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="main-entry-table-body">
                    <!-- Main entry data will be dynamically inserted here -->
                </tbody>
            </table>

            <!-- Modal for Detailed Entries -->
            <div id="detailed-entries-modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <div id="modal-body-content">
                        <!-- Detailed entry data will be dynamically inserted here -->
                    </div>
                </div>
            </div>

            <!-- Alert Message -->
            <div id="alert-message" class="alert-message" style="display: none;"></div>

            <!-- Include your JavaScript scripts here -->
            <script>
                // Your existing JavaScript code here

                    // View Inventory function
                function viewInventory() {
                    // Fetch main entry data and store information from session
                    const mainEntryDataSession = <?php echo json_encode($_SESSION['main_store_inventory_data'] ?? $_SESSION['satellite_inventory_data'] ?? null); ?>;
                    const storeTypeSession = <?php echo json_encode($_SESSION['storeType'] ?? null); ?>;

                    if (mainEntryDataSession) {
                        // Display store type
                        displayStoreType(storeTypeSession);

                        // Update the content of the current page with the main entry data
                        displayMainEntryData(mainEntryDataSession);
                    } else {
                        showAlert('No main entry data available.');
                    }
                }
                // Display store type
                function displayStoreType(storeType) {
                    const welcomeMessage = document.getElementById('welcome-message');
                    welcomeMessage.textContent = `Welcome to ${storeType} Inventory Management`;
                }

                // Display main entry data
                function displayMainEntryData(mainEntryData, searchTerm) {
                    const mainEntryTableBody = document.getElementById('main-entry-table-body');
                    mainEntryTableBody.innerHTML = ''; // Clear existing content

                    mainEntryData.forEach((entry) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${entry.product_name}</td>
                            <td>${entry.category}</td>
                            <td>${entry.total_quantity}</td>
                            <td>${entry.quantity_description}</td>
                            <td>
                                <button class="more-info-button" onclick="showDetailedEntries(${entry.id})">More Info</button>
                            </td>
                        `;
                        mainEntryTableBody.appendChild(row);
                    });
                }

                // Show detailed entries for the selected main entry ID
                function showDetailedEntries(mainEntryId) {
                    // Fetch detailed entries for the selected main entry ID
                    const detailedEntries = <?php echo json_encode(getDetailedEntries($mainEntryId)); ?>;

                    // Display detailed entries in the modal
                    displayDetailedEntries(detailedEntries);
                    // Show the modal
                    openModal();
                }

                // Display detailed entries in the modal
                function displayDetailedEntries(detailedEntries) {
                    const modalBodyContent = document.getElementById('modal-body-content');
                    modalBodyContent.innerHTML = ''; // Clear existing content

                    detailedEntries.forEach((entry) => {
                        const paragraph = document.createElement('p');
                        paragraph.textContent = `${entry.field_name}: ${entry.field_value}`;
                        modalBodyContent.appendChild(paragraph);
                    });
                }

                // Open modal
                function openModal() {
                    const modal = document.getElementById('detailed-entries-modal');
                    modal.style.display = 'block';
                }

                // Close modal
                function closeModal() {
                    const modal = document.getElementById('detailed-entries-modal');
                    modal.style.display = 'none';
                }

                // Call viewInventory when the page is loaded
                document.addEventListener('DOMContentLoaded', function () {
                    viewInventory();
                });

                // New functions for store selection
                function handleStoreTypeChange() {
                    const storeTypeSelect = document.getElementById('store-type');
                    const selectedStoreType = storeTypeSelect.value;

                    // Save selected store type to session
                    <?php
                    $_SESSION['store_type'] = "' + selectedStoreType + '";
                    ?>
                }

                function handleSatelliteButtonClick(locationName) {
                    // Your existing handleSatelliteButtonClick function
                }

                function setStoreSessionData(storeName, locationName, locationType) {
                    // Your existing setStoreSessionData function
                }
            </script>
        </div>
    </div>

    <!-- Include your additional JavaScript scripts here -->
</body>

</html>

