import AdminService from "@/shared/Services/AdminService";
import PollsQuery from "@/types/polls-query";
import PaginatedResponse from "@/types/paginated-response";
import axios from "axios";
import PollData = App.DataTransferObjects.PollData;

export default class AdminPollService {
    public static async getPolls(queryData?: (PollsQuery|null)): Promise<PaginatedResponse<PollData>> {
        try {
            const queryParams = {
                page: queryData?.p,
                perPage: queryData?.l,
            };

            const response = await axios.get(route('pollsData'), {
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