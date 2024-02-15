import axios from "axios";

export default class PublicPetitionService {
    public static async fetchPetitions(params) {
        try {
            const response = await axios.get(route("petitions.petitionsData", params));            
            return response.data;
        } catch (e) {
            console.error(e);
        }
    }
}
