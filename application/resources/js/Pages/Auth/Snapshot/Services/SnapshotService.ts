import AdminService from "@/shared/Services/AdminService";
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import axios from "axios";
import PaginatedResponse from "@/types/paginated-response";
import votingPowerQuery from "@/types/voting-power-query";


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

    public static async getSnapshotVotingPowers(snapshotHash: string, queryData?: (votingPowerQuery|null)): Promise<PaginatedResponse<VotingPowerData>> {
        try {
            const queryParams = {
                page: queryData?.p,
                perPage: queryData?.l,
                sort: queryData?.st
            };

            const response = await axios.get(`/admin/snapshots/${snapshotHash}/powers`, {
                params: queryParams,
            });
            
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
