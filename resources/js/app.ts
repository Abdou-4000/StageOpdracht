import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import AgendaWeek from './components/AgendaWeek.vue';
import AgendaMonth from './components/AgendaMonth.vue';
import ChatComponent from './components/ChatComponent.vue';
import AgendaList from './components/AgendaList.vue';
import TeacherMap from './components/TeacherMap.vue';
import TeacherProfile from './components/TeacherProfile.vue';
import 'leaflet/dist/leaflet.css';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

(window as any).pusher = Pusher;


// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        
        readonly VITE_PUSHER_APP_KEY: string;
        readonly VITE_PUSHER_APP_CLUSTER: string;

        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        // Register the component globally
        vueApp.component('AgendaWeek', AgendaWeek);
        vueApp.component('AgendaMonth', AgendaMonth);
        vueApp.component('AgendaList', AgendaList);
        vueApp.component('TeacherMap', TeacherMap);
        vueApp.component('TeacherProfile', TeacherProfile);

        vueApp.component('ChatComponent', ChatComponent);
        
        const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
        const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

        if (!pusherKey || !pusherCluster) {
            console.error('Pusher key or cluster is not defined in the environment variables.');
        } else {
            //init laravel echo
            const echoInstance = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true
            });
            (window as any).Echo = echoInstance;
            console.log('Echo init with pusher:', (window as any).Echo);
        }



        vueApp.use(plugin).use(ZiggyVue).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
