import './bootstrap';
import '../scss/app.scss';
import { createApp, h, DefineComponent, watch } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {createPinia} from "pinia";
import { modal } from "momentum-modal";
import VMdEditor from '@kangc/v-md-editor';
import '@kangc/v-md-editor/lib/style/base-editor.css';
import vuepressTheme from '@kangc/v-md-editor/lib/theme/vuepress.js';
import '@kangc/v-md-editor/lib/theme/style/vuepress.css';

import Prism from 'prismjs';
import 'prismjs/components/prism-json';

VMdEditor.use(vuepressTheme, {
    Prism,
  });

(BigInt.prototype as any).toJSON = function () {
    return this.toString();
};

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        watch(
            pinia.state,
            (state) => {
                sessionStorage.setItem('piniaState', JSON.stringify(state))
            },
            {deep: true}
        );

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(pinia)
            .use(modal, {
                resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
            })
            .use(VMdEditor)
            .mount(el);
    },
    progress: {
        color: '#0EA5E9',
    },
}).then();
