import {defineStore} from 'pinia';
import {onMounted, ref, Ref} from 'vue';
import { useStorage } from '@vueuse/core';
import axios from 'axios';

export interface Config {
    hosted_by_link: boolean;
    explorer?: string;
    logo: string;
    powered_by: string;
    show_created_by: boolean;
}
export interface ConfigStore {
    isDarkMode?: Boolean;
    config: Config;
}

export const useConfigStore = defineStore('config', () => {
    const isDarkMode: Ref<Boolean> = useStorage('dark-mode', true, localStorage, {mergeDefaults: true});
    const config: Ref<Config> = ref<Config>({} as Config);

    async function loadConfig() {
        const configData = (await axios.get(route('config.app')))?.data;
        config.value = {
            ...config.value,
            ...configData,
            explorer:  'https://preview.cardanoscan.io/transaction',
        };
    }

    function toggleDarkMode() {
        isDarkMode.value = !isDarkMode.value;
    }

    onMounted(loadConfig);

    return {
        config,
        isDarkMode,
        toggleDarkMode,
    }
});


