<script setup>

defineProps({
  teacher: Object
})

</script>

<template>
    <div class="header">
        <img src="../../../public/assets/Logo.png" alt="Logo">
        <div>
            <a :href="`/agenda/${teacher.id}`" class="text-accentBlue hover:underline">Bewerk kalender</a>
        </div>
    </div>
    <div>
        <TeacherProfile :teacher="teacher" :show="true"/> 
    </div>
</template>