<template>
  <div class="map-wrapper">


    <div class="map-container">
      <div id="map"></div>
    </div>
      <div class="controls">
        <div class="predictive-input-container">
          <!-- Suggestion layer (gray text, positioned behind) -->
          <div class="suggestion-layer" ref="suggestionLayer">{{ searchQuery }}{{ inlineSuggestion }}</div>
          
          <!-- Actual input layer (what user types, positioned in front) -->
          <input 
            type="text" 
            v-model="searchQuery" 
            class="search-input" 
            id="search-input"
            ref="searchInput"
            placeholder="Teacher"
            @input="onInputChange"
            @focus="handleFocus"
            @keydown="handleKeyDown"
            @keyup.enter="performSearch"
          >
          <button id="search-button" @click="performSearch">
            <i class="fas fa-search"></i>
          </button>
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
    <TeacherProfile 
      :show="showDetailPopup"
      :teacher="selectedTeacher"
      :distance="selectedTeacherDistance"
      @close="showDetailPopup = false"
    />
</template>

<script>
import L from 'leaflet';
import TeacherProfile from './TeacherProfile.vue';
import algoliasearch from 'algoliasearch/lite';

export default {
  name: 'TeacherMap',
  components: {
    TeacherProfile 
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
      // Algolia client
      searchClient: null,
      searchIndex: null,
      inlineSuggestion: '',
      autocompleteResults: [],
      popularSearches: [],
      // Debounce search
      searchDebounce: null
    }
  },
  async mounted() {
    // Initialize Algolia client
    this.searchClient = algoliasearch(
      'L7ZA4PQTY7',
      'd5e073ed7c6a62711de8754011afcfcc' // This should be your search-only API key
    );
    this.searchIndex = this.searchClient.initIndex('teachers');
    this.loadPopularSearches();

    try {
      // Fetch categories from Algolia facets
      const { facets } = await this.searchIndex.search('', {
        facets: ['category']
      });
      
      if (facets && facets.category) {
        this.categories = Object.keys(facets.category);
      } else {
        // Fallback to fetch from API if Algolia facets not available
        const response = await fetch('/map/teachers');
        const data = await response.json();
        this.teachers = data.teachers;
        this.initCategories();
      }
    } catch (error) {
      console.error('Failed to fetch data:', error);
      
      // Fallback to original method if Algolia fails
      const response = await fetch('/map/teachers');
      const data = await response.json();
      this.teachers = data.teachers;
    }
    
    // Initialize map
    const initializeMap = () => {
      if (document.getElementById('map')) {
        this.initMap();
        this.requestLocation();
      } else {
        setTimeout(initializeMap, 100);
      }
    };

    this.$nextTick(initializeMap);
  },
  watch: {
    selectedCategory() {
      this.performSearch();
    },
    radius() {
      this.performSearch();
    },
    searchQuery(newVal) {
      // Debounce search as user types
      clearTimeout(this.searchDebounce);
      this.searchDebounce = setTimeout(() => {
        this.performSearch();
      }, 300);
    }
  },
  methods: {
    // Keep existing map initialization methods
    initMap() {
      // Same as original implementation
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
    },
    
    // Initialize categories from local data if needed
    initCategories() {
      const allCategories = new Set();
      this.teachers.forEach(teacher => {
        if (Array.isArray(teacher.category)) {
          teacher.category.forEach(cat => allCategories.add(cat));
        }
      });
      this.categories = [...allCategories];
    },

    
    
    // Generate autocomplete suggestions based on query
      async generateAutocompleteSuggestions() {
    try {
      if (!this.searchQuery.trim()) {
        this.autocompleteResults = this.popularSearches.slice(0, 5);
        return;
      }
      
      // Use Algolia for autocomplete with enhanced typo tolerance
      const { hits } = await this.searchIndex.search(this.searchQuery, {
        hitsPerPage: 5,
        typoTolerance: true,
        minWordSizefor1Typo: 3,
        minWordSizefor2Typos: 6,
        exactOnSingleWordQuery: 'none'
      });
      
      // Rest of the method remains the same
      const suggestions = hits.map(hit => hit.name || `${hit.firstname} ${hit.lastname}`);
      
      const categoryMatches = this.categories.filter(category => 
        category.toLowerCase().includes(this.searchQuery.toLowerCase())
      ).slice(0, 2);
      
      this.autocompleteResults = [...new Set([...suggestions, ...categoryMatches])].slice(0, 5);
    } catch (error) {
      console.error('Error generating autocomplete suggestions:', error);
      this.generateLocalAutocompleteSuggestions();
    }
  },

  // Add this new method
    handleFocus() {
      // Only show results and run search if there's a query
      if (this.searchQuery.trim()) {
        this.showSearchResults = true;
        this.onInputChange();
      }
    },

    generateLocalAutocompleteSuggestions() {
      // Your existing code for local filtering
      const suggestions = [];
      
      // Add category-based suggestions
      this.categories.forEach(category => {
        if (category.toLowerCase().includes(this.searchQuery.toLowerCase())) {
          suggestions.push(category);
        }
      });
      
      // Add suggestions from popular searches
      this.popularSearches.forEach(term => {
        if (term.toLowerCase().includes(this.searchQuery.toLowerCase())) {
          suggestions.push(term);
        }
      });
      
      // Add direct matches from teacher names (limit to 5)
      const teacherNameSuggestions = this.teachers
        .filter(teacher => {
          const fullName = `${teacher.firstname} ${teacher.lastname}`.toLowerCase();
          return fullName.includes(this.searchQuery.toLowerCase());
        })
        .slice(0, 5)
        .map(teacher => `${teacher.firstname} ${teacher.lastname}`);
      
      this.autocompleteResults = [...new Set([...suggestions, ...teacherNameSuggestions])].slice(0, 5);
    },
    
    // Select an autocomplete suggestion
    selectSuggestion(suggestion) {
      this.searchQuery = suggestion;
      this.performSearch();
      this.showSearchResults = true;
      
      // Save to popular searches
      this.addToPopularSearches(suggestion);
    },
    
    // Load popular searches from localStorage
    loadPopularSearches() {
      const saved = localStorage.getItem('popularSearches');
      if (saved) {
        try {
          this.popularSearches = JSON.parse(saved);
        } catch (e) {
          this.popularSearches = [];
        }
      }
    },
    
    // Add term to popular searches
    addToPopularSearches(term) {
      // Don't add empty terms
      if (!term.trim()) return;
      
      // Remove if exists already (to reorder)
      const index = this.popularSearches.indexOf(term);
      if (index > -1) {
        this.popularSearches.splice(index, 1);
      }
      
      // Add to front of array
      this.popularSearches.unshift(term);
      
      // Keep only top 20
      this.popularSearches = this.popularSearches.slice(0, 20);
      
      // Save to localStorage
      localStorage.setItem('popularSearches', JSON.stringify(this.popularSearches));
    },

    // Keep other map-related methods the same
    requestLocation() {
      // Same as original
      this.map.locate({ setView: false, maxZoom: 15 });
      this.map.on('locationfound', this.onLocationFound);
      this.map.on('locationerror', this.onLocationError);
    },
    
    handleMapClick(e) {
      // Same as original
      if (!e.originalEvent.target.closest('.leaflet-marker-icon')) {
        this.map.closePopup();
        this.showSearchResults = false;
      }
    },
    
    onLocationFound(e) {
      // Same as original with search call
      const radius = e.accuracy / 2;
      this.userLocation = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
      };
      
      L.marker(e.latlng).addTo(this.map)
        .bindPopup("You are within " + radius + " meters from this point").openPopup();
      
      this.map.setView(e.latlng, 15);
      
      // Use Algolia search when location is found
      this.performSearch();
    },
    
    onLocationError(e) {
      // Same as original with search call
      alert(e.message);
      this.map.setView([50.996, 5.538], 17);
      this.performSearch();
    },
    /*
    // New method: Perform Algolia search
    async performSearch() {
      try {
        if (this.searchQuery.trim()) {
          this.addToPopularSearches(this.searchQuery);
        }

        // Build filters
        const filters = [];
        
        // Add category filter
        if (this.selectedCategory !== 'all') {
          filters.push(`category:"${this.selectedCategory}"`);
        }
        
        // Perform Algolia search
        const { hits } = await this.searchIndex.search(this.searchQuery, {
          filters: filters.join(' AND '),
          distinct: true,
          hitsPerPage: 50
        });
        
        // Apply radius filter on the client side (since Algolia geo filtering is a paid feature)
        let filteredResults = hits;
        if (this.userLocation && this.radius) {
          filteredResults = hits.filter(teacher => {
            if (!teacher.lat || !teacher.lng) return false;
            
            const distance = this.calculateDistance(
              this.userLocation.lat, 
              this.userLocation.lng, 
              teacher.lat, 
              teacher.lng
            );
            return distance <= this.radius;
          });
        }
        
        // Update search results and display on map
        this.searchResults = filteredResults;
        this.showSearchResults = this.searchQuery.trim() !== '';
        this.createMarkersFromResults(filteredResults);
        
      } catch (error) {
        console.error('Algolia search error:', error);
        
        // Fallback to local filtering if Algolia fails
        this.filterLocally();
      }
    },*/

    onInputChange(event) {
  // Get the input element
  const input = this.$refs.searchInput;
  
  // Clear the previous suggestion
  this.inlineSuggestion = '';
  
  // Don't search for empty queries
  if (!this.searchQuery.trim()) {
    this.showSearchResults = false;
    return;
  }
  
  // Debounce the suggestion lookup
  clearTimeout(this.searchDebounce);
  this.searchDebounce = setTimeout(() => {
    // Call Algolia to get a suggestion
    this.searchIndex.search(this.searchQuery, {
      hitsPerPage: 1,
      typoTolerance: true,
    }).then(({ hits }) => {
      if (hits.length > 0) {
        const bestMatch = hits[0].name || `${hits[0].firstname} ${hits[0].lastname}` || '';
        
        if (bestMatch && bestMatch.toLowerCase().startsWith(this.searchQuery.toLowerCase())) {
          // Use browser's built-in autocomplete
          input.value = this.searchQuery;
          input.setSelectionRange(this.searchQuery.length, bestMatch.length);
          
          // Store suggestion for Tab completion
          this.inlineSuggestion = bestMatch.substring(this.searchQuery.length);
        }
      }
    });
    
    // Also run the search
    this.combinedSearch();
  }, 300);
},


    // Replace separate predictText() and performSearch() calls
      async combinedSearch() {
        try {
          // Run both searches in a single network request
          const results = await this.searchClient.multipleQueries([
            {
              // Search for inline prediction
              indexName: 'teachers',
              query: this.searchQuery,
              params: {
                hitsPerPage: 1,
                typoTolerance: true,
                minWordSizefor1Typo: 3,
                minWordSizefor2Typos: 6
              }
            },
            {
              // Main search for results
              indexName: 'teachers',
              query: this.searchQuery,
              params: {
                filters: this.selectedCategory !== 'all' ? `category:"${this.selectedCategory}"` : '',
                distinct: true,
                hitsPerPage: 50
              }
            }
          ]);

          // Handle prediction result
          const predictionHit = results.results[0].hits[0];
          if (predictionHit) {
            const bestMatch = predictionHit.name || 
                            `${predictionHit.firstname} ${predictionHit.lastname}` || 
                            '';
            
            if (bestMatch && bestMatch.toLowerCase() !== this.searchQuery.toLowerCase()) {
              this.inlineSuggestion = bestMatch.substring(this.searchQuery.length);
            } else {
              this.inlineSuggestion = '';
            }
          }

          // Handle main search results
          let filteredResults = results.results[1].hits;
          
          // Apply radius filter (still needs to be client-side in free tier)
          if (this.userLocation && this.radius) {
            filteredResults = filteredResults.filter(teacher => {
              if (!teacher.lat || !teacher.lng) return false;
              
              const distance = this.calculateDistance(
                this.userLocation.lat, 
                this.userLocation.lng, 
                teacher.lat, 
                teacher.lng
              );
              return distance <= this.radius;
            });
          }
          
          // Update results and map
          this.searchResults = filteredResults;
          this.createMarkersFromResults(filteredResults);
          
        } catch (error) {
          console.error('Search error:', error);
          this.filterLocally();
        }
      },
    

    

    // Handle keyboard navigation for prediction
    handleKeyDown(event) {
  // Tab or Right Arrow to accept suggestion
  if ((event.key === 'Tab' || event.key === 'ArrowRight') && this.inlineSuggestion) {
    event.preventDefault(); // Prevent default tab behavior
    
    // Replace with the full suggestion instead of just appending
    this.searchQuery = this.fullSuggestion || (this.searchQuery + this.inlineSuggestion);
    this.inlineSuggestion = '';
    
    // Place cursor at end and perform search
    this.$nextTick(() => {
      const input = this.$refs.searchInput;
      input.selectionStart = input.selectionEnd = input.value.length;
      this.combinedSearch();
    });
  } else if (event.key === 'Escape') {
    // Clear suggestion on escape
    this.inlineSuggestion = '';
    this.fullSuggestion = '';
    this.showSearchResults = false;
  }
},
    
    // Fallback method using local filtering
    filterLocally() {
      // Similar to original createMarkers but updates searchResults
      let filteredTeachers = this.teachers;
      
      if (this.selectedCategory !== 'all') {
        filteredTeachers = filteredTeachers.filter(teacher => 
          Array.isArray(teacher.category) && teacher.category.includes(this.selectedCategory)
        );
      }
      
      if (this.searchQuery) {
        filteredTeachers = filteredTeachers.filter(teacher => 
          teacher.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }
      
      if (this.userLocation) {
        filteredTeachers = filteredTeachers.filter(teacher => {
          const distance = this.calculateDistance(
            this.userLocation.lat, 
            this.userLocation.lng, 
            teacher.lat, 
            teacher.lng
          );
          return distance <= this.radius;
        });
      }
      
      this.searchResults = filteredTeachers;
      this.showSearchResults = this.searchQuery.trim() !== '';
      this.createMarkersFromResults(filteredTeachers);
    },
    
    // Updated to accept search results parameter
    createMarkersFromResults(results) {
      // Clear existing markers
      this.markersLayer.clearLayers();
      this.markers = {};
      
      // Create marker icon
      const teacherIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
        iconSize: [45, 45],
        popupAnchor: [0, -25]
      });
      
      results.forEach(teacher => {
        if (!teacher.lat || !teacher.lng) return;
        
        const marker = L.marker([teacher.lat, teacher.lng], {
          icon: teacherIcon
        });
        
        // Calculate distance
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
            <strong>${teacher.compname || teacher.firstname + ' ' + teacher.lastname}</strong>
          </div>
        `;
        
        // Create click popup content
        const clickPopupContent = `
          <div class="click-popup">
            <strong>${teacher.compname || teacher.firstname + ' ' + teacher.lastname}</strong><br>
            Teacher: ${teacher.name || teacher.firstname + ' ' + teacher.lastname}<br>
            Category: ${this.getCategoryDisplay(teacher)}<br>
            Location: ${teacher.street || ''} ${teacher.streetnumber || ''}<br>
            Email: ${teacher.email || 'N/A'}<br>
            Phone: ${teacher.phone || 'N/A'}<br>
            Distance: ${distance} km<br>
            <button class="more-info-btn">More Information</button>
          </div>
        `;
        
        // Hover events
        marker.on('mouseover', () => {
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
        });
        
        marker.on('mouseout', () => {
          if (!marker.isPopupOpen()) {
            this.map.closePopup();
          }
        });
        
        // Bind click popup
        marker.bindPopup(clickPopupContent);

        marker.on('popupopen', () => {
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
        // Store marker using objectID instead of name for Algolia results
        const key = teacher.objectID || teacher.name.toLowerCase();
        this.markers[key] = marker;
      });
    },
    
    // Keep helper methods the same
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
    
    // Update select teacher to work with Algolia results
    selectTeacher(teacher) {
      if (!teacher.lat || !teacher.lng) return;
      
      this.map.setView([teacher.lat, teacher.lng], 15);
      const key = teacher.objectID || teacher.name.toLowerCase();
      const marker = this.markers[key];
      if (marker) {
        marker.openPopup();
      }
      this.showSearchResults = false;
    },
    
    // Keep distance calculation method
    calculateDistance(lat1, lon1, lat2, lon2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
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

</style>
