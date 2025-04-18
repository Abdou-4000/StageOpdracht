<script setup lang="js">

defineProps({
  teacher: Object
})

</script>

<template>
    <!-- Header -->
    <div class="flex flex-row justify-between items-center p-4">
        <div class="flex w-1/12">
            <img src="../../../public/assets/Logo.png" alt="Logo">
        </div>
        <div class="flex">
            <a :href="`/agenda/${teacher.id}`" class="text-accentBlue hover:underline">Bewerk kalender</a>
        </div>
    </div>
    <div>
        <TeacherProfile :teacher="teacher" :show="true"/> 
    </div>
</template>