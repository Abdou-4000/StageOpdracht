<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
       body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: Arial, sans-serif;
      background-color: white;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    /* Header styles */
    .header {
      width: 15px;   
      position: absolute;
      top: 0;
      left: 0;
      right: 60;
    }

    .header img {
      width: 200px;
      height: 200px;
      margin-top: -30px;
      margin-left: 25px;
      object-fit: contain;
    }

   .leaflet-control-attribution {
     left: 250px;
   }

   .leaflet-control-zoom {
     border-radius: 15px;
     margin: 2px;
   }

    /* Controls container for search and filters - positioned vertically on the right */
    .controls {
      position: absolute;
      top: 10px;
      right: 4px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      z-index: 1000;
    }

    /* Map container styles */
    .map-container {
      position: relative;
      margin-top: 70px;
      height: 80vh;
      width: 95vw;
      border-radius: 15px;
      overflow: hidden;
    }

    #map {
      clip-path: polygon( 0% 7.314%,0% 7.314%,0.041% 6.127%,0.16% 5.002%,0.351% 3.953%,0.606% 2.994%,0.92% 2.142%,1.286% 1.411%,1.698% 0.816%,2.148% 0.373%,2.632% 0.096%,3.141% 0%,63.05% 0%,63.05% 0%,63.559% 0.096%,64.043% 0.373%,64.493% 0.816%,64.905% 1.411%,65.271% 2.142%,65.585% 2.994%,65.84% 3.953%,66.031% 5.002%,66.15% 6.127%,66.191% 7.314%,66.191% 24.003%,66.191% 24.003%,66.232% 25.189%,66.351% 26.314%,66.541% 27.364%,66.797% 28.322%,67.111% 29.174%,67.477% 29.905%,67.888% 30.5%,68.339% 30.944%,68.822% 31.221%,69.332% 31.316%,96.859% 31.316%,96.859% 31.316%,97.368% 31.412%,97.852% 31.689%,98.303% 32.133%,98.714% 32.728%,99.08% 33.459%,99.394% 34.311%,99.649% 35.269%,99.84% 36.319%,99.959% 37.444%,100% 38.63%,100% 50%,100% 92.686%,100% 92.686%,99.959% 93.873%,99.84% 94.998%,99.649% 96.047%,99.394% 97.006%,99.08% 97.858%,98.714% 98.589%,98.303% 99.184%,97.852% 99.627%,97.368% 99.904%,96.859% 100%,3.141% 100%,3.141% 100%,2.632% 99.904%,2.148% 99.627%,1.698% 99.184%,1.286% 98.589%,0.92% 97.858%,0.606% 97.006%,0.351% 96.047%,0.16% 94.998%,0.041% 93.873%,0% 92.686%,0% 7.314% );
      height: 80vh;
      width: 95vw;
    }

    /* Search input styling */
    .search-container {
      position: relative;
      width: 590px;
    }
    
    #search-input {
      padding: 12px 20px;
      width: 100%;
      height: 60px;
      border-radius: 50px;
      border: 2px solid red;
      background-color: white;
      font-size: 16px;
      outline: none;
      box-sizing: border-box;
    }
    
    #search-button {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #f02d21;
      cursor: pointer;
      font-size: 18px;
    }

    /* Dropdown styling */
    .filter-select {
      padding: 12px 20px;
      padding-bottom: 10px;
      border-radius: 50px;
      border: none;
      background-color: #8b0000;
      color: white;
      font-size: 18px;
      appearance: none;
      -webkit-appearance: none;
      cursor: pointer;
      width: 590px;
      height: 60px;
      position: relative;
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>');
      background-repeat: no-repeat;
      background-position: right 20px center;
    }

    /* Custom styling for dropdown options */
    .filter-select option {
      background-color: white;
      color: #333;
      padding: 10px;
    }

    /* Radius input styling */
    .radius-container {
      display: flex;
      align-items: center;
      background-color: #333;
      color: white;
      border-radius: 50px;
      padding: 0 15px;
      width: 590px;
      height: 60px;
      box-sizing: border-box;
    }

    .radius-label {
      margin-right: 5px;
    }

    #radius-filter {
      width: 60px;
      padding: 12px 10px;
      border: none;
      background-color: transparent;
      color: white;
      font-size: 16px;
      text-align: right;
      -moz-appearance: textfield;
    }

    #radius-filter::-webkit-inner-spin-button, 
    #radius-filter::-webkit-outer-spin-button { 
      -webkit-appearance: none;
      margin: 0;
    }

    /* Search results styling */
    #search-results {
      position: absolute;
      top: 50px;
      left: 0;
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      z-index: 1000;
      display: none;
    }

    .search-result-item {
      padding: 10px 20px;
      cursor: pointer;
      border-bottom: 1px solid #eee;
    }

    .search-result-item:hover {
      background-color: #f5f5f5;
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
  <div class="header">
    <img src="{{ asset('assets/Logo.png') }}" alt="Logo">
  </div>

  <div class="map-container">
    <div class="controls">
      <div class="search-container">
        <input type="text" id="search-input" placeholder="Teacher">
        <button id="search-button"><i class="fas fa-search"></i></button>
        <div id="search-results"></div>
      </div>
      
      <select id="category-filter" class="filter-select">
        <option value="all">Categorie</option>
        <!-- Categories will be added here dynamically -->
      </select>
      
      <div class="radius-container">
        <span class="radius-label">Straal</span>
        <input type="number" id="radius-filter" min="1" max="50" value="15">
        <span>Km</span>
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
        iconSize: [45, 45],
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

    // Extract unique categories - UPDATED
    const allCategories = new Set();
    companies.forEach(company => {
      if (Array.isArray(company.category)) {
        company.category.forEach(cat => allCategories.add(cat));
      }
    });
    const categories = [...allCategories];

    // Populate the category dropdown - UPDATED
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
      
      // Filter by category if not 'all' - UPDATED
      if (selectedCategory !== 'all') {
        filteredCompanies = filteredCompanies.filter(company => 
          Array.isArray(company.category) && company.category.includes(selectedCategory)
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
        
        // Get categories as string for display
        const categoryDisplay = Array.isArray(company.category) 
          ? company.category.join(', ') 
          : 'Uncategorized';
        
        // Hover popup content (simplified)
        const hoverPopupContent = `
          <div class="hover-popup">
            <strong>${company.compname}</strong>
          </div>
        `;
        
        // Click popup content - UPDATED to show categories
        const clickPopupContent = `
          <div class="click-popup">
            <strong>${company.compname}</strong><br>
            Teacher: ${company.name}<br>
            Category: ${categoryDisplay}<br>
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
      
      // Apply category filter - UPDATED
      if (selectedCategory !== 'all') {
        visibleCompanies = visibleCompanies.filter(company => 
          Array.isArray(company.category) && company.category.includes(selectedCategory)
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
        
        // UPDATED to display categories as a comma-separated list
        const categoryDisplay = Array.isArray(company.category) 
          ? company.category.join(', ') 
          : 'Uncategorized';
        
        resultItem.innerHTML = `${company.name} - ${categoryDisplay} (${distance} km)`;
        
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