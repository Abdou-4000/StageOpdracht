<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* Set the body to use flexbox to center the map */
    body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f0f0f0;
      font-family: Arial, sans-serif;
    }

    /* Container for map and search box */
    .map-container {
      position: relative;
      height: 79vh;
      width: 75vw;
      overflow: hidden;
      border-radius: 40px;
    }

    #map {
      height: 79vh;
      width: 75vw;
      border-radius: 40px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* Ensure zoom controls respect rounded corners */
    .leaflet-control-zoom {
      border-radius: 10px;
      margin: 10px;
      border: none;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .leaflet-control-zoom a {
      border-radius: 5px;
      margin: 2px;
    }

    /* Adjust attribution control */
    .leaflet-control-attribution {
      background: rgba(255, 255, 255, 0.7);
      border-radius: 5px;
      padding: 5px;
      margin: 10px;
      right: 10px;
      left: auto;
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
      gap: 10px;
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
      flex-direction: column;
      gap: 10px;
    }

    .filter-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .filter-label {
      font-size: 14px;
      margin-right: 10px;
      font-weight: bold;
      white-space: nowrap;
    }

    .filter-select, .radius-input {
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
      padding: 5px 10px;
      font-size: 14px;
    }

    .hover-popup .leaflet-popup-tip {
      background-color: #2c3e50;
    }

    .click-popup .leaflet-popup-content-wrapper {
      background-color: #f0f0f0;
      color: black;
    }

    .click-popup .leaflet-popup-tip {
      background-color: #f0f0f0;
    }
  </style>
</head>
<body>
  <div class="map-container">
    <div class="control-container">
      <div class="search-box">
        <input type="text" id="search-input" placeholder="Search for a teacher...">
        <button id="search-button"><i class="fas fa-search"></i></button>
      </div>
      <div id="search-results"></div>
      
      <div class="filter-dropdown">
        <div class="filter-row">
          <span class="filter-label">Category:</span>
          <select id="category-filter" class="filter-select">
            <option value="all">All Categories</option>
            <!-- Categories will be added here dynamically -->
          </select>
        </div>
        <div class="filter-row">
          <span class="filter-label">Radius (km):</span>
          <input type="number" id="radius-filter" class="radius-input" 
                 min="1" max="50" value="15" 
                 placeholder="Max distance">
        </div>
      </div>
    </div>
    <div id="map"></div>
  </div>

  <!-- Load Leaflet's JavaScript -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Expose teachers data to JavaScript
    const teachersData = @json($teachers);

    // Initialize the map
    const map = L.map('map', {
      zoomControl: true,
      attributionControl: true
    }).setView([50.996, 5.538], 16.5);

    // Custom marker icon
    var Marker = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
        iconSize: [55, 55],
        popupAnchor: [0, -25],
        closeButton: true,
    });

    // Load tiles from OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Companies now come from server-side data
    const companies = teachersData;

    // Global variables to store user location
    let userLocation = null;

    // Function to calculate distance between two points using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
      const R = 6371; // Radius of the earth in km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c; // Distance in kilometers
    }

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

    // Global markers object to support search
    const markers = {};
    
    function createMarkers() {
      // Clear all existing markers
      markersLayer.clearLayers();
      
      // Get filter values
      const selectedCategory = categorySelect.value;
      const maxRadius = parseFloat(document.getElementById('radius-filter').value);
      const searchQuery = document.getElementById('search-input').value.trim().toLowerCase();
      
      // Filter companies
      let filteredCompanies = companies;
      
      // Filter by category if not 'all'
      if (selectedCategory !== 'all') {
        filteredCompanies = filteredCompanies.filter(company => 
          company.category === selectedCategory
        );
      }
      
      // Filter by search query
      if (searchQuery) {
        filteredCompanies = filteredCompanies.filter(company => 
          company.name.toLowerCase().includes(searchQuery)
        );
      }
      
      // Filter by radius if user location is known
      if (userLocation) {
        filteredCompanies = filteredCompanies.filter(company => {
          const distance = calculateDistance(
            userLocation.lat, userLocation.lng, 
            company.lat, company.lng
          );
          return distance <= maxRadius;
        });
      }
      
      filteredCompanies.forEach(company => {
        // Calculate distance
        const distance = userLocation 
          ? calculateDistance(userLocation.lat, userLocation.lng, company.lat, company.lng).toFixed(2)
          : 'N/A';
        
        // Hover popup content (simplified)
        const hoverPopupContent = `
          <div class="hover-popup">
            <strong>${company.compname}</strong>
          </div>
        `;
        
        // Click popup content
        const clickPopupContent = `
          <div class="click-popup">
            <strong>${company.compname}</strong><br>
            Teacher: ${company.name}<br>
            Location: ${company.details.location}<br>
            Email: ${company.details.email}<br>
            Phone: ${company.details.phone}<br>
            Distance: ${distance} km
          </div>
        `;
        
        // Create marker
        const marker = L.marker([company.lat, company.lng], {
          icon: Marker
        });
        
        // Create hover popup
        const hoverPopup = L.popup({
          closeButton: false,
          autoClose: false,
          closeOnClick: false,
          className: 'hover-popup'
        }).setContent(hoverPopupContent);
        
        // Create click popup
        const clickPopup = L.popup({
          autoClose: false,
          closeOnClick: false,
          className: 'click-popup'
        }).setContent(clickPopupContent);
        
        marker.on('mouseover', function(e) {
  // Only show hover popup if no click popup is open
  if (!marker.isPopupOpen()) {
    // Adjust the popup position to account for marker icon size
    const popupOptions = {
      offset: L.point(0, -15), // Adjust this value based on your icon
      className: 'hover-popup',
      closeButton: false
    };
    
    L.popup(popupOptions)
      .setLatLng(marker.getLatLng()) // Use marker's center instead of event latlng
      .setContent(hoverPopupContent)
      .openOn(map);
  }
});
        
        marker.on('mouseout', function() {
          // Close hover popup if it's open and no click popup is open
          if (!marker.isPopupOpen()) {
            map.closePopup(hoverPopup);
          }
        });
        
        // Modify click event to close hover popup
        marker.on('click', function() {
          // Close any existing hover popup
          map.closePopup(hoverPopup);
        });
        
        // Bind click popup
        marker.bindPopup(clickPopup);
        
        // Add to layer group
        markersLayer.addLayer(marker);
        
        // Store marker reference
        markers[company.name.toLowerCase()] = marker;
      });
    }

    // Add global click event to close popups when clicking outside
    map.on('click', function(e) {
      // Check if the click is not on a marker
      if (!e.originalEvent.target.closest('.leaflet-marker-icon')) {
        // Close all popups
        map.closePopup();
      }
    });

    // Function to handle geolocation
    function onLocationFound(e) {
      const radius = e.accuracy / 2;

      // Store user location
      userLocation = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
      };

      // Add a marker at the user's location
      L.marker(e.latlng).addTo(map)
        .bindPopup("You are within " + radius + " meters from this point").openPopup();

      // Set the map view to user's location
      map.setView(e.latlng, 17);

      // Create markers with location-based filtering
      createMarkers();
    }

    // Function to handle geolocation error
    function onLocationError(e) {
      alert(e.message);
      // If geolocation fails, set default view and create markers
      map.setView([50.996, 5.538], 17);
      createMarkers();
    }

    // Request user's location
    map.locate({setView: false, maxZoom: 17});

    // Add event listeners for geolocation
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    // Add event listeners for filters
    categorySelect.addEventListener('change', createMarkers);
    document.getElementById('radius-filter').addEventListener('change', createMarkers);

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
      
      // Perform filtering using createMarkers
      createMarkers();
      
      // Get currently filtered companies
      const selectedCategory = categorySelect.value;
      const maxRadius = parseFloat(document.getElementById('radius-filter').value);
      
      let visibleCompanies = companies;
      
      // Apply category filter
      if (selectedCategory !== 'all') {
        visibleCompanies = visibleCompanies.filter(company => 
          company.category === selectedCategory
        );
      }
      
      // Apply radius filter
      if (userLocation) {
        visibleCompanies = visibleCompanies.filter(company => {
          const distance = calculateDistance(
            userLocation.lat, userLocation.lng, 
            company.lat, company.lng
          );
          return distance <= maxRadius;
        });
      }
      
      // Filter by search query
      const filteredCompanies = visibleCompanies.filter(company => 
        company.name.toLowerCase().includes(query)
      );
      
      // Display results
      searchResults.style.display = filteredCompanies.length > 0 ? 'block' : 'none';
      
      if (filteredCompanies.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item">No teachers found</div>';
        return;
      }
      
      filteredCompanies.forEach(company => {
        const resultItem = document.createElement('div');
        resultItem.className = 'search-result-item';
        
        // Calculate and display distance if user location is known
        const distance = userLocation 
          ? calculateDistance(userLocation.lat, userLocation.lng, company.lat, company.lng).toFixed(2)
          : 'N/A';
        
        resultItem.innerHTML = `${company.name} - ${company.category} (${distance} km)`;
        
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

    // Initial markers creation
    createMarkers();
  </script>
</body>
</html>