<script setup lang>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  user: Object,
})

</script>

<template>
    <div class="bg-white">
        <!-- header -->
        <div class="flex flex-row w-full items-center justify-between">
            <!-- Logo -->
            <div class="flex">
                <img class="w-[120px]" src="../../../public/assets/Logo.png" alt="Logo">
                <div class="flex text-gray-dark ml-6 items-center w-full gap-10 font-medium" v-if="user">
                    <p>Welkom {{ user.name }}</p>
                    <Link class="text-red hover:underline" href="/logout" method="POST" as="button">Log uit</Link>
                </div>
                <a v-else class="flex text-gray-dark ml-2 items-center font-medium hover:underline" :href="`/login`"> 
                    Inloggen
                </a>
            </div>
            <div class="flex">
                <!-- Teacher button to teacherprofile -->
                <a class="flex items-center text-gray-dark font-medium mr-4 hover:underline" :href="`/teacherprofile`" v-if="user?.roles?.includes('teacher')"> 
                    Leerkrachtenprofiel
                </a>

                <!-- Admin button to adminpage -->
                <a class="flex items-center text-gray-dark font-medium mr-4 hover:underline" :href="`/teachers`" v-if="user?.roles?.includes('admin') || user?.roles?.includes('super_admin')">
                    Beheer
                </a>
            </div>
        </div>
        <div class="flex justify-center pb-2 text-4xl font-semibold text-gray-middle">Leerkrachtendatabase</div>
        <!-- Map -->
        <div id="app">
            <TeacherMap :user="user"/>
        </div>
        <div>
            <FooterF/>
        </div>
    </div>
</template>

<style>
html, body {
  background-color: white;
}
</style>