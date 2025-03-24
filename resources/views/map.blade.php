<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Leaflet Map Test</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    /* Set the body to use flexbox to center the map */
    body {
      display: flex;
      justify-content: center; /* Center horizontally */
      align-items: center;     /* Center vertically */
      height: 100vh;           /* Full screen height */
      margin: 0;               /* Remove default margin */
      background-color: #f0f0f0; /* Light background color */
    }
    
    /* Set the map to be 35% of the screen size */
    #map {
      height: 79vh; /* 35% of the screen height */
      width: 75vw;  /* 35% of the screen width */
      border-radius: 40px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <!-- This loads Leaflet's JavaScript to make the map work -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Start the map at a certain place on earth (T-2 campus)
    const map = L.map('map', {
      zoomControl: true,
      attributionControl: true
    }).setView([50.996, 5.538], 16.5);
    
    var Marker = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
       iconSize:     [60, 60], // size of the icon
        popupAnchor:  [0, -25], // point from which the popup should open relative to the iconAnchor
        closeButton: true,
    });
    
    // Load tiles from OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    // Create popup contents
    const hoverPopupContent = '<strong>SyntraPXL</strong>';
    const clickPopupContent = `
      <strong>SyntraPXL</strong><br>
      Location: T2-Campus<br>
      Working Hours: Mon-Fri, 9:00 AM - 5:00 PM
    `;
    
    // Add a marker (a pin on the map)
    var marker = L.marker([50.9973, 5.5367], {icon: Marker}).addTo(map);
        
    // Create popup objects
    const hoverPopup = L.popup({
      closeButton: false,
      autoClose: true,
      className: 'hover-popup'
    }).setContent(hoverPopupContent);
        
    const clickPopup = L.popup({
      autoClose: false,
      closeOnClick: false
    }).setContent(clickPopupContent);
        
    let isClickPopupOpen = false;
        
    // Hover popup (shows on hover)
    marker.on('mouseover', function() {
      // Only show hover popup if click popup is not open
      if (!isClickPopupOpen) {
        marker.bindPopup(hoverPopup).openPopup();
      }
    });
    
    marker.on('mouseout', function() {
      // Only close if the click popup is not open
      if (!isClickPopupOpen) {
        marker.closePopup();
      }
    });
    
    // Click popup (shows when clicked, stays open)
    marker.on('click', function() {
      marker.unbindPopup();
      marker.bindPopup(clickPopup).openPopup();
      isClickPopupOpen = true;
    });
    
    // Close the persistent popup when clicking outside
    map.on('click', function(e) {
      // Only close if the click wasn't on the marker
      if (!marker.getLatLng().equals(map.mouseEventToLatLng(e.originalEvent)) && isClickPopupOpen) {
        marker.closePopup();
        isClickPopupOpen = false;
      }
    });
  </script>
</body>
</html>