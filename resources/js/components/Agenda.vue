<template>
  <div>
    <div v-if="calendarOptions">
      <FullCalendar 
        ref="calendarRef"
        :options="calendarOptions" 
      />
      <!-- Form to collect event details -->
      <div class="event-form">
        <form @submit.prevent="handleSubmit">
          <!-- Input for choosing the start time of an event -->
          <label for="start-time">Start Time</label>
          <input v-model="startTime" type="time" id="start-time" class="text-gray-dark" required />

          <!-- Input for choosing the end time if an event -->
          <label for="end-time">End Time</label>
          <input v-model="endTime" type="time" id="end-time" class="text-gray-dark" required />

          <!-- Checkboxes to select which days the event will take place -->
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

          <!-- Checkbox for adjusting one or all matching events -->
          <label v-if="showCheckbox">
            <input type="checkbox" v-model="applyToAll" @change="toggleApplyToAll" />
            Selecteer alle gelijkaardige events
          </label>

          <!-- Button for adjusting events -->
          <button v-if="changeButton" @click="saveChanges">Save Changes</button>

          <!-- Button to save new events -->
          <button v-if="submitButton" type="submit">Add</button>

          <!-- Cancel button -->
          <button @click="resetForm">Cancel</button>

          <!-- Button to save the week to the database -->
          <button @click="saveWeek">Save week</button>
        </form>
      </div>
    </div>
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

const selectedDays = ref([]); // Array to store selected days
const showCheckbox = ref(false); // Keeps track of visibility selectAll checkbox
const applyToAll = ref(false); // Keeps track of the state of selectAll checkbox
const changeButton = ref(false); // Keeps track of visibility changeButton
const submitButton = ref(true); // Keeps track of visibility sumbitButton
const currentEvent = ref(null); // Stores the current event for toggleApplyToAll
const selectedEvents = ref([]); // Array to store the selected events

const calendarRef = ref(null);


// Calendar options including initial view setup
const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],
  initialView: 'timeGridWeek',
  locale: 'nl',
  timeZone: 'local',
  dayHeaderFormat: { weekday: 'long' },
  firstDay: 1,
  allDaySlot: false,
  headerToolbar: false,
  events: function(info, successCallback) {
    successCallback(events.value);
  },
  eventClick: handleEventClick,
  eventRemove: handleEventRemove,
});

// Watch for changes to the events array and update the calendar
watch(events, () => {
  if (calendarRef.value) {
    const calendar = calendarRef.value.getApi();
    calendar.refetchEvents();
  }
}, { deep: true });

// Reduces the event array and saves it to the database
function saveWeek () {
  // extract hours and day from the start and end
  const dayMap = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];

  const extractTimes = events.value.map(event => ({
    title: event.title, 
    start: event.start.toTimeString().split(' ')[0],
    end: event.end.toTimeString().split(' ')[0],
    day: dayMap[new Date(event.start).getDay()]
  }));

  // Reduce to save the same events on different days together
  const reform = extractTimes.reduce((acc, event) => {
    const key = `${event.title}_${event.start}_${event.end}`;

    if (!acc[key]) {
      acc[key] = {
        title: event.title,
        start: event.start,
        end: event.end,
        days: []
      };
    }

    acc[key].days.push(event.day);

    return acc;
  }, {});

  // Make an array with the rrule structure
  const makeRrule = Object.values(reform).map(event => ({
    title: event.title,
    start: event.start,
    end: event.end,
    rrule: `FREQ=WEEKLY;BYDAY=${Array.from(event.days).join(',')}`
  }));
  
  // Save the events to the database
  axios.post('/availabilities', { makeRrule })
  .then(response => {console.log(response.data);})
  .catch(error => {console.error('Error:', error);});
}

// Handles the clicked event/form
function handleEventClick(info) {
  currentEvent.value = info.event;

  selectedEvents.value = [{
    title: currentEvent.value.title,
    start: currentEvent.value.start,
    end: currentEvent.value.end
  }];

  startTime.value = formatTime(currentEvent.value.start); 
  endTime.value = formatTime(currentEvent.value.end); 

  selectedDays.value = [convertDateToShortCode(currentEvent.value.start)];

  showCheckbox.value = true;
  changeButton.value = true;
  submitButton.value = false;
  applyToAll.value = false; 
}

// Handles if only the clicked or all similar events get selected
function toggleApplyToAll() {
  selectedDays.value = [];
  if (applyToAll.value) {
    const matchingEvents = events.value.filter(e => 
      formatTime(new Date(e.start)) === formatTime(currentEvent.value.start) &&
      formatTime(new Date(e.end)) === formatTime(currentEvent.value.end) &&
      e.title === currentEvent.value.title
    );

    selectedEvents.value = matchingEvents.map(e => ({
      title: e.title,
      start: new Date(e.start),
      end: new Date(e.end)
    }));

    selectedDays.value = matchingEvents.map(e => convertDateToShortCode(new Date(e.start)));
  } else {
    selectedDays.value = [convertDateToShortCode(currentEvent.value.start)];

    selectedEvents.value = [{
      title: currentEvent.value.title,
      start: currentEvent.value.start,
      end: currentEvent.value.end
    }];
  }
}

// Reforms the time to match the form start/end time
function formatTime(date) {
  return date.toLocaleTimeString('nl-BE', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Europe/Brussels' });
}

// Reforms the date to a day
function convertDateToShortCode(date) {
  const days = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
  return days[date.getDay()];
}

// Removes the selected events
function handleEventRemove(eventToRemove) {
  // Filter out the event from the `events` array based on matching properties
  events.value = events.value.filter(existingEvent => 
    !(existingEvent.start.getTime() === eventToRemove.start.getTime() &&
      existingEvent.end.getTime() === eventToRemove.end.getTime() &&
      existingEvent.title === eventToRemove.title)
  );
}

// Removes and adds the edited events
function saveChanges () {
  selectedEvents.value.forEach(selectedEvent => {
    // Call handleEventRemove to filter and remove matching events
    handleEventRemove(selectedEvent);
  });
  
  events.value = events.value.map(event => toRaw(event));

  // Add the events from the form
  handleSubmit()

  // Reset to the add an event form
  showCheckbox.value = false;
  changeButton.value = false;
  submitButton.value = true;
}

// Clears the form
function resetForm () {
  // Empty the form and reset selected days
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = [];
  
  // Reset to the add an event form
  showCheckbox.value = false;
  changeButton.value = false;
  submitButton.value = true;
}

// Handles the creation of a new event
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
        title: 'Event',
        start: eventStart, 
        end: eventEnd,     
      });
    }
  });

  // Empty the form and reset selected days
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = [];

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

// Reforms all the data to seperate events
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
            start: eventStart,
            end: eventEnd,
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
