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
            <div v-if="showStartTime">
                <label for="start-time">Start Time</label>
                <input v-model="startTime" type="time" id="start-time" class="text-gray-dark" required />
            </div>
  
            <!-- Input for choosing the end time if an event -->
            <div v-if="showEndTime">
                <label for="end-time">End Time</label>
                <input v-model="endTime" type="time" id="end-time" class="text-gray-dark" required />
            </div>
  
            <!-- Radio to select a sort -->
            <div v-if="showSort" v-for="(sort, id) in sort" :key="id">
              <input 
                type="radio"
                :id="sort.name"
                :value="sort.name"
                v-model="selectedSort"
              />
              <label :for="sort.name">{{ sort.name }}</label>
            </div>          
  
            <!-- Button for adjusting events -->
            <button v-if="changeButton" @click="saveChanges">Save Changes</button>
  
            <!-- Button to save new events -->
            <button v-if="submitButton" type="submit">Add exception</button>
  
            <!-- Cancel button -->
            <button v-if="cancelButton" @click="resetForm">Cancel</button>

            <!-- Delete button -->
            <button v-if="deleteButton" @click="removeEvent">Delete</button>
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

const calendarRef = ref(null);
const events = ref([]);
const exceptions = ref([]);
const sort = ref([]);
const startTime = ref(''); // For user-entered start time
const endTime = ref(''); // For user-entered end time
const selectedSort = ref(null); // Keeps track of the selected sort
const selectedDate = ref(null); // Keeps track of the selected date
const selectedEvent = ref(null); // Keeps track of the selected event

const changeButton = ref(false); // Keeps track of visibility changeButton
const submitButton = ref(false); // Keeps track of visibility submitButton
const cancelButton = ref(false); // Keeps track of visibility cancelButton
const deleteButton = ref(false); // Keeps track of visibility deleteButton
const showSort = ref(false); // Keeps track of the visibility of sort radio
const showStartTime = ref(false); // Keeps track of visibility startTime
const showEndTime = ref(false); // Keeps track of visibility endTime

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],
  initialView: 'dayGridMonth',
  locale: 'nl',
  timeZone: 'local',
  firstDay: 1,
  allDaySlot: false,
  displayEventTime: true,
  displayEventEnd: true,
  headerToolbar: {
    left: 'next today',
    center: 'title',
    right: 'dayGridMonth dayGridWeek'
  },
  eventSources: [
    {
        events: function(info, successCallback) {
            successCallback(events.value);
        },
        color: 'red'
    },
    {
        events: function(info, successCallback) {
            successCallback(exceptions.value);
        },
        color: 'blue',
    }
  ],
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false // set to true for AM/PM
  },
  dateClick: handleDateClick,
  eventClick: handleEventClick,
});

// Watch for changes to the events array and update the calendar
watch(events, () => {
  if (calendarRef.value) {
    const calendar = calendarRef.value.getApi();
    calendar.refetchEvents();
  }
}, { deep: true });

watch(exceptions, () => {
  if (calendarRef.value) {
    const calendar = calendarRef.value.getApi();
    calendar.refetchEvents();
  }
}, { deep: true });

function removeEvent () {

  }

  function saveChanges() {
    console.log('clicked');
    
    // Find the exception based on the original values we stored
    const updatedEvent = exceptions.value.find(event => 
        isSameDateTime(event.start, selectedEvent.value.start) && 
        isSameDateTime(event.end, selectedEvent.value.end) &&
        event.title === selectedEvent.value.title
    );
    
    if (updatedEvent) {
        // Create new Date objects based on the time input values
        const newStart = createDateFromTimeString(startTime.value, selectedEvent.value.start);
        const newEnd = createDateFromTimeString(endTime.value, selectedEvent.value.end);

        // Update the properties
        updatedEvent.start = newStart;
        updatedEvent.end = newEnd;
        
        console.log('Updated event:', updatedEvent);
        console.log('Updated exceptions array:', exceptions.value);
    } else {
        console.log('Event not found!');
    }
    
    // Reset and hide the form
    startTime.value = '';
    endTime.value = '';
    showStartTime.value = false;
    showEndTime.value = false;
    showSort.value = false;
    cancelButton.value = false;
    changeButton.value = false;
    deleteButton.value = false;
}

// Helper function to compare two dates including time
function isSameDateTime(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate() &&
           date1.getHours() === date2.getHours() &&
           date1.getMinutes() === date2.getMinutes();
}

// Helper function to create a date from a time string and a reference date
function createDateFromTimeString(timeString, referenceDate) {
    const [hours, minutes] = timeString.split(':').map(Number);
    const date = new Date(referenceDate);
    date.setHours(hours, minutes, 0, 0);
    return date;
}

// Shows the edit form
function handleEventClick(info) {
    if (info.event.extendedProps.isException) {
        // Shows the form
        showStartTime.value = true;
        showEndTime.value = true;
        changeButton.value = true;
        cancelButton.value = true;
        deleteButton.value = true;

        // Set the time to the currently saved time
        startTime.value = formatTime(info.event.start);
        endTime.value = formatTime(info.event.end);

        // Extract only the needed properties
        selectedEvent.value = {
            title: info.event.title,
            start: info.event.start,
            end: info.event.end,
            isException: true
        };
        
        console.log('selected event:', selectedEvent.value);
    }
}

// Reforms the time to the correct form
function formatTime(date) {
  return date.toLocaleTimeString('nl-BE', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Europe/Brussels' });
}

// Resets the form
function resetForm () {
    // Resets the input
    startTime.value = '';
    endTime.value = '';
    selectedDate.value = '';

    // Hides the form
    showStartTime.value = false;
    showEndTime.value = false;
    showSort.value = false;
    submitButton.value = false;
    cancelButton.value = false;
    changeButton.value = false;
    deleteButton.value = false;
}

// Save the details from the form to the exceptions array
function handleSubmit () {
    // Checks if a sort is selected
    if (!selectedSort.value) {
        alert('Please select the type of activity');
        return;
    }

    // Sets the correct hours from start and end time inputs
    const [startHour, startMinute] = startTime.value.split(':');
    const [endHour, endMinute] = endTime.value.split(':');

    const eventStart =  new Date(selectedDate.value);
    const eventEnd = new Date(selectedDate.value);

    eventStart.setHours(startHour, startMinute);
    eventEnd.setHours(endHour, endMinute);

    // Pushes the object into the exceptions array
    exceptions.value.push({
        title: selectedSort.value,
        start: eventStart,
        end: eventEnd,
        isException: true
    });

    console.log(exceptions);
    
    // Resets the input
    startTime.value = '';
    endTime.value = '';

    // Resets the form
    showStartTime.value = false;
    showEndTime.value = false;
    showSort.value = false;
    submitButton.value = false;
    cancelButton.value = false;
}

// Select the day to add an exception and show the form
function handleDateClick (info) {
    // Shows the form
    showStartTime.value = true;
    showEndTime.value = true;
    showSort.value = true;
    submitButton.value = true;
    cancelButton.value = true;

    // Saves the selected date
    selectedDate.value = new Date(info.date);
}

async function getExceptions () {
    try {
        const response = await fetch('/exceptions');
        const data = await response.json();

        exceptions.value = [];

        data.exceptions.map(event => {
          exceptions.value.push({
            title: event.title,
            start: event.start,
            end: event.end,
            isException: true
          });
        });
        console.log(data);
    } catch (error) {
        console.error('Error fetching events:', error);
    }
}

// Fetches the availability data
async function getEvents () {
    try {
        const response = await fetch('/availabilities');
        const data = await response.json();

        const sorts = data.sorts;
        const availabilities = data.availabilities;

        sorts.forEach(s => {
            sort.value.push({
                id: s.id,
                name: s.name,
            });
        });
        
        const startDate = new Date();

        availabilities.forEach(event => {
            // Set the startdate to the correct format
            startDate.setHours(...event.start.split(":").map(Number));
            const dtstart = `${startDate.getFullYear()}-${(startDate.getMonth() + 1).toString().padStart(2, '0')}-${startDate.getDate().toString().padStart(2, '0')}T${startDate.toTimeString().split(' ')[0]}`;

            // Make an array of the days
            const byweekday = event.rrule
                .split(';')[1] // Get the part after FREQ=weekly;
                .split('=')[1] // Extract the days part (BYDAY=MO,WE,TH,FR)
                .split(','); // Split into individual days
            
            // Calculate duration
            const [startHour, startMinute] = event.start.split(":").map(Number);
            const [endHour, endMinute] = event.end.split(":").map(Number);

            const startTime = new Date();
            startTime.setHours(startHour, startMinute, 0);

            const endTime = new Date();
            endTime.setHours(endHour, endMinute, 0);

            const durationMilliseconds = endTime - startTime;
            const durationHours = Math.floor(durationMilliseconds / (1000 * 60 * 60));
            const durationMinutes = Math.floor((durationMilliseconds % (1000 * 60 * 60)) / (1000 * 60));

            const duration = `${String(durationHours).padStart(2, "0")}:${String(durationMinutes).padStart(2, "0")}`;

            // Push the event into the events array
            events.value.push({
                title: event.title,
                rrule: {
                    freq: "weekly", 
                    byweekday: byweekday,
                    dtstart: dtstart
                },
                duration: duration 
            })
        });

        console.log(events);
    } catch (error) {
        console.error('Error fetching events:', error);
    }
}

// Log the options to ensure they're being set correctly
onMounted(() => {
  console.log("Calendar Options:", calendarOptions.value);
  getEvents();
  getExceptions();
  console.log(events);
});
</script>

<style>
/* Optional: Add custom styles for FullCalendar */
</style>