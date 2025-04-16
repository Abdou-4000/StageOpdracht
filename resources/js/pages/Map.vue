<script setup lang=js>

const props = defineProps({
  user: Object,
})

</script>

<template>
    <div>
        <!-- header -->
        <div class="flex flex-row justify-between m-4">
            <!-- Logo -->
            <div class="w-1/12">
                <img src="../../../public/assets/Logo.png" alt="Logo">
            </div>
            <div class="flex justify-center w-1/6 h-10 bg-red text-white p-2 rounded-3xl">
                <!-- Teacher button to teacherprofile -->
                <a :href="`/teacherprofile`" v-if="user?.roles?.includes('teacher')"> 
                    Leerkrachtenprofiel
                </a>

                <!-- Admin button to adminpage -->
                <a :href="`/teachers`" v-if="user?.roles?.includes('admin')">
                    Beheer
                </a>
            </div>
        </div>
        <!-- Map -->
        <div id="app">
            <TeacherMap/>
        </div>
    </div>
</template>

<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
    }
    #app {
        height: 100vh;
        width: 100vw;
    }
</style>