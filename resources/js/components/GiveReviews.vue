<template>
  <div class="flex flex-col h-full">
    <!-- Star rating in dark box -->
    <div class="bg-[#22262d] text-white p-4 rounded-[35px] font-bold text-2xl w-full h-1/4 text-center flex items-center justify-center">
      <div class="flex items-center justify-center space-x-1 group">
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
    </div>

    <!-- Textarea and button in white area -->
    <div class="flex-1 p-2 flex flex-col space-y-1">
      <textarea
        v-model="review"
        :disabled="isLoading"
        placeholder="Leave a review (optional)..."
        class="w-full p-2 text-gray-600 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none flex-1"
        maxlength="255"
      ></textarea>

      <!-- Error message -->
      <div v-if="error" class="text-red  font-medium">
        {{ error }}
      </div>

      <!-- Success message -->
      <div v-if="successMessage" class="text-green-500 text-sm">
        {{ successMessage }}
      </div>

      <button 
        :disabled="isLoading || !rating"
        :class="{'opacity-50 cursor-not-allowed': isLoading || !rating}"
        class="bg-[#22262d] text-white px-4 py-2 rounded-[50px] max-w-[200px] hover:bg-opacity-90" 
        @click="saveReview"
      >
        <span v-if="isLoading">Saving...</span>
        <span v-else>Save Review</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="js">
import { ref } from 'vue';
import axios from 'axios';

const rating = ref(0);
const hoverRating = ref(0);
const review = ref('');
const maxStars = 5;
const r = ref('');
const isLoading = ref(false);
const error = ref('');
const successMessage = ref('');
const props = defineProps({
teacherId: { 
  type: Number,
  required: true
}
});

const emit = defineEmits(['review-saved']); 

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

async function saveReview() {
  if (!rating.value) {
      error.value = 'Please select a rating';
      return;
  }

  try {
      const response = await axios.post('/reviews', {
          teacher_id: props.teacherId,
          rating: rating.value,
          review: review.value
      });
      
      successMessage.value = 'Review saved successfully!';
      review.value = '';
      rating.value = 0;
      emit('review-saved'); // Emit event when review is saved

  } catch (err) {
      if (err.response?.status === 401) {
          error.value = 'Please login to leave a review';
      } else if (err.response?.status === 422) {
          error.value = err.response.data.message;
      } else {
          error.value = 'Failed to save review';
      }
  }
}
</script>

<style>
.error-shake {
animation: shake 0.5s;
}

@keyframes shake {
0% { transform: translateX(0); }
25% { transform: translateX(5px); }
50% { transform: translateX(-5px); }
75% { transform: translateX(5px); }
100% { transform: translateX(0); }
}
</style>