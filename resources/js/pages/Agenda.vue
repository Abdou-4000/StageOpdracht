<script lang="js">

export default {
  data() {
    return {
      view: 'month',
    };
  },
  methods: {
    setView(val) {
      this.view = val;
    },
  },
};

</script>

<template>
    <!-- Calendar buttons -->
    <div class="w-screen overflow-y-auto overflow-x-hidden h-screen flex flex-col">
        <div class="flex justify-center w-full">
            <div class="relative flex w-1/2 m-4 mt-6 bg-red/30 rounded-3xl overflow-hidden">
            <!-- Slider -->
            <div
                class="absolute top-0 left-0 w-1/2 h-full rounded-3xl bg-red transition-transform duration-300"
                :class="{
                'translate-x-0': view === 'month',
                'translate-x-full': view === 'week',
                }"
            ></div>

            <!-- Buttons -->
            <button
                class="relative w-1/2 p-2 z-10 text-white"
                @click="setView('month')">
                month
            </button>
            <button
                class="relative w-1/2 p-2 z-10 text-white"
                @click="setView('week')">
                week
            </button>
            </div>
        </div>
        <!-- Calendar -->
        <div class="flex justify-center">
            <div v-if="view === 'week'">
                <AgendaWeek/>
            </div>
            <div v-if="view === 'month'"> 
                <AgendaMonth/>
            </div>
        </div>
    </div>
</template>