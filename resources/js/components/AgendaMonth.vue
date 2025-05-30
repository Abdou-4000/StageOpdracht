<template>
    <div class="text-gray-dark flex flex-col w-screen">
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
              Deze agenda geeft de algemene weekplanning weer als deze is ingesteld.
              Een uitzondering kan worden aangemaakt door een dag te selecteren.
              Uitzonderingen kunnen bewerkt of verwijderd worden door de uitzondering te selecteren.
            </div>
            <form class="flex flex-col w-full" @submit.prevent="handleSubmit">
              <!-- titel -->
              <div v-if="submitButton" class="ml-3 text-2xl font-semibold">Uitzondering maken</div>
              <div v-if="changeButton" class="ml-3 text-2xl font-semibold">Uitzondering bewerken</div>

              <!-- Input for choosing the start time of an event -->
              <div class="flex justify-between m-2" v-if="showStartTime">
                  <label class="flex p-2" for="start-time">Starttijd</label>
                  <input v-model="startTime" type="time" id="start-time" class="flex justify-center text-gray-dark flex border border-gray-300 p-2 w-1/4 rounded-3xl" required />
              </div>
    
              <!-- Input for choosing the end time if an event -->
              <div class="flex justify-between m-2" v-if="showEndTime">
                  <label class="flex p-2" for="end-time">Eindtijd</label>
                  <input v-model="endTime" type="time" id="end-time" class="flex justify-center text-gray-dark flex border border-gray-300 p-2 w-1/4 rounded-3xl" required />
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
                <button class="bg-red text-white w-2/5 p-2 rounded-3xl" v-if="changeButton" type="button" @click="saveChanges">Verandering opslaan</button>
      
                <!-- Button to save new events -->
                <button class="bg-red text-white w-2/5 p-2 rounded-3xl" v-if="submitButton" type="submit">Uitzondering toevoegen</button>
      
                <!-- Cancel button -->
                <button class="bg-red text-white w-2/5 p-2 rounded-3xl" v-if="cancelButton" type="button" @click="resetForm">Annuleren</button>
              </div>
                <!-- Delete button -->
                <button class="text-red border border-red w-2/5 m-2 p-2 rounded-3xl" v-if="deleteButton" type="button" @click="removeEvent">Verwijderen</button>
            </form>
        </div>
      </div>
    </div>
</template>

<script setup lang>
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3'; 
import dayGridPlugin from '@fullcalendar/daygrid';  
import interactionPlugin from '@fullcalendar/interaction';  
import timeGridPlugin from '@fullcalendar/timegrid'; 
import listPlugin from '@fullcalendar/list'; 
import rrulePlugin from '@fullcalendar/rrule';
import nlLocale from '@fullcalendar/core/locales/nl';
import axios from 'axios';

const page = usePage();
const teacherId = page.props.teacherId;

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
const saveMessage = ref('');
const saveMessageType = ref('');

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],
  initialView: 'dayGridMonth',
  locale: nlLocale,
  timeZone: 'local',
  firstDay: 1,
  allDaySlot: false,
  displayEventTime: true,
  displayEventEnd: true,
  validRange: {
    start: new Date().toISOString().split('T')[0],
  },
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth dayGridWeek'
  },
  eventSources: [
    {
        events: function(info, successCallback) {
            successCallback(events.value);
        },
        color: '#fbba00'
    },
    {
        events: function(info, successCallback) {
            successCallback(exceptions.value);
        },
        color: '#ff3521',
    }
  ],
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false // set to true for AM/PM
  },
  eventContent: function (arg) {
    console.log(arg);
    const time = arg.timeText;
    const title = arg.event.title;
    const dotColor = arg.backgroundColor || '#3788d8'; // fallback default color

    return {
      html: `
        <div class="fc-event-custom flex items-start gap-1 text-sm leading-tight whitespace-normal break-words overflow-hidden">
          <span class="mt-[0.3rem] w-[0.5rem] h-[0.5rem] rounded-full" style="background-color: ${dotColor};"></span>
          <div>
            <span>${time}</span>
            <span class="font-semibold"> ${title}</span>
          </div>
        </div>
      `,
    };
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
    const deletedEvent = {
        id: selectedEvent.value.id,
        title: selectedEvent.value.title,
        start: formatToDateTimeString(selectedEvent.value.start),
        end: formatToDateTimeString(selectedEvent.value.end),
    }

    // The URL to send the  request to
    const url = `/exceptions/${deletedEvent.id}`;

    // Axios PUT request
    axios.delete(url, deletedEvent)
      .then(response => {
        console.log('Event deleted successfully:', response.data)
        saveMessage.value = 'Uitzondering succesvol verwijderd!'
        saveMessageType.value = 'success'
      })
      .catch(error => {
        console.error('Error deleting event:', error)
        saveMessage.value = 'Fout bij het verwijderen van de uitzondering. Probeer opnieuw.'
        saveMessageType.value = 'error'
      })
      .finally(() => {
        setTimeout(() => {
          saveMessage.value = ''
          saveMessageType.value = ''
        }, 3000)
      });
    
    // Fetch the exceptions
    getExceptions();
    
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

// Saves the changes made to the selecetedEvent
function saveChanges() {
    // Set the selectedEvent with the chosen hours
    const [startHour, startMinute] = startTime.value.split(':');
    const [endHour, endMinute] = endTime.value.split(':');

    const eventStart = new Date(selectedEvent.value.start);
    const eventEnd = new Date(selectedEvent.value.end);

    eventStart.setHours(startHour, startMinute);
    eventEnd.setHours(endHour, endMinute);

    // Checks if the end time is on the next day
    if (eventEnd <= eventStart) {
        eventEnd.setDate(eventEnd.getDate() + 1);
    }

    const updatedEvent = {
        id: selectedEvent.value.id,
        title: selectedEvent.value.title,
        start: formatToDateTimeString(eventStart),
        end: formatToDateTimeString(eventEnd),
    }

    // The URL to send the PUT request to
    const url = `/exceptions/${updatedEvent.id}`;

    // Axios PUT request
    axios.put(url, updatedEvent)
      .then(response => {
        console.log('Event updated successfully:', response.data)
        saveMessage.value = 'Uitzondering succesvol bijgewerkt!'
        saveMessageType.value = 'success'
      })
      .catch(error => {
        console.error('Error updating event:', error)
        saveMessage.value = 'Fout bij het bijwerken van de uitzondering. Probeer opnieuw.'
        saveMessageType.value = 'error'
      })
      .finally(() => {
        setTimeout(() => {
          saveMessage.value = ''
          saveMessageType.value = ''
        }, 3000)
      });
    
    // Fetch the exceptions
    getExceptions();

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

// Shows the edit form
function handleEventClick(info) {
    if (info.event.extendedProps.isException) {
        // Shows the form
        showStartTime.value = true;
        showEndTime.value = true;
        showSort.value = false;
        changeButton.value = true;
        cancelButton.value = true;
        deleteButton.value = true;
        submitButton.value = false;

        // Set the time to the currently saved time
        startTime.value = formatTime(info.event.start);
        endTime.value = formatTime(info.event.end);

        // Extract only the needed properties
        selectedEvent.value = {
            id: info.event.id,
            title: info.event.title,
            start: info.event.start,
            end: info.event.end,
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

    // Checks if the end time is on the next day
    if (eventEnd <= eventStart) {
        eventEnd.setDate(eventEnd.getDate() + 1);
    }

    // Pushes the object into the exceptions array
    const exception = {
        title: selectedSort.value,
        start: formatToDateTimeString(eventStart),
        end: formatToDateTimeString(eventEnd), 
    };

    console.log(exceptions);

    // Save the events to the database
    axios.post(`/exceptions/${teacherId}`, exception)
      .then(response => {
        console.log(response.data)
        saveMessage.value = 'Uitzondering succesvol opgeslagen!'
        saveMessageType.value = 'success'
      })
      .catch(error => {
        console.error('Error:', error)
        saveMessage.value = 'Fout bij het opslaan van de uitzondering. Probeer opnieuw.'
        saveMessageType.value = 'error'
      })
      .finally(() => {
        setTimeout(() => {
          saveMessage.value = ''
          saveMessageType.value = ''
        }, 3000)
      });

    getExceptions();
    
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

// Sets the date into the correct format for the database
function formatToDateTimeString(date) {
  return `${date.getFullYear()}-${(date.getMonth() + 1)
    .toString()
    .padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')} ${date
    .getHours()
    .toString()
    .padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:00`;
}

// Select the day to add an exception and show the form
function handleDateClick (info) {
    const clickedDate = info.date;
    const today = new Date();
  
    // Prevent selecting dates in the past
    if (clickedDate < today) {
      return;
    }
    
    // Shows the form
    showStartTime.value = true;
    showEndTime.value = true;
    showSort.value = true;
    submitButton.value = true;
    cancelButton.value = true;
    changeButton.value = false;
    deleteButton.value = false;

    startTime.value = '';
    endTime.value = '';
    selectedSort.value = '';

    // Saves the selected date
    selectedDate.value = new Date(info.date);
    console.log('selected day', selectedDate.value)
}

async function getExceptions () {
    try {
        const response = await fetch(`/exceptions/${teacherId}`);
        const data = await response.json();

        exceptions.value = [];

        data.exceptions.map(event => {
          exceptions.value.push({
            id: event.id,
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
        const response = await fetch(`/availabilities/${teacherId}`);
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

            // Checks if the end time is on the next day
            if (endTime <= startTime) {
                endTime.setDate(endTime.getDate() + 1);
            }

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
@import '../../css/radioButton.css';
</style>