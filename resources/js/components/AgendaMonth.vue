<template>
    <div v-if="calendarOptions">
        <FullCalendar 
        ref="calendarRef"
        :options="calendarOptions" 
        />
    </div>
</template>

<script setup>
import { ref, onMounted, toRaw, watch } from 'vue';
import FullCalendar from '@fullcalendar/vue3'; 
import dayGridPlugin from '@fullcalendar/daygrid';  
import interactionPlugin from '@fullcalendar/interaction';  
import timeGridPlugin from '@fullcalendar/timegrid'; 
import listPlugin from '@fullcalendar/list'; 
import rrulePlugin from '@fullcalendar/rrule';
import axios from 'axios';

const calendarRef = ref(null);
const events = ref([]);

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],
  initialView: 'dayGridMonth',
  locale: 'nl',
  timeZone: 'local',
  firstDay: 1,
  allDaySlot: false,
  headerToolbar: {
    left: 'next',
    center: 'title',
    right: 'today'
  },
  events: function(info, successCallback) {
    successCallback(events.value);
  },
});

// Watch for changes to the events array and update the calendar
watch(events, () => {
  if (calendarRef.value) {
    const calendar = calendarRef.value.getApi();
    calendar.refetchEvents();
  }
}, { deep: true });

// Fetches the availability data
async function getEvents () {
    try {
        const response = await fetch('/availabilities');
        const data = await response.json();

        const availabilities = data.availabilities;

        events.value = availabilities;
        
        console.log(events);
    } catch (error) {
        console.error('Error fetching events:', error);
    }
}

// Log the options to ensure they're being set correctly
onMounted(() => {
  console.log("Calendar Options:", calendarOptions.value);
  getEvents();
  console.log(events);
});
</script>

<style>
/* Optional: Add custom styles for FullCalendar */
</style>