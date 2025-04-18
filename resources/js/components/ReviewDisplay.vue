<template>
  <div class="bg-white/90 rounded-[25px] pt-2 ml-2 pr-5 pl-5 pb-2 mb-[10px] border-[1px] border-[grey] shadow-xl mb-4">
    <div class="flex justify-between items-start ">
      <div class="flex space-x-1">
        <svg v-for="n in 5" :key="n" xmlns="http://www.w3.org/2000/svg" 
             :class="n <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
             class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
        </svg>
      </div>
      <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
    </div>
    <p v-if="review.review" class="text-gray-700 text-sm">{{ review.review }}</p>
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
