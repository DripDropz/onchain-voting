import AdminService from "@/shared/Services/AdminService";

export default class BallotService {
    public static async getBallotTypes(): Promise<string[]> {
        return AdminService.getEnums('ballot-type');
    }

    public static async getBallotStatuses(): Promise<string[]> {
        return AdminService.getEnums('model-status');
    }
}
