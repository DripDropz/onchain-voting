import {defineStore} from 'pinia';
import {onMounted, ref, Ref, watch} from 'vue';
import { useStorage } from '@vueuse/core';
import axios from 'axios';

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

function getSystemPreference(): boolean {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export const useConfigStore = defineStore('config', () => {
    // Track if user has manually set a preference
    const hasUserPreference: Ref<boolean> = useStorage('user-theme-preference', false, localStorage);
    
    // Use stored preference if user set one, otherwise use system preference
    const isDarkMode: Ref<boolean> = ref(false);
    
    const config: Ref<Config> = ref<Config>({} as Config);
    let showModal = ref(false);
    let showPublishModal = ref(false);

    function initializeTheme() {
        if (hasUserPreference.value) {
            // User has manually set a preference, use stored value
            const storedValue = localStorage.getItem('dark-mode');
            isDarkMode.value = storedValue !== null ? storedValue === 'true' : getSystemPreference();
        } else {
            // No user preference, use system preference
            isDarkMode.value = getSystemPreference();
        }
        updateHtmlClass();
    }

    function updateHtmlClass() {
        if (typeof document !== 'undefined') {
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }

    function toggleDarkMode() {
        isDarkMode.value = !isDarkMode.value;
        hasUserPreference.value = true; // Mark that user has manually set preference
        localStorage.setItem('dark-mode', String(isDarkMode.value));
        updateHtmlClass();
    }

    // Watch for system preference changes
    function setupSystemPreferenceListener() {
        if (typeof window !== 'undefined') {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', (e) => {
                if (!hasUserPreference.value) {
                    // Only auto-switch if user hasn't manually set a preference
                    isDarkMode.value = e.matches;
                    updateHtmlClass();
                }
            });
        }
    }

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

    function toggleModal() {
        showModal.value = !showModal.value;
    }

    function togglePublishModal() {
        showPublishModal.value = !showPublishModal.value;
    }

    // Initialize on mount
    onMounted(() => {
        loadConfig();
        initializeTheme();
        setupSystemPreferenceListener();
    });

    return {
        config,
        isDarkMode,
        toggleModal,
        togglePublishModal,
        toggleDarkMode,
        showModal,
        showPublishModal,
        initializeTheme,
    }
});


