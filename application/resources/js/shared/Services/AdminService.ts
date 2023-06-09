import axios from "axios";
export default class AdminService {
    public static async getEnums(collection: string): Promise<string[]> {
        const response = await axios.get(route('admin.enums', { collection }));
        return response.data;
    }

}
