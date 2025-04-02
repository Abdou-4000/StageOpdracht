<template>
  <div>
    <div v-if="calendarOptions">
      <FullCalendar 
        :options="calendarOptions" 
      />
      <!-- Form to collect event details -->
      <div class="event-form">
        <form @submit.prevent="handleSubmit">
          <label for="start-time">Start Time</label>
          <input v-model="startTime" type="time" id="start-time" required />

          <label for="end-time">End Time</label>
          <input v-model="endTime" type="time" id="end-time" required />

          <label>Days of the Week</label>
          <div>
            <!-- Generate checkboxes dynamically from the weekdays array -->
            <div v-for="(day, index) in weekdays" :key="index">
              <input 
                type="checkbox" 
                :id="day.shortCode" 
                :value="day.shortCode"
                v-model="selectedDays" 
              />
              <label :for="day.shortCode">{{ day.name }}</label>
            </div>
          </div>

          <button type="submit">Add</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3';  // FullCalendar for Vue 3
import dayGridPlugin from '@fullcalendar/daygrid';  // DayGrid plugin
import interactionPlugin from '@fullcalendar/interaction';  // Interaction plugin (needed for event handling)
import timeGridPlugin from '@fullcalendar/timegrid';  // TimeGrid plugin
import listPlugin from '@fullcalendar/list';  // List plugin
import rrulePlugin from '@fullcalendar/rrule';

// Define your events (empty for now)
const events = ref([]);
const startTime = ref(''); // For user-entered start time
const endTime = ref(''); // For user-entered end time

// Array of weekdays with short codes
const weekdays = ref([
  { name: 'Monday', shortCode: 'MO' },
  { name: 'Tuesday', shortCode: 'TU' },
  { name: 'Wednesday', shortCode: 'WE' },
  { name: 'Thursday', shortCode: 'TH' },
  { name: 'Friday', shortCode: 'FR' },
  { name: 'Saturday', shortCode: 'SA' },
  { name: 'Sunday', shortCode: 'SU' }
]);

// Selected days array
const selectedDays = ref([]);  // Array to store selected days of the week

// Calendar options including initial view setup
const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],  // Add required plugins
  initialView: 'timeGridWeek',  // Default view for the calendar
  locale: 'nl',  // Locale for the calendar
  dayHeaderFormat: { weekday: 'long' },
  firstDay: 1,
  allDaySlot: false,
  headerToolbar: false,
  events: events.value,
});

// Submit the form and add the event with rrule
function handleSubmit() {
  // Get start and end times
  const [startHour, startMinute] = startTime.value.split(':');
  const [endHour, endMinute] = endTime.value.split(':');

  // Check if at least one day is selected
  if (selectedDays.value.length === 0) {
    alert('Please select at least one day.');
    return;
  }

  // Create rrule for weekly recurrence based on selected days
  const rrule = `FREQ=WEEKLY;BYDAY=${selectedDays.value.join(',')}`;

  // Add event with rrule
  events.value.push({
    title: 'Event',  // Customize event title
    start: `${startHour}:${startMinute}:00`,  // FullCalendar expects HH:mm:ss
    end: `${endHour}:${endMinute}:00`,
    rrule: rrule,  // Add the rrule to the event
  });

  // Empty the form
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = []; // Reset selected days

  console.log(events)
}

// Log the options to ensure they're being set correctly
onMounted(() => {
  console.log("Calendar Options:", calendarOptions.value);
  document.querySelectorAll('.fc-day-today').forEach(el => {
    el.style.backgroundColor = 'transparent';
  });
});
</script>

<style scoped>
/* Optional: Add custom styles for FullCalendar */
</style>
