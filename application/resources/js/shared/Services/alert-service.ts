import { useGlobalAlert } from "@/stores/global-alert-store";
import setAlert from "@/utils/set-alert";
import axios from "axios";

export default class AlertService {
    static show(alerts: string [], type: string = 'info') {
        console.log(alerts);
        const alertStore = useGlobalAlert();
        for (const message of alerts) {
            alertStore.showAlert(setAlert(message, type));
        }
    }
}
