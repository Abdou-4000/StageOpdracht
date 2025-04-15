<script setup>

defineProps({
  teacher: Object
})

</script>

<template>
    <div class="header">
        <img src="../../../public/assets/Logo.png" alt="Logo">
    </div>
    <div>
        <TeacherProfile :teacher="teacher" :show="true"/> 
    </div>
</template>