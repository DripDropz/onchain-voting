import {defineStore} from 'pinia';
import {computed, ref, Ref} from 'vue';
import Alert from '@/models/alert';

export const useGlobalAlert = defineStore('global-alert', () => {
    let alert: Ref<Alert | null> = ref(null);

    function showAlert(alertModel: Alert) {
        alert.value = alertModel;
        setTimeout(() => {
            alert.value = null;
        }, 5000);
    }

    function closeAlert() {
        alert.value = null;
    }

    return {
        showAlert,
        closeAlert,
        alert
    }
});

