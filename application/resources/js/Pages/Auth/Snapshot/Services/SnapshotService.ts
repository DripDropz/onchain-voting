import AdminService from "@/shared/Services/AdminService";
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import axios from "axios";
import PaginatedResponse from "@/types/paginated-response";

export default class SnapshotService {
    public static async getSnapshotTypes(): Promise<string[]> {
        return AdminService.getEnums('snapshot-type');
    }

    public static async getSnapshotStatuses(): Promise<string[]> {
        return AdminService.getEnums('model-status');
    }

    public static async getSnapshot(term:string): Promise<[]> {
        const response = await axios.get(route('searchSnapshot', { term:term }));
        return response.data;
    }

    public static async getSnapshotVotingPowers(snapshotHash: string): Promise<PaginatedResponse<VotingPowerData>> {
        try {
            const response = await axios.get(route('admin.snapshots.powers.list', {snapshot: snapshotHash}));
            return response.data;
        } catch (e) {
            console.error(e);
        }
        return {
            data: [],
            meta: {} as any
        };
    }
}
