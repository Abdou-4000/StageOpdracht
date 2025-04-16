<script setup lang="js">
import { ref } from 'vue'

const agendaView = ref('month') // of 'week'

function setView(view) {
  agendaView.value = view
}

</script>

<template>
    <div class="w-screen overflow-y-auto overflow-x-hidden h-screen flex flex-col">
        <div class="flex justify-between p-4">
            <button class="text-gray-dark" @click="setView('month')">
                month
            </button>
            <button class="text-gray-dark" @click="setView('week')">
                week
            </button>
        </div>
        <div class="flex justify-center">
            <div v-if="agendaView === 'week'">
                <AgendaWeek/>
            </div>
            <div v-if="agendaView === 'month'"> 
                <AgendaMonth/>
            </div>
        </div>
    </div>
</template>