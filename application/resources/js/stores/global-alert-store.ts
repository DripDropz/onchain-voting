import {defineStore} from 'pinia';
import {computed, ref, Ref} from 'vue';
import Alert from '@/models/alert';

export const useGlobalAlert = defineStore('global-alert', () => {
    let alerts: Ref<Alert[]> = ref([]);

    function showAlert(alertModel: Alert) {
        alerts.value = [...alerts.value,{...alertModel}];
        setTimeout(() => {
            alerts.value = [];
        }, 5000);
    }

    function closeAlert(index:number) {
        alerts.value.length==1 ? alerts.value = [] : alerts.value.splice(index, 1);
    }

    return {
        showAlert,
        closeAlert,
        alerts
    }
});

