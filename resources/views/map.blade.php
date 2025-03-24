<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaflet Map with Company Search and Category Filter</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* Set the body to use flexbox to center the map */
    body {
      display: flex;
      flex-direction: column;
      justify-content: center; /* Center horizontally */
      align-items: center;     /* Center vertically */
      height: 100vh;           /* Full screen height */
      margin: 0;               /* Remove default margin */
      background-color: #f0f0f0; /* Light background color */
      font-family: Arial, sans-serif;
    }

    /* Container for map and search box */
    .map-container {
      position: relative;
      height: 79vh; /* 79% of the screen height */
      width: 75vw;  /* 75% of the screen width */
    }

    /* Set the map to be 35% of the screen size */
    #map {
      height: 79vh;
      width: 75vw;
      border-radius: 40px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }
    .leaflet-top,
    .leaflet-bottom {
      padding: 15px;
    }

    /* Search and filter container */
    .control-container {
      position: absolute;
      right: 20px;
      top: 20px;
      z-index: 1000;
      background: white;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      width: 300px;
      display: flex;
      flex-direction: column;
    }

    .search-box {
      display: flex;
      margin-bottom: 10px;
    }

    #search-input {
      flex-grow: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px 0 0 5px;
      font-size: 14px;
    }

    #search-button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      cursor: pointer;
      border-radius: 0 5px 5px 0;
    }

    #search-button:hover {
      background-color: #45a049;
    }

    #search-results {
      max-height: 100px;
      overflow-y: auto;
      display: none;
      margin-bottom: 10px;
    }

    .search-result-item {
      padding: 10px;
      cursor: pointer;
      border-bottom: 1px solid #eee;
    }

    .search-result-item:hover {
      background-color: #f5f5f5;
    }

    /* Filter dropdown styles */
    .filter-dropdown {
      display: flex;
      align-items: center;
    }

    .filter-label {
      font-size: 14px;
      margin-right: 10px;
      font-weight: bold;
    }

    .filter-select {
      flex-grow: 1;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    /* Custom popup styles */
    .hover-popup .leaflet-popup-content-wrapper {
      background-color: #2c3e50;
      color: white;
    }

    .hover-popup .leaflet-popup-tip {
      background-color: #2c3e50;
    }
  </style>
</head>
<body>
  <div class="map-container">
    <!-- Combined search and filter panel -->
    <div class="control-container">
      <div class="search-box">
        <input type="text" id="search-input" placeholder="Search for a company...">
        <button id="search-button"><i class="fas fa-search"></i></button>
      </div>
      <div id="search-results"></div>
      
      <!-- Category filter dropdown -->
      <div class="filter-dropdown">
        <span class="filter-label">Filter by:</span>
        <select id="category-filter" class="filter-select">
          <option value="all">All Categories</option>
          <!-- Categories will be added here dynamically -->
        </select>
      </div>
    </div>
    
    <div id="map"></div>
  </div>

  <!-- Load Leaflet's JavaScript -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Initialize the map
    const map = L.map('map', {
      zoomControl: true,
      attributionControl: true
    }).setView([50.996, 5.538], 16.5);

    // Custom marker icon
    var Marker = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
        iconSize: [55, 55], // size of the icon
        popupAnchor: [0, -25], // point from which the popup should open
        closeButton: true,
    });

    // Load tiles from OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Company database with locations and categories
    const companies = [
      {
        name: "SyntraPXL",
        lat: 50.9973,
        lng: 5.5367,
        category: "Education",
        details: {
          location: "T2-Campus",
          hours: "Mon-Fri, 9:00 AM - 5:00 PM"
        }
      },
      {
        name: "Corda Campus",
        lat: 50.9519,
        lng: 5.3535,
        category: "Business",
        details: {
          location: "Hasselt",
          hours: "Mon-Fri, 8:00 AM - 6:00 PM"
        }
      },
      {
        name: "Thor Park",
        lat: 50.9938,
        lng: 5.5370,
        category: "Technology",
        details: {
          location: "Genk",
          hours: "Mon-Fri, 8:30 AM - 5:30 PM"
        }
      },
      {
        name: "PXL University",
        lat: 50.9289,
        lng: 5.3897,
        category: "Education",
        details: {
          location: "Hasselt",
          hours: "Mon-Fri, 8:00 AM - 10:00 PM"
        }
      },
      {
        name: "ucll University",
        lat: 50.9279,
        lng: 5.3850,
        category: "Education",
        details: {
          location: "Diepenbeek",
          hours: "Mon-Fri, 8:00 AM - 8:00 PM"
        }
      }
    ];

    // Create a layer group for all markers
    const markersLayer = L.layerGroup().addTo(map);

    // Extract unique categories
    const categories = [...new Set(companies.map(company => company.category))];

    // Populate the category dropdown
    const categorySelect = document.getElementById('category-filter');
    categories.forEach(category => {
      const option = document.createElement('option');
      option.value = category;
      option.textContent = category;
      categorySelect.appendChild(option);
    });

    // Create markers for all companies
    const markers = {};
    
    function createMarkers(filteredCompanies = companies) {
      // Clear all existing markers
      markersLayer.clearLayers();
      
      filteredCompanies.forEach(company => {
        // Create popup contents
        const hoverPopupContent = `<strong>${company.name}</strong>`;
        const clickPopupContent = `
          <strong>${company.name}</strong><br>
          Category: ${company.category}<br>
          Location: ${company.details.location}<br>
          Working Hours: ${company.details.hours}
        `;
        
        // Create marker
        const marker = L.marker([company.lat, company.lng], {
          icon: Marker
        });
        
        // Add to layer group
        markersLayer.addLayer(marker);
        
        // Store marker reference
        markers[company.name.toLowerCase()] = marker;
        
        // Create popups
        const hoverPopup = L.popup({
          closeButton: false,
          autoClose: true,
          className: 'hover-popup'
        }).setContent(hoverPopupContent);
        
        const clickPopup = L.popup({
          autoClose: false,
          closeOnClick: false
        }).setContent(clickPopupContent);
        
        // Define marker interaction
        let isClickPopupOpen = false;
        
        marker.on('mouseover', function() {
          if (!isClickPopupOpen) {
            marker.bindPopup(hoverPopup).openPopup();
          }
        });
        
        marker.on('mouseout', function() {
          if (!isClickPopupOpen) {
            marker.closePopup();
          }
        });
        
        marker.on('click', function() {
          marker.unbindPopup();
          marker.bindPopup(clickPopup).openPopup();
          isClickPopupOpen = true;
        });
        
        // Close popups when clicking elsewhere
        map.on('click', function(e) {
          if (!marker.getLatLng().equals(map.mouseEventToLatLng(e.originalEvent)) && isClickPopupOpen) {
            marker.closePopup();
            isClickPopupOpen = false;
          }
        });
      });
    }

    // Initialize all markers
    createMarkers();

    // Apply filter function when dropdown changes
    categorySelect.addEventListener('change', function() {
      const selectedCategory = this.value;
      
      let filteredCompanies;
      if (selectedCategory === 'all') {
        filteredCompanies = companies;
      } else {
        filteredCompanies = companies.filter(company => company.category === selectedCategory);
      }
      
      // Update markers
      createMarkers(filteredCompanies);
    });

    // Search functionality
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    const searchResults = document.getElementById('search-results');

    // Function to perform search
    function performSearch() {
      const query = searchInput.value.trim().toLowerCase();
      if (query.length === 0) return;
      
      // Clear previous search results
      searchResults.innerHTML = '';
      
      // Get the currently visible companies based on selected filter
      const selectedCategory = categorySelect.value;
      let visibleCompanies;
      
      if (selectedCategory === 'all') {
        visibleCompanies = companies;
      } else {
        visibleCompanies = companies.filter(company => company.category === selectedCategory);
      }
      
      // Filter visible companies based on the query
      const filteredCompanies = visibleCompanies.filter(company => 
        company.name.toLowerCase().includes(query)
      );
      
      // Display results
      searchResults.style.display = filteredCompanies.length > 0 ? 'block' : 'none';
      
      if (filteredCompanies.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item">No companies found</div>';
        return;
      }
      
      filteredCompanies.forEach(company => {
        const resultItem = document.createElement('div');
        resultItem.className = 'search-result-item';
        resultItem.innerHTML = `${company.name} - ${company.category}`;
        resultItem.addEventListener('click', () => {
          // Center map on company
          map.setView([company.lat, company.lng], 15);
          
          // Open popup for the company
          const marker = markers[company.name.toLowerCase()];
          if (marker) {
            marker.openPopup();
          }
          
          // Hide search results
          searchResults.style.display = 'none';
        });
        searchResults.appendChild(resultItem);
      });
    }

    // Search button click event
    searchButton.addEventListener('click', performSearch);
    
    // Enter key press in search input
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });
    
    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
      if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchButton) {
        searchResults.style.display = 'none';
      }
    });
  </script>
</body>
</html>