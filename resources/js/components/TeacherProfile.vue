<template>
    <div v-if="show" class="teacher-profile-overlay">
      <div class="teacher-profile-popup">
        <button class="close-button" @click="$emit('close')">×</button>
        <div class="teacher-profile-content">
          <h2>{{ teacher.compname }}</h2>
          <div class="teacher-info">
            <div class="info-section">
              <h3>Teacher Information</h3>
              <p><strong>Name:</strong> {{ teacher.name }}</p>
              <p><strong>Categories:</strong> {{ getCategoryDisplay }}</p>
              <p><strong>Location:</strong> {{ teacher.details?.location || 'N/A' }}</p>
            </div>
            <div class="info-section">
              <h3>Contact Details</h3>
              <p><strong>Email:</strong> {{ teacher.details?.email || 'N/A' }}</p>
              <p><strong>Phone:</strong> {{ teacher.details?.phone || 'N/A' }}</p>
              <p><strong>Distance:</strong> {{ distance }} km</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'TeacherProfile',
    props: {
      show: Boolean,
      teacher: Object,
      distance: [Number, String]
    },
    computed: {
      getCategoryDisplay() {
        return Array.isArray(this.teacher.category) 
          ? this.teacher.category.join(', ') 
          : 'Uncategorized';
      }
    }
  }
  </script>
  
  <style scoped>
  .teacher-profile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
  }
  
  .teacher-profile-popup {
    background: white;
    width: 90%;
    max-width: 600px;
    border-radius: 15px;
    padding: 20px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 80vh;
    overflow-y: auto;
  }
  
  .close-button {
    position: absolute;
    right: 15px;
    top: 10px;
    border: none;
    background: none;
    font-size: 24px;
    cursor: pointer;
    color: #333;
  }
  
  .teacher-profile-content {
    margin-top: 20px;
  }
  
  h2 {
    color: #8b0000;
    margin-bottom: 20px;
  }
  
  .teacher-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
  
  .info-section {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 10px;
  }
  
  h3 {
    color: #333;
    margin-bottom: 15px;
  }
  
  p {
    margin: 10px 0;
  }
  
  @media (max-width: 768px) {
    .teacher-info {
      grid-template-columns: 1fr;
    }
  }
  </style>
