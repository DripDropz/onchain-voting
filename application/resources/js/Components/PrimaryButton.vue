<template>
    <button
        class="inline-flex items-center justify-center px-4 font-semibold rounded-md shadow-sm gap-x-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 "
        :class="[selectedClass,defaultClass]" >
        <slot/>
    </button>
</template>


<script lang="ts" setup>
import {computed} from 'vue';

const props = withDefaults(defineProps<{
    theme?: string
    defaultClass?:string
}>(), {
    theme: '',
    defaultClass:'py-1'
})


let isPrimary = computed(() => props.theme == 'primary');
let isPrimary2 = computed(() => props.theme == 'primary2');


let selectedClass = computed(()=>{
    if (isPrimary.value) {
        return 'bg-sky-400 text-white hover:bg-sky-500';
    }else if (isPrimary2.value) {
        return 'border-sky-400 text-sky-400 border-2 hover:text-sky-500 hover:border-sky-500';
    } else  {
        return 'dark:hover:text-slate-200 border';
    }
})
</script>
