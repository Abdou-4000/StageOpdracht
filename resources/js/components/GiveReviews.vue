<template>
    <div class="space-y-3">
      <!-- Star rating -->
      <div class="flex items-center space-x-1 group">
        <button
          v-for="n in maxStars"
          :key="n"
          class="w-6 h-6 relative"
          @mousemove="onMouseMove($event, n)"
          @mouseleave="hoverRating = 0"
          @click="setRating(n)"
        >
          <!-- Full star -->
          <svg v-if="displayRating(n) === 'full'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-yellow-400 group-hover:opacity-50">
            <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
          </svg>
  
          <!-- Half star -->
          <svg v-else-if="displayRating(n) === 'half'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-full h-full text-yellow-400 group-hover:opacity-50">
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
          <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="w-full h-full text-gray-300 group-hover:fill-opacity-50" stroke="currentColor" stroke-width="2">
            <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.4 8.168L12 18.896l-7.334 3.865 1.4-8.168L.132 9.211l8.2-1.193z"/>
          </svg>
        </button>
      </div>
  
       <!-- Textarea for review with maxlength for varchar(255) -->
        <textarea
        v-model="review"
        placeholder="Leave a review (optional)..."
        class="w-full p-2 text-gray-600 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"
        rows="3"
        maxlength="255"
        ></textarea>

        <button class="text-gray-600" @click="saveReview()">Opslaan</button>
    </div>
</template>
  
<script setup>
import { ref } from 'vue';
import axios from 'axios';
  
const rating = ref(0);
const hoverRating = ref(0);
const review = ref('');
const maxStars = 5;
const r = ref('');
  
function onMouseMove(event, n) {
    const { offsetX, currentTarget } = event
    const isHalf = offsetX < currentTarget.offsetWidth / 2
    hoverRating.value = n - (isHalf ? 0.5 : 0)
}
  
function setRating(n) {
    rating.value = hoverRating.value || n
}
  
function displayRating(n) {
    const value = hoverRating.value || rating.value
    if (value >= n) return 'full'
    if (value >= n - 0.5) return 'half'
    return 'empty'
}

function saveReview() {
    r.value = {
        rating: rating.value,
        review: review.value,
    }

    // Save the events to the database
    axios.post('/reviews', r)
    .then(response => {console.log(response.data);})
    .catch(error => {console.error('Error:', error);});
}
</script>
  
<style></style>