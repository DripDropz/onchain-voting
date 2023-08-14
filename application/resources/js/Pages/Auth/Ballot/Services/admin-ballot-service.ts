import AdminService from "@/shared/Services/AdminService";
import axios from "axios";

export default class AdminBallotService {
    public static async getBallotTypes(): Promise<string[]> {
        return AdminService.getEnums('ballot-type');
    }

    public static async getBallotStatuses(): Promise<string[]> {
        return AdminService.getEnums('model-status');
    }

    public static async linkSnapshot(data: { ballot: string, snapshot: string}): Promise<boolean> {
        await axios.post(route('admin.ballots.snapshots.link', data), {});
        return true;
    }

    public static async unlinkSnapshot(data: { ballot: string, snapshot: string}){
        try {
            await axios.post(route('admin.ballots.snapshots.unLink', data), {});
        } catch(e: any) {
            return e.response.data.message;
        }
    }
}
