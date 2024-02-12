import {defineStore} from 'pinia';
import {onMounted, ref, Ref} from 'vue';
import { useStorage } from '@vueuse/core';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

export interface Config {
    hosted_by_link: string;
    hosted_by: string;
    explorer?: string;
    logo: string;
    powered_by: string;
    show_created_by: boolean;
    app_url:string
}
export interface ConfigStore {
    isDarkMode?: Boolean;
    config: Config;
}

export const useConfigStore = defineStore('config', () => {
    const isDarkMode: Ref<Boolean> = useStorage('dark-mode', true, localStorage, {mergeDefaults: true});
    const config: Ref<Config> = ref<Config>({} as Config);
    let showModal = ref(false);
    let user =  usePage().props.auth.user;


    async function loadConfig() {
        const configData = (await axios.get(route('config.app')))?.data;

        config.value = {
            ...config.value,
            ...configData,
            powered_by: configData.powered_by,
            hosted_by: configData.hosted_by,
            hosted_by_link: configData.hosted_by_link,
            show_created_by: configData.show_created_by,
            explorer: 'https://preview.cardanoscan.io/transaction',
        };
    }

    function toggleDarkMode() {
        isDarkMode.value = !isDarkMode.value;
    }

    function toggleModal() {
        showModal.value = !showModal.value;
    }

    onMounted(loadConfig);

    return {
        config,
        isDarkMode,
        toggleModal,
        toggleDarkMode,
        showModal,
        user
    }
});


