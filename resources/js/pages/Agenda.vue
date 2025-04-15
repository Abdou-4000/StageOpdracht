<script setup>
import { ref } from 'vue'

const agendaWeek = ref(false);
const agendaMonth = ref(true);

function showMonth () {
    agendaWeek.value = false;
    agendaMonth.value = true;
}

function showWeek () {
    agendaWeek.value = true;
    agendaMonth.value = false;
}

</script>

<template>
    <div class="flex justify-between p-4">
        <button class="text-gray-dark" @click="showMonth">
            month
        </button>
        <button class="text-gray-dark" @click="showWeek">
            week
        </button>
    </div>
    <div class="flex flex-wrap flex-row gap-32">
        <div v-if="agendaWeek">
            <AgendaWeek/>
        </div>
        <div v-if="agendaMonth"> 
            <AgendaMonth/>
        </div>
    </div>
</template>