<template>
  <div v-if="show" class="" @click="closeProfile">
    <div class="flex justify-center ml-[200px]">
        <div class="clip-path-custom bg-gray-middle"
           @click.stop>
          <div class="flex">
        
            <!-- Main Info Items Container -->
            <div class="w-2/3 flex flex-col items-center m-6">
              <!-- Company Name -->
              <div class="flex justify-center bg-accentBlue text-white w-3/5 m-3 ml-[200px] p-2 rounded-3xl">
                {{ normalizedTeacher.compname || 'N/A' }}
              </div>

              <!-- Teacher Name -->
              <div class="flex justify-center bg-accentPurple text-white w-3/5 m-3 ml-[-200px] p-2 rounded-3xl">
                {{ normalizedTeacher.name || 'N/A' }}
              </div>

              <!-- Address -->
              <div class="flex justify-center bg-accentPink text-white w-3/5 m-3 ml-[125px] p-2 rounded-3xl">
                <span class="font-bold">Address:</span>
                {{ normalizedTeacher.details?.location || 'N/A' }}
              </div>

              <!-- City -->
              <div class="flex justify-center bg-accentYellow text-white w-3/5 m-3 ml-[-75px] p-2 rounded-3xl">
                <span class="font-bold">City:</span>
                {{ teacher.city || 'N/A' }}
              </div>
            </div>

            <!-- Agenda List -->
            <div class="hidden xl:block w-2/5 h-1/2 m-6">
              <AgendaList :id="teacher.id"/>
              <!-- review -->
              <div class="flex bg-red m-4 rounded-3xl">
                <div v-for="item in [ 'Rating']" 
                    class="">
                  {{ item }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex w-1/3 m-4 relative top-[-190px] left-[175px]">
      <div class="flex flex-col w-full bg-darkred items-center p-1 rounded-3xl" @click.stop>
        <div v-for="item in [normalizedTeacher.details?.email || 'Email', normalizedTeacher.details?.phone || 'Phone', getCategoryDisplay || 'Category']" 
             class="flex bg-red justify-center w-full m-1 p-2 rounded-3xl">
          {{ item }}
        </div>
      </div>
      </div>

      <div class="">
        <img src="../../../public/assets/Logo.png" alt="Syntra Logo" class="h-[130px] w-[180px] object-contain">
      </div>
    </div>

</template>

<script>
export default {
  name: 'TeacherProfile',
  props: {
    show: Boolean,
    teacher: Object,
    distance: [Number, String]
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
    }
  }
}
</script>

<style>
@media (max-width: 2200px) {
  .clip-path-custom {
    clip-path: polygon(0% 7.725%,0% 7.725%,0.056% 6.472%,0.22% 5.283%,0.481% 4.175%,0.832% 3.163%,1.262% 2.263%,1.765% 1.49%,2.329% 0.862%,2.948% 0.394%,3.611% 0.101%,4.31% 0%,95.69% 0%,95.69% 0%,96.389% 0.101%,97.052% 0.394%,97.671% 0.862%,98.235% 1.49%,98.738% 2.263%,99.168% 3.163%,99.519% 4.175%,99.78% 5.283%,99.944% 6.472%,100% 7.725%,100% 92.275%,100% 92.275%,99.944% 93.528%,99.78% 94.717%,99.519% 95.825%,99.168% 96.837%,98.738% 97.738%,98.235% 98.51%,97.671% 99.138%,97.052% 99.606%,96.389% 99.899%,95.69% 100%,50% 100%,40.596% 100%,40.596% 100%,39.896% 99.899%,39.233% 99.606%,38.615% 99.138%,38.05% 98.51%,37.548% 97.738%,37.117% 96.837%,36.766% 95.825%,36.505% 94.717%,36.342% 93.528%,36.285% 92.275%,36.285% 70.435%,36.285% 70.435%,36.229% 69.182%,36.066% 67.994%,35.804% 66.885%,35.454% 65.873%,35.023% 64.973%,34.521% 64.201%,33.956% 63.573%,33.337% 63.104%,32.674% 62.812%,31.975% 62.711%,4.31% 62.711%,4.31% 62.711%,3.611% 62.61%,2.948% 62.317%,2.329% 61.848%,1.765% 61.22%,1.262% 60.448%,0.832% 59.548%,0.481% 58.536%,0.22% 57.428%,0.056% 56.239%,0% 54.986%,0% 7.725%);
    border-radius: 45px;
    height: 500px;
    width: 1100px;
  }
}
@media (max-width: 1380px) {
  .clip-path-custom {
    clip-path: none;
    height: 500px;
    width: 80%;
  }
}

</style>
