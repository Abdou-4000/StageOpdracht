<template>
  <div class="flex flex-col xl:flex-row relative justify-center items-center xl:item-start w-full">
      <!-- map -->
      <div class="flex clip-path">
        <div ref="map" id="map" class="flex rounded-3xl z-0"></div>
      </div>

      <!-- Filter items -->
      <div class="flex flex-col md:flex-row xl:flex-col justify-center xl:justify-start xl:absolute xl:right-[1px] xl:top-[-30px] w-11/12 md:w-full xl:w-1/3 m-2 xl:m-6">
        <!-- searchbar -->
        <div class="flex bg-red m-1.5 ml-1 xl:ml-6 mr-1 xl:mr-6 rounded-3xl">
          <input 
            type="text" 
            v-model="searchQuery" 
            class="flex bg-red w-full p-2.5 pl-6 pr-6 rounded-3xl text-white placeholder-white" 
            placeholder="Zoeken"
            @keyup.enter="performSearch"
          >
          <button id="" @click="performSearch">
            <i class=""></i>
          </button>
          <div v-if="showSearchResults" class="hidden">
            <div v-for="result in searchResults" 
                 :key="result.id" 
                 class="hidden"
                 @click="selectTeacher(result)">
              <div class="hidden">{{ result.name }}</div>
              <div class="hidden">
                <span class="hidden">{{ getCategoryDisplay(result) }}</span>
                <span v-if="userLocation" class="hidden">({{ getDistance(result) }} km)</span>
              </div>
            </div>
            <div v-if="searchResults.length === 0" class="hidden">
              No results found
            </div>
          </div>
        </div>
        
        <!-- Categories -->
        <div class="flex w-full md:w-1/3 xl:w-full">
          <select v-model="selectedCategory" class="bg-darkred w-full filter-select m-1.5 ml-1 xl:ml-6 mr-1 xl:mr-6 p-2.5 pl-5 pr-5 rounded-3xl" >
            <option value="all">Categorie</option>
            <option v-for="category in categories" 
                    :key="category" 
                    :value="category">
              {{ category }}
            </option>
          </select>
        </div>
        
        <!-- Radius -->
        <div class="flex w-full md:w-1/3 xl:w-full">
        <div class="flex justify-between bg-gray-dark w-full m-1.5 ml-1 xl:ml-6 mr-1 xl:mr-6 p-3 pl-6 pr-6 rounded-3xl">
          <div class="flex items-center text-white">
            Straal
          </div>
          <div class="flex items-center">
            <input type="number" v-model.number="radius" id="radius-filter" min="1" max="50">
            <p class="ml-1 text-white">km</p>
          </div>
        </div>
        </div>
      </div>
      <div class="absolute top-[20px]">
        <TeacherProfile 
          :show="showDetailPopup"
          :teacher="selectedTeacher"
          :distance="selectedTeacherDistance"
          :user="user"
          @close="showDetailPopup = false"
        />
      </div>
  </div>
</template>

<script>
import L from 'leaflet';
import TeacherProfile from './TeacherProfile.vue';

export default {
  name: 'TeacherMap',
  components: {
    TeacherProfile 
  },
  props: {
    user: Object
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
      categories: [],
      showDetailPopup: false,
      selectedTeacher: null,
      selectedTeacherDistance: 'N/A',
      teachers: [],
    }
  },
  async mounted() {
    try {
      const response = await fetch('/map/teachers');
      const data = await response.json();

      this.teachers = data.teachers;

    } catch (error) {
      console.error('Failed to fetch teachers data:', error);
    }
    // Add console log for debugging
    console.log('Component mounted', this.teachers);

    this.initCategories();
    this.$nextTick(() => {
      this.requestLocation(); // when the map is ready
    });
    
  },
  watch: {
    selectedCategory() {
      this.createMarkers();
    },
    radius() {
      this.createMarkers();
    },
    searchQuery() {
      this.createMarkers();
    },
    teachers(newVal) {
    if (newVal.length > 0 && this.$refs.map) {
      this.initMap(); // init when teachers and map are ready
    }
  }
  },
  methods: {
    initMap() {
      this.map = L.map('map', {
        zoomControl: true,
        attributionControl: false,
      }).setView([50.996, 5.538], 1);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 15,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(this.map);

      this.markersLayer = L.layerGroup().addTo(this.map);

      this.map.on('click', this.handleMapClick);
      // this.createMarkers(); // Initial markers creation
      this.$watch('teachers', () => {
        if (this.teachers.length > 0) {
          this.createMarkers();
        }
      });
    },
    initCategories() {
      const allCategories = new Set();
      this.teachers.forEach(teacher => {
        if (Array.isArray(teacher.category)) {
          teacher.category.forEach(cat => allCategories.add(cat.name));
        }
      });
      this.categories = [...allCategories];
    },
    requestLocation() {
      this.map.locate({ setView: false, maxZoom: 15 });
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
      
      this.map.setView(e.latlng, 15);
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
          Array.isArray(teacher.category) && teacher.category.map(cat => cat.name).includes(this.selectedCategory)
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
            Leerkracht: ${teacher.name}<br>
            Categorie: ${this.getCategoryDisplay(teacher)}<br>
            Locatie: ${teacher.details?.location || 'N/A'}<br>
            Email: ${teacher.details?.syntramail || 'N/A'}<br>
            Afstand: ${distance} km<br>
            <button class="more-info-btn">More Information</button>
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

        marker.on('popupopen', (popup) => {
          const moreInfoBtn = document.querySelector('.more-info-btn');
          if (moreInfoBtn) {
            moreInfoBtn.addEventListener('click', () => {
              this.selectedTeacher = teacher;
              this.selectedTeacherDistance = distance;
              this.showDetailPopup = true;
            });
          }
        });
        
        this.markersLayer.addLayer(marker);
        this.markers[teacher.name.toLowerCase()] = marker;
      });
    },
    getCategoryDisplay(teacher) {
      return Array.isArray(teacher.category) 
        ? teacher.category.map(cat => cat.name).join(', ') 
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
      const a 
        = Math.sin(dLat/2) * Math.sin(dLat/2) +
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
.clip-path {
  clip-path: polygon( 0% 7.314%,0% 7.314%,0.041% 6.127%,0.16% 5.002%,0.351% 3.953%,0.606% 2.994%,0.92% 2.142%,1.286% 1.411%,1.698% 0.816%,2.148% 0.373%,2.632% 0.096%,3.141% 0%,63.05% 0%,63.05% 0%,63.559% 0.096%,64.043% 0.373%,64.493% 0.816%,64.905% 1.411%,65.271% 2.142%,65.585% 2.994%,65.84% 3.953%,66.031% 5.002%,66.15% 6.127%,66.191% 7.314%,66.191% 24.003%,66.191% 24.003%,66.232% 25.189%,66.351% 26.314%,66.541% 27.364%,66.797% 28.322%,67.111% 29.174%,67.477% 29.905%,67.888% 30.5%,68.339% 30.944%,68.822% 31.221%,69.332% 31.316%,96.859% 31.316%,96.859% 31.316%,97.368% 31.412%,97.852% 31.689%,98.303% 32.133%,98.714% 32.728%,99.08% 33.459%,99.394% 34.311%,99.649% 35.269%,99.84% 36.319%,99.959% 37.444%,100% 38.63%,100% 50%,100% 92.686%,100% 92.686%,99.959% 93.873%,99.84% 94.998%,99.649% 96.047%,99.394% 97.006%,99.08% 97.858%,98.714% 98.589%,98.303% 99.184%,97.852% 99.627%,97.368% 99.904%,96.859% 100%,3.141% 100%,3.141% 100%,2.632% 99.904%,2.148% 99.627%,1.698% 99.184%,1.286% 98.589%,0.92% 97.858%,0.606% 97.006%,0.351% 96.047%,0.16% 94.998%,0.041% 93.873%,0% 92.686%,0% 7.314% );
  height: 38vw;
  width: 95%;
}
@media (max-width: 1280px) {
  .clip-path {
      clip-path: none;
      width: 95%;
      height: 500px;
  }
}
</style>
