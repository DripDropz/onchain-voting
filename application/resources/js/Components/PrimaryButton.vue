<template>
    <button class="inline-flex items-center px-4 py-1 font-semibold rounded-md shadow-sm gap-x-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 " 
    :class="{
        'bg-sky-400 text-white hover:bg-sky-500':isPrimary,
        'dark:hover:text-slate-200 border' :!isPrimary && !isPrimary2,
        'border-sky-400 text-sky-400 border-2 hover:text-sky-500 hover:border-sky-500':isPrimary2
    }" >
        <slot />
    </button>
</template>


<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    theme:string
}>(), {
    theme:'',
})


let isPrimary = computed(()=> props.theme == 'primary');
let isPrimary2 = computed(() => props.theme == 'primary2');

</script>
