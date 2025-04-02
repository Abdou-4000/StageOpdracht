<template>
  <div>
    <div v-if="calendarOptions">
      <FullCalendar 
      :options="calendarOptions" 
      @dateClick="handleDateClick" 
      />
       <!-- Form to collect event details -->
      <div v-if="isFormVisible" class="event-form">
        <form @submit.prevent="handleSubmit">
          <label for="title">Event Title</label>
          <input v-model="eventTitle" type="text" id="title" required />

          <label for="start-time">Start Time</label>
          <input v-model="startTime" type="time" id="start-time" required />

          <label for="end-time">End Time</label>
          <input v-model="endTime" type="time" id="end-time" required />

          <button type="submit">Save Event</button>
          <button type="button" @click="cancelEvent">Cancel</button>
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
import timeGridPlugin from '@fullcalendar/timegrid';  // TimeGrid plugin (optional, but good for week/day views)
import listPlugin from '@fullcalendar/list';  // List plugin (optional, for list view)

// Define your events (empty for now)
const events = ref([]);
const isReady = ref(false);
const isFormVisible = ref(false); // To show/hide the form
const eventTitle = ref(''); // For user-entered title
const startTime = ref(''); // For user-entered start time
const endTime = ref(''); // For user-entered end time
const currentDate = ref(null); // The date that the user clicked
const currentDay = ref('');

// Calendar options including initial view setup
const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],  // Add required plugins
  initialView: 'timeGridWeek',  // Default view for the calendar
  locale: 'nl',  // Locale for the calendar
  dayHeaderFormat: { weekday: 'long' },
  firstDay: 1,
  allDaySlot: false,
  headerToolbar: false,
  events: events.value,
  dateClick: handleDateClick,
});

// Handle clicking on a date
function handleDateClick(info) {
    const selectedDate = new Date(info.dateStr); 
    const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    currentDay.value = daysOfWeek[selectedDate.getDay()]; // Store only the weekday name

    isFormVisible.value = true; // Show the input form
}

// Submit the form and add the event
function handleSubmit() {
    // Get start and end times
    const [startHour, startMinute] = startTime.value.split(':');
    const [endHour, endMinute] = endTime.value.split(':');

    // Convert weekday name to FullCalendar format (Sunday = 0, Monday = 1, ...)
    const daysMap = { "Sunday": 0, "Monday": 1, "Tuesday": 2, "Wednesday": 3, "Thursday": 4, "Friday": 5, "Saturday": 6 };
    const dayNumber = daysMap[currentDay.value];

    // Add event with recurring rule
    events.value.push({
        title: eventTitle.value,
        startTime: `${startHour}:${startMinute}:00`, // FullCalendar expects HH:mm:ss
        endTime: `${endHour}:${endMinute}:00`,
        daysOfWeek: [dayNumber], // Make the event repeat every week on this day
    });

    // Hide the form
    isFormVisible.value = false;
    eventTitle.value = '';
    startTime.value = '';
    endTime.value = '';
}


// Cancel the event
function cancelEvent() {
    isFormVisible.value = false;
    eventTitle.value = '';
    startTime.value = '';
    endTime.value = '';
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
