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

    public static async fetchPetition(hash: string) {
        try {
            const response = await axios.get(route("petitions.petitionData", {petition: hash}));
            return response.data;
        } catch (e) {
            console.error(e);
        }
    }
}
