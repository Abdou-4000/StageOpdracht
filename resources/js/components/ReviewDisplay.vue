<template>
  <div class="flex flex-col bg-red justify-center w-full m-1 p-2 rounded-3xl">
    <div class="flex justify-between items-start">
      <div class="flex space-x-1">
        <svg v-for="n in 5" :key="n" xmlns="http://www.w3.org/2000/svg" 
             :class="n <= review.rating ? 'text-accentYellow' : 'text-white'"
             class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
        </svg>
      </div>
      <span class="text-sm text-white">{{ formatDate(review.created_at) }}</span>
    </div>
    <p v-if="review.review" class="text-white text-sm flex flex-wrap">{{ review.review }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  review: {
    type: Object,
    required: true
  }
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};
</script>
