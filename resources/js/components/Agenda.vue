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
  { name: 'Maandag', shortCode: 'MO' },
  { name: 'Disndag', shortCode: 'TU' },
  { name: 'Woensdag', shortCode: 'WE' },
  { name: 'Donderdag', shortCode: 'TH' },
  { name: 'Vrijdag', shortCode: 'FR' },
  { name: 'Zaterdag', shortCode: 'SA' },
  { name: 'Zondag', shortCode: 'SU' }
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

function handleSubmit() {
  // Get start and end times
  const [startHour, startMinute] = startTime.value.split(':');
  const [endHour, endMinute] = endTime.value.split(':');

  // Check if at least one day is selected
  if (selectedDays.value.length === 0) {
    alert('Please select at least one day.');
    return;
  }

  // Loop through each selected day and calculate the corresponding event date
  selectedDays.value.forEach(dayCode => {
    const eventDate = calculateDayOfWeek(dayCode); // Calculate the date for this day
    
    if (eventDate) {
      // Set the start and end times using the selected hour and minute
      const eventStart = new Date(eventDate);
      eventStart.setHours(startHour, startMinute); // Set the start time

      const eventEnd = new Date(eventDate);
      eventEnd.setHours(endHour, endMinute); // Set the end time

      // Add the event with the calculated date and time
      events.value.push({
        title: 'Event',  // Customize event title
        start: eventStart.toISOString(),  // FullCalendar expects ISO format for date-time
        end: eventEnd.toISOString(),     // FullCalendar expects ISO format for date-time
      });
    }
  });

  // Empty the form and reset selected days
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = []; // Reset selected day

  console.log(events);
}

// Function to calculate the exact date for a given day of the week
function calculateDayOfWeek(dayCode) {
  const daysOfWeek = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];
  const dayIndex = daysOfWeek.indexOf(dayCode); // Get the index of the day (e.g., 'MO' -> 0, 'SU' -> 6)

  // Define the start of the current week (Monday)
  const currentDate = new Date();
  const startOfWeek = new Date(currentDate);
  startOfWeek.setDate(currentDate.getDate() - currentDate.getDay() + 1); // Set to Monday of the current week
  startOfWeek.setHours(0, 0, 0, 0); // Set time to 00:00:00
  
  if (dayIndex === -1) return null; // If invalid day code, return null

  // Calculate the date for this day based on the start of the week (Monday)
  const eventDate = new Date(startOfWeek);
  eventDate.setDate(startOfWeek.getDate() + dayIndex); // Add the day offset
  
  return eventDate; // Return the calculated Date
}


async function getEvents() {
  try {
    const response = await fetch('/availabilities');
    const data = await response.json();

    // Transform events based on rrule
    data.forEach(event => {
      const rrule = event.rrule; // The rrule string, e.g., "FREQ=WEEKLY;BYDAY=MO,SA"
      const eventDays = rrule.split(';')[1].replace('BYDAY=', '').split(','); // Extract days like ['MO', 'SA']

      eventDays.forEach(dayCode => {
        const eventDate = calculateDayOfWeek(dayCode); // Calculate the date for this event
        
        if (eventDate) {
          // Set the event's start and end time
          const [startHour, startMinute] = event.start.split(':');
          const [endHour, endMinute] = event.end.split(':');

          const eventStart = new Date(eventDate);
          eventStart.setHours(startHour, startMinute); // Set the start time

          const eventEnd = new Date(eventDate);
          eventEnd.setHours(endHour, endMinute); // Set the end time

          // Push the modified event into the events array
          events.value.push({
            title: event.title,
            start: eventStart.toISOString(),
            end: eventEnd.toISOString(),
          });
        }
      });
    });
  } catch (error) {
    console.error('Error fetching events:', error);
  }
}

// Log the options to ensure they're being set correctly
onMounted(() => {
  console.log("Calendar Options:", calendarOptions.value);
  document.querySelectorAll('.fc-day-today').forEach(el => {
    el.style.backgroundColor = 'transparent';
  });
  getEvents();
  console.log(events);
});
</script>

<style scoped>
/* Optional: Add custom styles for FullCalendar */
</style>
