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

<script setup lang>
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

const props = defineProps({
  id: {
    type: [Number, String],
    required: true
  }
});

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
        color: '#9C91c5'
    },
    {
        events: function(info, successCallback) {
            successCallback(exceptions.value);
        },
        color: '#71bdba',
    }
  ],
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false // set to true for AM/PM
  },
  eventDidMount: function(info) {
    const event = info.event;
    const eventDate = event.start.toISOString().split('T')[0]; // Get the date in YYYY-MM-DD format
    
    // Check if this event's date should be excluded
    if (event.extendedProps.exdate && event.extendedProps.exdate.includes(eventDate)) {
      console.log(`Event ${event.title} is being excluded on ${eventDate}`);
      // You could also mark the event as invisible if needed
      event.remove();  // This will remove the event from the calendar (for testing purposes)
    }
    
    console.log('Event being rendered:', event.title, event.start);
    console.log('Exclusion Dates:', event.extendedProps.exdate);
  }
});

// Fetches the exceptions data
async function getExceptions () {
    try {
        const response = await fetch(`/exceptions/${props.id}`);
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
        const response = await fetch(`/availabilities/${props.id}`);
        const data = await response.json();

        const availabilities = data.availabilities;
        
        const startDate = new Date();
        const pad = num => num.toString().padStart(2, '0');

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

            const type = event.title;

        // Set exdate
            const exceptionDates = exceptions.value
                .filter(exception => exception.title === type)
                .map(exception => {
                    const [date] = exception.start.split(' ');
                    const [year, month, day] = date.split('-');

                    const eventDate = new Date(year, month - 1, day); // months are 0-indexed
                    eventDate.setHours(startHour, startMinute, 0, 0);

                    const formattedDate = `${eventDate.getFullYear()}-${pad(eventDate.getMonth() + 1)}-${pad(eventDate.getDate())}T${pad(eventDate.getHours())}:${pad(eventDate.getMinutes())}:${pad(eventDate.getSeconds())}`;

                    return formattedDate;
                });

            console.log('exceptions', exceptionDates)
            
        // Push the event into the events array
            events.value.push({
                title: event.title,
                rrule: {
                    freq: "weekly", 
                    byweekday: byweekday,
                    dtstart: dtstart
                },
                duration: duration ,
                exdate: exceptionDates
            })
        });

        console.log(events);
    } catch (error) {
        console.error('Error fetching events:', error);
    }
}

// Log the options to ensure they're being set correctly
onMounted(async () => {
  console.log("Calendar Options:", calendarOptions.value);
  await getExceptions();
  await getEvents();
  console.log(events);
  if (calendarRef.value) {
    calendarRef.value.getApi().refetchEvents();
  }
});
</script>

<style>
.fc-list-day-cushion {
    background-color: #343943 !important; /* bg color days */
    color: white;
    font-weight: bold;
    padding: 10px;
    font-size: 1.1rem;
}
.fc .fc-list-day {
  border-bottom: 2px solid #fbba00; /* border days */
}
.fc .fc-list-event {
  border-bottom: 2px solid #fbba00; /* border event */
}
.fc .fc-list {
  border: 2px solid #fbba00; /* whole calendar border */
  border-radius: 8px;
  overflow: hidden;
}
.fc-list .fc-list-event {
    color: white; /* text color event */
}
.fc-list .fc-list-event:hover {
    color: black; /* hover text color */
}
</style>
