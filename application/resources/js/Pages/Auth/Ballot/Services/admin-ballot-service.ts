import AdminService from "@/shared/Services/AdminService";
import BallotsQuery from "@/types/ballots-query";
import PaginatedResponse from "@/types/paginated-response";
import axios from "axios";
import BallotData = App.DataTransferObjects.BallotData;

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

    public static async getAllBallots(): Promise<BallotData[]> {
        return (await axios.get(route('allBallots'))).data;

    }

    public static async unlinkSnapshot(data: { ballot: string, snapshot: string}){
        try {
            await axios.post(route('admin.ballots.snapshots.unLink', data), {});
        } catch(e: any) {
            return e.response.data.message;
        }
    }

    public static async getBallots(queryData?: (BallotsQuery|null)): Promise<PaginatedResponse<BallotData>> {
        try {
            const queryParams = {
                page: queryData?.p,
                perPage: queryData?.l,
            };

            const response = await axios.get(route('ballotsData'), {
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
