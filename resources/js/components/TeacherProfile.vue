<template>
  <div v-if="show" class="profile-overlay" @click="closeProfile">
    <div class="card-container">
      <div class="main-card flex flex-row" @click.stop>
        <div class="flex">
          <AgendaList :id="teacher.id"/>
        </div>
        <div class="flex flex-col">

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
              <span class="detail-label">City:</span>
              {{ teacher.city || 'N/A' }}
            </div>
          </div>
        </div>
      <div class="info-box" @click.stop>
        <div class="info-item">{{ teacher.details?.email || 'Email' }}</div>
        <div class="info-item">{{ teacher.details?.phone || 'Phone' }}</div>
        <div class="info-item">{{ this.getCategoryDisplay || 'Category' }} </div>
      </div>
      <div class="logo-box">

        <img src="../../../public/assets/Logo.png" alt="Syntra Logo" class="logo-image">
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
  },
  methods: {
    closeProfile() {
      this.$emit('close');
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
</style>
