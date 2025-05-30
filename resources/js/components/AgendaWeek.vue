<template>
  <div class="text-gray-dark flex flex-col w-screen">
    <div v-if="calendarOptions">
      <!-- Succes message-->
      <p v-if="saveMessage"
        :class="[
          'mt-2 px-4 py-2 rounded transition-opacity duration-300 flex justify-center',
          saveMessageType === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : '',
          saveMessageType === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : ''
        ]">
        {{ saveMessage }}
      </p>

      <div class="flex flex-col lg:flex-row justify-between">
        <!-- Calendar -->
        <div class="flex w-5/6 lg:w-3/5 m-10" v-if="calendarOptions">
          <div class="w-full flex-1">
            <FullCalendar
              ref="calendarRef"
              :options="calendarOptions" 
            />
          </div>
        </div>
        <!-- Form to collect event details -->
        <div class="event-form flex flex-col w-3/5 lg:w-2/6 m-4 lg:mt-10 ml-10 lg:ml-4">
          <div class="flex text-gray-dark text-justify mb-10 ml-3 mr-3">
            In deze agenda kan de algemene weekplanning ingesteld worden per type.
            Sla de week op wanneer ze correct is ingevult.
          </div>
          <form class="flex flex-col w-full" @submit.prevent="handleSubmit">
            <!-- titel -->
            <div v-if="submitButton" class="ml-3 text-2xl font-semibold">Planning maken</div>
            <div v-if="changeButton" class="ml-3 text-2xl font-semibold">Planning bewerken</div>

            <!-- Input for choosing the start time of an event -->
            <div class="flex justify-between m-2">
                <label class="flex p-2" for="start-time">Starttijd</label>
                <input v-model="startTime" type="time" id="start-time" class="flex justify-center text-gray-dark flex border border-gray-300 p-2 w-1/4 rounded-3xl" required />
            </div>
    
            <!-- Input for choosing the end time if an event -->
            <div class="flex justify-between m-2">
                <label class="flex p-2" for="end-time">Eindtijd</label>
                <input v-model="endTime" type="time" id="end-time" class="flex justify-center text-gray-dark flex border border-gray-300 p-2 w-1/4 rounded-3xl" required />
            </div>

            <div class="flex justify-between">
              <!-- Checkboxes to select which days the event will take place -->
              <label class="ml-4 font-semibold">Weekdagen</label>

              <!-- Checkbox for adjusting one or all matching events -->
              <label class="flex justify-end text-accentPink" v-if="showCheckbox">
                <input class="m-2" type="checkbox" v-model="applyToAll" @change="toggleApplyToAll" />
                Selecteer gelijkaardig
              </label>
            </div>

            <div>
              <!-- Generate checkboxes dynamically from the weekdays array -->
              <div class="m-2" v-for="(day, index) in weekdays" :key="index">
                <input 
                  type="checkbox" 
                  :id="day.shortCode" 
                  :value="day.shortCode"
                  v-model="selectedDays"
                  class="m-2"
                />
                <label :for="day.shortCode">{{ day.name }}</label>
              </div>
            </div>

            <!-- Radio to select a sort -->
            <div v-if="showSort">
                <!-- tussentitel -->
                <div class="ml-4 font-semibold">Type</div>
                <!-- radio buttons -->
                <div class="m-2 ml-4" v-for="(sort, id) in sort" :key="id">
                  <label class="custom-radio-wrapper">
                    <input 
                      type="radio"
                      :id="sort.name"
                      :value="sort.name"
                      v-model="selectedSort"
                      name="sort"
                      class="custom-radio"
                    />
                    <span class="custom-radio-style"></span>
                    <span class="ml-2">{{ sort.name }}</span>
                  </label>
                </div>  
              </div>        
            
            <div class="flex justify-between m-2">
              <!-- Button for adjusting events -->
              <button class="bg-red text-white w-2/5 p-2 rounded-3xl" type="button" v-if="changeButton" @click="saveChanges">Verandering bewaren</button>

              <!-- Button to save new events -->
              <button class="bg-red text-white w-2/5 p-2 rounded-3xl" v-if="submitButton" type="submit">Toevoegen</button>

              <!-- Cancel button -->
              <button class="bg-red text-white w-2/5 p-2 rounded-3xl" type="button" @click="resetForm">Annuleren</button>
            </div>
          </form>
          <!-- Button to save the week to the database -->
          <button class="text-red border border-red w-2/5 m-2 p-2 rounded-3xl" v-if="saveWeekButton" @click="saveWeek">Week opslaan</button>

          <!-- Button to delete events-->
          <button class="text-red border border-red w-2/5 m-2 p-2 rounded-3xl" v-if="deleteButton" @click="deleteEvents">Verwijder</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang>
import { ref, onMounted, toRaw, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3'; 
import dayGridPlugin from '@fullcalendar/daygrid';  
import interactionPlugin from '@fullcalendar/interaction';  
import timeGridPlugin from '@fullcalendar/timegrid'; 
import listPlugin from '@fullcalendar/list'; 
import rrulePlugin from '@fullcalendar/rrule';
import axios from 'axios';

const page = usePage();
const teacherId = page.props.teacherId;

const events = ref([]);
const sort = ref([]);
const startTime = ref(''); // For user-entered start time
const endTime = ref(''); // For user-entered end time

// Array of weekdays with short codes
const weekdays = ref([
  { name: 'Maandag', shortCode: 'MO' },
  { name: 'Dinsdag', shortCode: 'TU' },
  { name: 'Woensdag', shortCode: 'WE' },
  { name: 'Donderdag', shortCode: 'TH' },
  { name: 'Vrijdag', shortCode: 'FR' },
  { name: 'Zaterdag', shortCode: 'SA' },
  { name: 'Zondag', shortCode: 'SU' }
]);

const selectedDays = ref([]); // Array to store selected days
const selectedSort = ref(null); // Keeps track of the selected sort
const showSort = ref(true); // Keeps track of the visibility of sort radio
const showCheckbox = ref(false); // Keeps track of visibility selectAll checkbox
const applyToAll = ref(false); // Keeps track of the state of selectAll checkbox
const changeButton = ref(false); // Keeps track of visibility changeButton
const submitButton = ref(true); // Keeps track of visibility sumbitButton
const deleteButton = ref(false); // Keeps track of visibility deleteButton
const saveWeekButton = ref(true); // Keeps track of visibility saveWeekButton
const currentEvent = ref(null); // Stores the current event for toggleApplyToAll
const selectedEvents = ref([]); // Array to store the selected events
const saveMessage = ref('');
const saveMessageType = ref('');

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
  slotEventOverlap: false,
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

watch(() => selectedSort.value, (newValue) => {
  console.log('Selected Sort:', newValue);
});


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
  axios.post(`/availabilities/${teacherId}`, makeRrule)
  .then(response => {
    console.log(response.data)
    saveMessage.value = 'Week succesvol opgeslagen!'
    saveMessageType.value = 'success'
  })
  .catch(error => {
    console.error('Error:', error)
    saveMessage.value = 'Fout bij het opslaan van de week. Probeer opnieuw.'
    saveMessageType.value = 'error'
  })
  .finally(() => {
    setTimeout(() => {
      saveMessage.value = ''
      saveMessageType.value = ''
    }, 3000)
  })
}

// Handles the clicked event/form
function handleEventClick(info) {
  currentEvent.value = info.event;
  selectedSort.value = currentEvent.value.title;

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
  deleteButton.value = true;
  saveWeekButton.value = false;
  applyToAll.value = false;
  showSort.value = false; 
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

// Removes the selected events
function deleteEvents () {
  selectedEvents.value.forEach(selectedEvent => {
    // Call handleEventRemove to filter and remove matching events
    handleEventRemove(selectedEvent);
  });
}

// Removes and adds the edited events
function saveChanges () {
  deleteEvents();
  
  events.value = events.value.map(event => toRaw(event));

  // Add the events from the form
  handleSubmit();

  // Reset to the add an event form
  showCheckbox.value = false;
  changeButton.value = false;
  submitButton.value = true;
  deleteButton.value = false;
  saveWeekButton.value = true;
  showSort.value = true;
}

// Clears the form
function resetForm () {
  // Empty the form and reset selected days
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = [];
  selectedSort.value = '';
  
  // Reset to the add an event form
  showCheckbox.value = false;
  changeButton.value = false;
  submitButton.value = true;
  deleteButton.value = false;
  saveWeekButton.value = true;
  showSort.value = true;
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

  if (!selectedSort.value) {
    alert('Please select the type of activity');
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

      // Checks if the end time is on the next day
      if (eventEnd <= eventStart) {
          eventEnd.setDate(eventEnd.getDate() + 1);
      }

      // Add the event with the calculated date and time
      events.value.push({
        title: selectedSort.value,
        start: eventStart, 
        end: eventEnd,     
      });
    }
  });

  // Empty the form and reset selected days
  startTime.value = '';
  endTime.value = '';
  selectedDays.value = [];
  selectedSort.value = '';

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
    const response = await fetch(`/availabilities/${teacherId}`);
    const data = await response.json();

    const sorts = data.sorts;
    const availabilities = data.availabilities;

    sorts.forEach(s => {
      sort.value.push({
        id: s.id,
        name: s.name,
      });
    })

    // Transform events based on rrule
    availabilities.forEach(event => {
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

          // Checks if the end time is on the next day
          if (eventEnd <= eventStart) {
              eventEnd.setDate(eventEnd.getDate() + 1);
          }

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
  console.log(sort);
});
</script>

<style>
@import '../../css/radioButton.css';
</style>
