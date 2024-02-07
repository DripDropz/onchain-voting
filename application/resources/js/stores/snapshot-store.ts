import {defineStore} from 'pinia';
import { ref, watch} from 'vue';
import SnapshotService from '@/Pages/Auth/Snapshot/Services/SnapshotService';
import VotingPowerData = App.DataTransferObjects.VotingPowerData;
import Pagination from "@/types/pagination";
import votingPowerQuery from "@/types/voting-power-query";
import SnapshotData = App.DataTransferObjects.SnapshotData;
import DataQuery from '@/types/data-query';



export const useSnapshotStore = defineStore('snapshot-store', () => {
    let snapshotHash = ref('');
    let votingPowersData = ref<VotingPowerData[]>([]);
    let votingPowersPagination = ref<Pagination>();
    let queryData = ref<votingPowerQuery|null>({p:1, l:40, st:''})
    let snapshots = ref<SnapshotData[]>([]);
    let snapshotsPagination = ref<Pagination>();
    let snapshotsQueryData = ref<DataQuery | null>({ p: 1, l: 10 })

    async function loadVotingPowers(snapHash: string) {
        snapshotHash.value = snapHash;
        getVotingPower(snapshotHash.value);
    }

    watch(queryData, () => {
        getVotingPower(snapshotHash.value, queryData.value);
    })

    async function getVotingPower(snapshotHash:string, query?: (votingPowerQuery|null)) {
        await SnapshotService.getSnapshotVotingPowers(snapshotHash, query)
        .then((paginatedResponse) => {
            votingPowersData.value = paginatedResponse.data;
            votingPowersPagination.value = paginatedResponse.meta;
        });
    }

    async function loadSnaphots(query?: (DataQuery | null)) {
        await SnapshotService.getSnapshots( query)
            .then((paginatedResponse) => {
                snapshots.value = paginatedResponse.data;
                snapshotsPagination.value = paginatedResponse.meta;
            });
    }

    watch(snapshotsQueryData, () => {
        loadSnaphots(snapshotsQueryData.value);
    })

    return {
        snapshotHash,
        queryData,
        votingPowersData,
        votingPowersPagination,
        loadVotingPowers,
        snapshots ,
        snapshotsPagination,
        loadSnaphots,
        snapshotsQueryData

    }
});
