import { createApp } from 'vue/dist/vue.esm-bundler'
import TeacherMap from './components/TeacherMap.vue'

const app = createApp({})
app.component('teacher-map', TeacherMap)
app.mount('#app')