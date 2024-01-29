<template>
    <div class="flex flex-col items-center p-2 overflow-auto min-h-48 max-h-48 justify-items-center">
        <div v-for="(criterion, index) in criteriaRef"
            class="flex flex-row items-center justify-between w-full gap-2 py-1 border-b border-gray-400 border-opacity-40 dark:border-gray-600 ">
            <div class="flex flex-col gap-1 text-sm">
                <span class="font-bold">{{ criterion.name }}</span>
                <span class="font-light break-all text-slate-500" v-if="criterion?.assetName && criterion?.hash">
                    {{ criterion?.assetName }}
                </span>
                <span class="w-24 h-3 bg-slate-300 dark:bg-gray-700 animate-pulse" v-if=" !!criterion?.hash && !criterion?.assetName"></span>
            </div>
            <div>
                <label class="relative inline-flex items-center cursor-pointer" :for="`${index}`">
                    <input type="checkbox" :id="`${index}`" :value="criterion.type" class="sr-only peer"
                        @change="(e) => makeRule(e.target.checked, criterion.type, criterion.hash)"
                        :checked="!!criterion.hash">
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-sky-400">
                    </div>
                </label>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import PetitionData = App.DataTransferObjects.PetitionData;
import { Ref, computed, onMounted, onUnmounted, ref, watch } from 'vue';
import PollData = App.DataTransferObjects.PollData;
import axios from 'axios';


const props = withDefaults(defineProps<{
    model?: PetitionData|PollData;
}>(), {
});

let criteria = computed(()=> [
    {
        name: 'NFT',
        'type': 'nft',
        'assetName': null,
        ...(props.model && props.model.rules ? props.model.rules.filter((item) => item.type == 'nft')[0] : {})
    },
    {
        name: 'FT',
        'type': 'ft',
        'assetName': null,
        ...(props.model && props.model.rules ? props.model.rules.filter((item) => item.type == 'ft')[0] : {})
    }
]);

let criteriaRef = ref(criteria.value);

let makeRule = (toggleOn, type, hash) => {
    const data = { type }
    if (toggleOn && props.model && !hash) {
        router.get(route('petitions.rules.create', { petition: props.model.hash }), data)
    } else if (!toggleOn && props.model && hash) {
        router.get(route('petitions.rules.removeRule', { petition: props.model.hash, rule: hash }))
    }
}


let getAssetData = async () => {
    for (let i = 0; i < criteriaRef.value.length; i++) {
        const element = criteriaRef.value[i];
        if (element?.value2) {
                 let res = (await axios.get(route('frost.asset', { policy: element.value2 }))).data;
            const assetData = res.asset;
            criteriaRef.value[i].assetName = assetData?.metadata?.name ?? assetData?.onchain_metadata?.name ?? assetData?.name ?? assetData?.fingerprint   
        }
    }
}

if (props.model?.rules.length) {
    getAssetData();
}

watch(()=> criteria.value,()=>{
    criteriaRef.value = criteria.value;
    if (props.model?.rules.length) {
        getAssetData();
    }
},{deep:true})

</script>