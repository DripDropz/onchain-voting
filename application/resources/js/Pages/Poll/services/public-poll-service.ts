import axios from "axios";

export default class PublicPollService {
    public static async fetchPolls(params) {
        try {
            const response = await axios.get(route('polls.pollsData', params));

            return response.data;
        } catch (e) {
            console.error(e);
        }
    }
}
