import { defineStore } from 'pinia';
import { computed, ref, Ref } from 'vue';
import Alert from '@/models/alert';

export const useGlobalAlert = defineStore('global-alert', () => {
    let alert = ref({} as Alert);

    function showAlert(alertModel:Alert){
        alert.value = alertModel;
        setTimeout(() => {
            alert.value.show = false;
        }, 5000);
    }

    function closeAlert(){
        alert.value.show = false;
    }

    return {
        showAlert,
        closeAlert,
        alert,
    }
});

