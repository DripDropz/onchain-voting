import AdminService from "@/shared/Services/AdminService";

export default class SnapshotService {
    public static async getSnapshotTypes(): Promise<string[]> {
        return AdminService.getEnums('snapshot-type');
    }

    public static async getSnapshotStatuses(): Promise<string[]> {
        return AdminService.getEnums('model-status');
    }
}
