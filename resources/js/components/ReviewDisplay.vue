<template>
  <div class="flex flex-col bg-red justify-center w-full m-1 p-2 rounded-3xl">
    <div class="flex justify-between items-start">
      <div class="flex space-x-1">
        <template v-for="n in 5" :key="n">
          <!-- Full star -->
          <svg v-if="displayRating(n) === 'full'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-400">
            <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
          </svg>
          <!-- Half star -->
          <svg v-else-if="displayRating(n) === 'half'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 text-yellow-400">
            <defs>
              <linearGradient id="half-grad">
                <stop offset="50%" stop-color="currentColor"/>
                <stop offset="50%" stop-color="transparent"/>
              </linearGradient>
            </defs>
            <path fill="url(#half-grad)" d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
            <path fill="none" stroke="currentColor" stroke-width="1" d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
          </svg>
          <!-- Empty star -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-gray-300" stroke="white" stroke-width="1">
            <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
          </svg>
        </template>
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

function displayRating(n) {
  if (props.review.rating >= n) return 'full';
  if (props.review.rating >= n - 0.5) return 'half';
  return 'empty';
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};
</script>
