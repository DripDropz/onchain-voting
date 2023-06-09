import { defineStore } from "pinia";
import { ref } from "vue";
import SnapshotService from "@/Pages/Auth/Snapshot/Services/SnapshotService";

export const useSnapshotStore = defineStore('snapshots', () => {
    const searchResults = ref([])

    async function search(term:string){
        try{
            searchResults.value = await SnapshotService.getSnapshot(term);
        }catch(e){
            console.error(e)
        }
    }

    return {
        searchResults,
        search
    }
});
