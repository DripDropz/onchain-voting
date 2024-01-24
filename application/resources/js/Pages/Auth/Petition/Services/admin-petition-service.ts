import AdminService from "@/shared/Services/AdminService";
import PetitionsQuery from "@/types/petitions-query";
import PaginatedResponse from "@/types/paginated-response";
import axios from "axios";
import PetitionData = App.DataTransferObjects.PetitionData;

export default class AdminPetitionService {
    public static async getPetitions(queryData?: (PetitionsQuery|null)): Promise<PaginatedResponse<PetitionData>> {
        try {
            const queryParams = {
                page: queryData?.p,
                perPage: queryData?.l,
            };

            const response = await axios.get(route('petitionsData'), {
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