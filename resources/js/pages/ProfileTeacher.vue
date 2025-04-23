<script setup lang="js">

defineProps({
  teacher: Object
})

</script>

<template>
<div class="bg-white">
    <!-- Header -->
    <div class="flex flex-row justify-between items-center p-4">
        <div class="flex w-1/12 items-center">
            <img src="../../../public/assets/Logo.png" alt="Logo">
            <a :href="`/map`" class="text-gray-dark hover:underline p-2">Terug</a>
        </div>
        <div class="flex">
            <a :href="`/agenda/${teacher.id}`" class="text-gray-dark hover:underline">Bewerk kalender</a>
        </div>
    </div>
    <!-- profile -->
    <div class="flex justify-center">
        <TeacherProfile :teacher="teacher" :show="true"/> 
    </div>
</div>
</template>