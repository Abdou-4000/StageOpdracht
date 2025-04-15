<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex justify-center items-center z-[1000]" @click="closeProfile">
    <div class="relative w-[85%] h-[65%] flex justify-center items-center p-2.5">
      <div class="absolute w-[70%] min-w-[450px] h-full bg-[#22262d] p-5 clip-path-custom
                  lg:rounded-[45px] lg:max-w-[80%] lg:min-w-[350px] lg:w-full lg:h-full lg:p-2.5 lg:clip-path-none
                  xl:min-w-[1000px]" 
           @click.stop>
        <div class="flex">
          <!-- Agenda List -->
          <div class="hidden xl:block w-1/3">
            <AgendaList :id="teacher.id"/>
          </div>

          <!-- Main Info Items Container -->
          <div class="w-2/3 relative flex flex-col space-y-8 mt-5">
            <!-- Company Name -->
            <div class="transform translate-x-[90%] bg-[#71bdba] text-[#22262d] p-3 rounded-[35px] font-bold 
                        text-[clamp(16px,1.5vw,20px)] w-[50%] min-h-[50px] text-center
                        lg:translate-x-[80%] lg:w-[50%]
                        md:translate-x-0 md:w-[90%]">
              {{ normalizedTeacher.compname || 'N/A' }}
            </div>

            <!-- Teacher Name -->
            <div class="transform translate-x-[40%] bg-[#9C91c5] text-[#22262d] p-3 rounded-[35px] 
                        font-medium text-[clamp(14px,1.5vw,18px)] w-[50%] min-h-[50px] text-center
                        lg:translate-x-[30%] lg:w-[50%]
                        md:w-[90%]">
              {{ normalizedTeacher.name || 'N/A' }}
            </div>

            <!-- Address -->
            <div class="transform translate-x-[100%] bg-[#ee7766] text-[#22262d] p-3 rounded-[35px] 
                        font-medium text-[clamp(14px,1.5vw,18px)] w-[50%] min-h-[50px] text-center
                        lg:translate-x-[90%] lg:w-[50%]
                        md:translate-x-0 md:w-[90%]">
              <span class="font-bold">Address:</span>
              {{ normalizedTeacher.details?.location || 'N/A' }}
            </div>

            <!-- City -->
            <div class="transform translate-x-[50%] bg-[#fbba00] text-[#22262d] p-3 rounded-[35px] 
                        font-medium text-[clamp(14px,1.5vw,18px)] w-[50%] min-h-[50px] text-center
                        lg:translate-x-[40%] lg:w-[50%]
                        md:translate-x-0 md:w-[90%]">
              <span class="font-bold">City:</span>
              {{ teacher.city || 'N/A' }}
            </div>
          </div>
        </div>


      <div class="absolute bottom-[20%] left-[3%] bg-[#920000] rounded-[45px] w-[33%] max-w-[650px] h-[35%] p-2.5 flex flex-col gap-[4%] z-[2]
                  2xl:w-[33%] 2xl:max-w-[650px]
                  xl:w-[400px] xl:h-[220px] xl:bottom-[5%] xl:left-[5%]
                  lg:w-[350px]
                  md:w-[300px] md:left-[5%]
                  sm:w-[250px]" @click.stop>
        <div v-for="item in [normalizedTeacher.details?.email || 'Email', normalizedTeacher.details?.phone || 'Phone', getCategoryDisplay || 'Category']" 
             class="bg-[#ff3521] text-white p-4 rounded-[35px] font-bold text-2xl w-full h-1/2 text-center
                    xl:text-xl
                    lg:text-lg
                    md:text-base">
          {{ item }}
        </div>
      </div>

      <div class="absolute top-[15%] right-[8%] bg-white border-[3px] border-[#920000] rounded-[15px] z-[3]
                  xl:hidden">
        <img src="../../../public/assets/Logo.png" alt="Syntra Logo" class="h-[150px] w-[200px] object-contain">
      </div>
    </div>
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
.clip-path-custom {
  clip-path: polygon(0% 7.725%,0% 7.725%,0.056% 6.472%,0.22% 5.283%,0.481% 4.175%,0.832% 3.163%,1.262% 2.263%,1.765% 1.49%,2.329% 0.862%,2.948% 0.394%,3.611% 0.101%,4.31% 0%,95.69% 0%,95.69% 0%,96.389% 0.101%,97.052% 0.394%,97.671% 0.862%,98.235% 1.49%,98.738% 2.263%,99.168% 3.163%,99.519% 4.175%,99.78% 5.283%,99.944% 6.472%,100% 7.725%,100% 92.275%,100% 92.275%,99.944% 93.528%,99.78% 94.717%,99.519% 95.825%,99.168% 96.837%,98.738% 97.738%,98.235% 98.51%,97.671% 99.138%,97.052% 99.606%,96.389% 99.899%,95.69% 100%,50% 100%,40.596% 100%,40.596% 100%,39.896% 99.899%,39.233% 99.606%,38.615% 99.138%,38.05% 98.51%,37.548% 97.738%,37.117% 96.837%,36.766% 95.825%,36.505% 94.717%,36.342% 93.528%,36.285% 92.275%,36.285% 70.435%,36.285% 70.435%,36.229% 69.182%,36.066% 67.994%,35.804% 66.885%,35.454% 65.873%,35.023% 64.973%,34.521% 64.201%,33.956% 63.573%,33.337% 63.104%,32.674% 62.812%,31.975% 62.711%,4.31% 62.711%,4.31% 62.711%,3.611% 62.61%,2.948% 62.317%,2.329% 61.848%,1.765% 61.22%,1.262% 60.448%,0.832% 59.548%,0.481% 58.536%,0.22% 57.428%,0.056% 56.239%,0% 54.986%,0% 7.725%);
}

.clip-path-none {
  clip-path: none;
}
</style>
