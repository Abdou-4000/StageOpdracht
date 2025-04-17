<script setup lang>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  user: Object,
})

</script>

<template>
        <!-- header -->
        <div class="flex flex-row justify-between m-4">
            <!-- Logo -->
            <div class="flex flex-row">
                <img class="w-1/6" src="../../../public/assets/Logo.png" alt="Logo">
                <div class="flex text-gray-dark w-1/4 ml-2 items-center justify-between w-full font-medium" v-if="user">
                    <p>Welkom {{ user.name }}</p>
                    <Link class="text-red" href="/logout" method="POST" as="button">Log uit</Link>
                </div>
                <a v-else class="flex text-gray-dark ml-2 items-center font-medium" :href="`/login`"> 
                    Inloggen
                </a>
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
            <FooterF/>
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