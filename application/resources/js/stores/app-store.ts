import {defineStore} from 'pinia';
import {onMounted, ref} from 'vue';

export const appStore = defineStore('app', () => {
    let isDarkMode = ref<Boolean|null>(null);

    function toggleDarkMode() {
        isDarkMode.value = ! isDarkMode.value;
        localStorage.setItem('dark-mode', isDarkMode.value.toString());
    }

    function setDarkMode() {
        let dark = localStorage.getItem('dark-mode');
        if (dark) {
            isDarkMode.value = dark == 'true' ? true : false;
        } else {
            isDarkMode.value = false;
            localStorage.setItem('dark-mode', isDarkMode.value.toString());
        }
    }

    onMounted(setDarkMode);

    return {
        isDarkMode,
        toggleDarkMode,
    }
});


