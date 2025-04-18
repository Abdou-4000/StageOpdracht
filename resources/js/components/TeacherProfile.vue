<template>
<div class="flex justify-center bg-black/20 pb-28" @click="closeProfile">
  <div v-if="show" class="flex flex-col relative left-[70px]">
      <div class="flex clip-path-custom rounded-3xl bg-gray-middle"
           @click.stop>
        
            <!-- Main Info Items Container -->
            <div class="w-2/3 flex flex-col items-center m-6">
              <!-- Company Name -->
              <div class="flex justify-center bg-accentBlue text-white w-3/5 m-3 ml-[125px] p-2 rounded-3xl">
                {{ normalizedTeacher.name || 'N/A' }}
              </div>

              <!-- Teacher Name -->
              <div class="flex justify-center bg-accentPurple text-white w-3/5 m-3 ml-[-200px] p-2 rounded-3xl">
                {{ normalizedTeacher.compname || 'N/A' }}
              </div>

              <!-- Address -->
              <div class="flex justify-center bg-accentPink text-white w-3/5 m-3 ml-[200px] p-2 rounded-3xl">
                {{ normalizedTeacher.details?.location || 'N/A' }}
              </div>

              <!-- Address -->
              <div class="flex justify-center bg-accentYellow text-white w-3/5 m-3 ml-[-75px] p-2 rounded-3xl">
                {{ normalizedTeacher.details?.syntramail || 'N/A' }}
              </div>

              <!-- <div v-for="item in [normalizedTeacher.details?.email || 'Email', normalizedTeacher.details?.phone || 'Phone', getCategoryDisplay || 'Category']" 
              class="flex bg-red justify-center w-full m-1 p-2 rounded-3xl">
                {{ item }}
              </div> -->
            </div>

            <!-- Agenda List -->
            <div class="hidden xl:block w-2/5 h-1/2 m-6 mr-12">
              <AgendaList :id="teacher.id"/>
              <!-- City -->
              <div class="flex flex-col p-2 rounded-3xl">
                <div v-for="(item, index) in normalizedTeacher.category" 
                  :key="index" 
                  :style="{ backgroundColor: item.color }"
                  class="flex justify-center text-white m-1 p-1 rounded-3xl">
                  {{ item.name || 'N/A' }}
                </div>
              </div>
            </div>
      </div>

      <!-- Side box -->
      <div class="flex absolute top-[310px] left-[-181px]">
        <div class="flex flex-col w-full bg-darkred items-center w-[550px] h-[220px] m-4 p-1 pl-2 pr-2 rounded-3xl" @click.stop>
          <!-- review -->
          <!-- <div class="flex bg-red justify-center w-full m-1 p-2 rounded-3xl">
              <div v-for="item in [ 'Rating']" class="">
                {{ item }}
              </div>
          </div> -->

          <ReviewDisplay v-for="review in recentReviews" 
                           :key="review.id" 
                           :review="review"/>

          <div class="w-full">
            <!-- Recent Reviews -->
            <button 
              @click="showReviewModal = true"
              class="">
              Give Review
            </button>
            <div class="">
              <ReviewDisplay v-for="review in recentReviews" 
                           :key="review.id" 
                           :review="review" />
            </div>
          </div>
        </div>
      </div>

      <!-- Review Modal -->
      <div v-if="showReviewModal" 
           class="fixed inset-0 bg-black/50 flex justify-center items-center z-[1001]" 
           @click="showReviewModal = false">
        <div class="bg-white p-6 rounded-[35px] w-[90%] max-w-[500px]" @click.stop>
          <GiveReviews 
            ref="reviewComponent" 
            :teacher-id="teacher.id" 
            @review-saved="onReviewSaved"
          />
        </div>
      </div>

      <!-- Logo box -->
      <!-- <div class="flex absolute w-1/5 top-[75px] left-[1030px]">
        <div class="flex justify-center bg-white border border-red border-4 m-3 p-1 rounded-3xl">
          <img src="../../../public/assets/Logo.png" alt="Syntra Logo">
        </div>
      </div> -->
    </div>
  </div>
</template>

<script>
import GiveReviews from './GiveReviews.vue'
import ReviewDisplay from './ReviewDisplay.vue'
import axios from 'axios'

export default {
  name: 'TeacherProfile',
  components: {
    GiveReviews,
    ReviewDisplay
  },
  props: {
    show: Boolean,
    teacher: Object,
    distance: [Number, String]
  },
  data() {
    return {
      showReviewModal: false,
      recentReviews: []
    }
  },
  computed: {
    getCategoryDisplay() {
      return Array.isArray(this.teacher.category) 
        ? this.teacher.category.join(', ') 
        : 'Uncategorized';
    },
    normalizedTeacher() {
      if (!this.teacher) return {}; // Early return if null

      if (this.teacher.details) {
        // Already normalized
        return this.teacher;
      }

      return {
        name: `${this.teacher.firstname} ${this.teacher.lastname}`,
        compname: this.teacher.companyname,
        category: this.teacher.category ?? [],
        lat: this.teacher.lat,
        lng: this.teacher.lng,
        details: {
          location: `${this.teacher.street} ${this.teacher.streetnumber}`,
          email: this.teacher.email,
          phone: this.teacher.phone,
          hours: 'Contact for availability'
        }
      };
    }
  },
  methods: {
    closeProfile() {
      this.$emit('close');
    },
    async fetchReviews() {
      try {
        const response = await axios.get(`/teachers/${this.teacher.id}/reviews`);
        this.recentReviews = response.data.slice(0, 2); // Get only 2 most recent reviews
      } catch (error) {
        console.error('Failed to fetch reviews:', error);
      }
    },
    onReviewSaved() {
      this.showReviewModal = false;
      this.fetchReviews(); // Refresh reviews after new review is saved
    }
  },
  watch: {
    teacher: {
      immediate: true,
      handler(newTeacher) {
        if (newTeacher?.id) {
          this.fetchReviews();
        }
      }
    }
  }
}
</script>

<style>
.clip-path-custom {
  clip-path: polygon(0% 7.725%,0% 7.725%,0.056% 6.472%,0.22% 5.283%,0.481% 4.175%,0.832% 3.163%,1.262% 2.263%,1.765% 1.49%,2.329% 0.862%,2.948% 0.394%,3.611% 0.101%,4.31% 0%,95.69% 0%,95.69% 0%,96.389% 0.101%,97.052% 0.394%,97.671% 0.862%,98.235% 1.49%,98.738% 2.263%,99.168% 3.163%,99.519% 4.175%,99.78% 5.283%,99.944% 6.472%,100% 7.725%,100% 92.275%,100% 92.275%,99.944% 93.528%,99.78% 94.717%,99.519% 95.825%,99.168% 96.837%,98.738% 97.738%,98.235% 98.51%,97.671% 99.138%,97.052% 99.606%,96.389% 99.899%,95.69% 100%,50% 100%,40.596% 100%,40.596% 100%,39.896% 99.899%,39.233% 99.606%,38.615% 99.138%,38.05% 98.51%,37.548% 97.738%,37.117% 96.837%,36.766% 95.825%,36.505% 94.717%,36.342% 93.528%,36.285% 92.275%,36.285% 70.435%,36.285% 70.435%,36.229% 69.182%,36.066% 67.994%,35.804% 66.885%,35.454% 65.873%,35.023% 64.973%,34.521% 64.201%,33.956% 63.573%,33.337% 63.104%,32.674% 62.812%,31.975% 62.711%,4.31% 62.711%,4.31% 62.711%,3.611% 62.61%,2.948% 62.317%,2.329% 61.848%,1.765% 61.22%,1.262% 60.448%,0.832% 59.548%,0.481% 58.536%,0.22% 57.428%,0.056% 56.239%,0% 54.986%,0% 7.725%);
  height: 500px;
  width: 1100px;
}

@media (max-width: 1280px) {
  .clip-path-custom {
    clip-path: none;
    height: 500px;
    width: 80%;
  }
}
.stars-only textarea, .stars-only button {
  display: none;
}

.textarea-only .group {
  display: none;
}
</style>
