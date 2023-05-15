import Alert from "@/models/alert";

function setAlert(message: string, type: string) {
    let notification = {} as Alert
    notification.message = message;
    notification.type = type;
    notification.show = true;
    return notification;
}

export default setAlert;
