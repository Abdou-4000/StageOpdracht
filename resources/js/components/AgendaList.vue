<template>
    <div>
      <div v-if="calendarOptions">
        <FullCalendar 
          ref="calendarRef"
          :options="calendarOptions" 
        />
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3'; 
import dayGridPlugin from '@fullcalendar/daygrid';  
import interactionPlugin from '@fullcalendar/interaction';  
import timeGridPlugin from '@fullcalendar/timegrid'; 
import listPlugin from '@fullcalendar/list'; 
import rrulePlugin from '@fullcalendar/rrule';

const calendarRef = ref(null);
const events = ref([]);
const exceptions = ref([]);
const sort = ref([]);

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin, rrulePlugin],
  initialView: 'listWeek',
  locale: 'nl',
  timeZone: 'local',
  firstDay: 1,
  allDaySlot: false,
  displayEventTime: true,
  displayEventEnd: true,
  headerToolbar: {
    left: 'next',
    center: 'title',
    right: 'today'
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
});

// Fetches the exceptions data
async function getExceptions () {
    try {
        const response = await fetch('/exceptions');
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
