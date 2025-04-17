<script setup lang>

const props = defineProps({
  user: Object,
})

</script>

<template>
        <!-- header -->
        <div class="flex flex-row justify-between m-4">
            <!-- Logo -->
            <div class="w-1/12">
                <img src="../../../public/assets/Logo.png" alt="Logo">
            </div>
            <div class="flex w-1/6 h-10">
                <!-- Teacher button to teacherprofile -->
                <a class="flex justify-center bg-red text-white w-full p-2 rounded-3xl" :href="`/teacherprofile`" v-if="user?.roles?.includes('teacher')"> 
                    Leerkrachtenprofiel
                </a>

                <!-- Admin button to adminpage -->
                <a class="flex justify-center bg-red text-white w-full p-2 rounded-3xl" :href="`/teachers`" v-if="user?.roles?.includes('admin')">
                    Beheer
                </a>
            </div>
        </div>
        <!-- Map -->
        <div id="app">
            <TeacherMap/>
        </div>
        <div>
            <Footer/>
        </div>
</template>

<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100vh;
        width: 100vw;
        overflow-x: hidden;
        overflow-y: auto; 
    }
    #app {
        height: 100vh;
        width: 100vw;
    }
</style>