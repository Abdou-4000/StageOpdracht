<template>
  <div v-if="show" class="profile-overlay">
    <div class="card-container">
      <div class="main-card">
        <button class="close-button" @click="$emit('close')">×</button>
          <div class="main-info-item">

            {{ teacher.compname || 'N/A' }}
          </div>
          <div class="main-info-item-2">
            {{ teacher.name || 'N/A' }}
          </div>
          <div class="main-info-item-3">
            <span class="detail-label">Address:</span>
            {{ teacher.details?.location || 'N/A' }}
          </div>
          <div class="main-info-item-4">
            <span class="detail-label">Distance:</span>
            {{ distance }} km
          </div>
        </div>
      <div class="info-box">
        <div class="info-item">{{ teacher.details?.email || 'Email' }}</div>
        <div class="info-item">{{ teacher.details?.phone || 'Phone' }}</div>
        <div class="info-item">{{ teacher.details?.location || 'Location' }}</div>
      </div>
      <div class="logo-box">
        <img src="../../../public/assets/Logo.png" alt="Syntra Logo" class="logo-image">      </div>
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

<style>
@import '../../css/teacher-profile.css';

.profile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-container {
  position: relative;
}

.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}
</style>
