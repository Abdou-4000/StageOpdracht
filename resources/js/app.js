import { createApp } from 'vue'
import TeacherMap from './components/TeacherMap.vue'

// Add error handler for debugging
window.onerror = function(msg, url, line) {
    console.error('JavaScript error:', msg, 'at', url, ':', line);
};

// Wait for everything to be ready
window.addEventListener('load', () => {
    const app = createApp({})
    app.component('teacher-map', TeacherMap)
    app.mount('#app')
})