import { createApp } from 'vue'
import TeacherMap from './components/TeacherMap.vue'

const app = createApp({})
app.component('teacher-map', TeacherMap)
app.mount('#app')