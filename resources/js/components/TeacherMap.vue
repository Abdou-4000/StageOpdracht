<template>
  <div class="map-wrapper">
    <div class="header">
      <img src="/assets/Logo.png" alt="Logo">
    </div>
    <div class="map-container">
      <div id="map"></div>
      <div class="controls">
        <div class="search-container">
          <input 
            type="text" 
            v-model="searchQuery" 
            class="search-input" 
            id="search-input"
            placeholder="Teacher"
            @keyup.enter="performSearch"
          >
          <button id="search-button" @click="performSearch">
            <i class="fas fa-search"></i>
          </button>
          <div id="search-results" v-if="showSearchResults" class="search-dropdown">
            <div v-for="result in searchResults" 
                 :key="result.id" 
                 class="search-result-item"
                 @click="selectTeacher(result)">
              <div class="result-name">{{ result.name }}</div>
              <div class="result-details">
                <span class="category">{{ getCategoryDisplay(result) }}</span>
                <span v-if="userLocation" class="distance">({{ getDistance(result) }} km)</span>
              </div>
            </div>
            <div v-if="searchResults.length === 0" class="no-results">
              No results found
            </div>
          </div>
        </div>
        
        <select v-model="selectedCategory" id="category-filter" class="filter-select">
          <option value="all">Categorie</option>
          <option v-for="category in categories" 
                  :key="category" 
                  :value="category">
            {{ category }}
          </option>
        </select>
        
        <div class="radius-container">
          <span class="radius-label">Straal</span>
          <input type="number" 
                 v-model.number="radius" 
                 id="radius-filter"
                 min="1" 
                 max="50">
          <span>Km</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';

export default {
  name: 'TeacherMap',
  props: {
    teachers: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      map: null,
      markersLayer: null,
      markers: {},
      userLocation: null,
      searchQuery: '',
      selectedCategory: 'all',
      radius: 15,
      showSearchResults: false,
      searchResults: [],
      categories: []
    }
  },
  mounted() {
    // Ensure map is initialized after DOM is ready
    this.$nextTick(() => {
      setTimeout(() => {
        this.initMap();
        this.initCategories();
        this.requestLocation();
      }, 100);
    });
  },
  watch: {
    selectedCategory() {
      this.createMarkers();
    },
    radius() {
      this.createMarkers();
    }
  },
  methods: {
    initMap() {
      this.map = L.map('map', {
        zoomControl: true,
        attributionControl: true
      }).setView([50.996, 5.538], 16.5);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(this.map);

      this.markersLayer = L.layerGroup().addTo(this.map);

      this.map.on('click', this.handleMapClick);
      this.createMarkers(); // Initial markers creation
    },
    initCategories() {
      const allCategories = new Set();
      this.teachers.forEach(teacher => {
        if (Array.isArray(teacher.category)) {
          teacher.category.forEach(cat => allCategories.add(cat));
        }
      });
      this.categories = [...allCategories];
    },
    requestLocation() {
      this.map.locate({ setView: false, maxZoom: 17 });
      this.map.on('locationfound', this.onLocationFound);
      this.map.on('locationerror', this.onLocationError);
    },
    handleMapClick(e) {
      // Close all popups when clicking outside markers
      if (!e.originalEvent.target.closest('.leaflet-marker-icon')) {
        this.map.closePopup();
      }
    },
    onLocationFound(e) {
      const radius = e.accuracy / 2;
      this.userLocation = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
      };
      
      // Add marker at user's location
      L.marker(e.latlng).addTo(this.map)
        .bindPopup("You are within " + radius + " meters from this point").openPopup();
      
      this.map.setView(e.latlng, 17);
      this.createMarkers();
    },
    onLocationError(e) {
      alert(e.message);
      this.map.setView([50.996, 5.538], 17);
      this.createMarkers();
    },
    createMarkers() {
      // Clear existing markers
      this.markersLayer.clearLayers();
      
      // Get filter values
      const maxRadius = this.radius;
      const searchQuery = this.searchQuery.toLowerCase();
      
      // Filter teachers
      let filteredTeachers = this.teachers;
      
      // Apply category filter
      if (this.selectedCategory !== 'all') {
        filteredTeachers = filteredTeachers.filter(teacher => 
          Array.isArray(teacher.category) && teacher.category.includes(this.selectedCategory)
        );
      }
      
      // Apply search filter
      if (searchQuery) {
        filteredTeachers = filteredTeachers.filter(teacher => 
          teacher.name.toLowerCase().includes(searchQuery)
        );
      }
      
      // Apply radius filter if user location exists
      if (this.userLocation) {
        filteredTeachers = filteredTeachers.filter(teacher => {
          const distance = this.calculateDistance(
            this.userLocation.lat, 
            this.userLocation.lng, 
            teacher.lat, 
            teacher.lng
          );
          return distance <= maxRadius;
        });
      }

      // Create marker icon
      const teacherIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
        iconSize: [45, 45],
        popupAnchor: [0, -25]
      });
      
      filteredTeachers.forEach(teacher => {
        const marker = L.marker([teacher.lat, teacher.lng], {
          icon: teacherIcon
        });
        
        // Calculate distance first so it's available for both popups
        const distance = this.userLocation 
          ? this.calculateDistance(
              this.userLocation.lat, 
              this.userLocation.lng, 
              teacher.lat, 
              teacher.lng
            ).toFixed(2)
          : 'N/A';
        
        // Create hover popup content
        const hoverPopupContent = `
          <div class="hover-popup">
            <strong>${teacher.compname}</strong>
          </div>
        `;
        
        // Create click popup content using the calculated distance
        const clickPopupContent = `
          <div class="click-popup">
            <strong>${teacher.compname}</strong><br>
            Teacher: ${teacher.name}<br>
            Category: ${this.getCategoryDisplay(teacher)}<br>
            Location: ${teacher.details?.location || 'N/A'}<br>
            Email: ${teacher.details?.email || 'N/A'}<br>
            Phone: ${teacher.details?.phone || 'N/A'}<br>
            Distance: ${distance} km
          </div>
        `;
        
        // Add hover events
        marker.on('mouseover', function(e) {
          if (!marker.isPopupOpen()) {
            L.popup({
              offset: L.point(0, -25),
              className: 'hover-popup',
              closeButton: false,
              autoClose: false
            })
            .setLatLng(marker.getLatLng())
            .setContent(hoverPopupContent)
            .openOn(this.map);
          }
        }.bind(this));
        
        marker.on('mouseout', function(e) {
          if (!marker.isPopupOpen()) {
            this.map.closePopup();
          }
        }.bind(this));
        
        // Bind click popup
        marker.bindPopup(clickPopupContent);
        
        this.markersLayer.addLayer(marker);
        this.markers[teacher.name.toLowerCase()] = marker;
      });
    },
    getCategoryDisplay(teacher) {
      return Array.isArray(teacher.category) 
        ? teacher.category.join(', ') 
        : 'Uncategorized';
    },
    getDistance(teacher) {
      if (!this.userLocation) return 'N/A';
      return this.calculateDistance(
        this.userLocation.lat,
        this.userLocation.lng,
        teacher.lat,
        teacher.lng
      ).toFixed(2);
    },
    performSearch() {
      this.searchResults = this.teachers.filter(teacher =>
        teacher.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
      this.showSearchResults = true;
      this.createMarkers();
    },
    selectTeacher(teacher) {
      this.map.setView([teacher.lat, teacher.lng], 15);
      const marker = this.markers[teacher.name.toLowerCase()];
      if (marker) {
        marker.openPopup();
      }
      this.showSearchResults = false;
    },
    calculateDistance(lat1, lon1, lat2, lon2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }
  }
}
</script>

<style>
@import '../../css/teacher-map.css';

.search-dropdown {
  position: absolute;
  top: 65px;
  left: 0;
  width: 100%;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-height: 300px;
  overflow-y: auto;
  z-index: 1001;
}

.search-result-item {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

.search-result-item:hover {
  background-color: #f8f9fa;
}

.result-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 4px;
}

.result-details {
  font-size: 0.9em;
  color: #666;
}

.category {
  color: #8b0000;
}

.distance {
  margin-left: 8px;
  color: #666;
}

.no-results {
  padding: 12px 16px;
  color: #666;
  text-align: center;
}
</style>
