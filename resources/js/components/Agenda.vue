<template>
  <div>
    <div v-if="calendarOptions" style="border: 2px solid red; height: 500px;">
      <FullCalendar :options="calendarOptions" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3';  // FullCalendar for Vue 3
import dayGridPlugin from '@fullcalendar/daygrid';  // DayGrid plugin
import interactionPlugin from '@fullcalendar/interaction';  // Interaction plugin (needed for event handling)
import timeGridPlugin from '@fullcalendar/timegrid';  // TimeGrid plugin (optional, but good for week/day views)
import listPlugin from '@fullcalendar/list';  // List plugin (optional, for list view)

// Define your events (empty for now)
const events = ref([]);
const isReady = ref(false);

// Calendar options including initial view setup
const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],  // Add required plugins
  initialView: 'dayGridMonth',  // Default view for the calendar
  locale: 'en',  // Locale for the calendar
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,dayGridWeek,timeGridWeek,timeGridDay,listWeek',  // Added more view options
  },
});

// Log the options to ensure they're being set correctly
onMounted(() => {
  console.log("Calendar Options:", calendarOptions.value);
});
</script>

<style scoped>
/* Optional: Add custom styles for FullCalendar */
</style>
