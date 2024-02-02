<template >
    <div class="grid grid-cols-2 gap-8 py-8">
            <div v-for="poll in polls" :key="poll.hash"
            class="relative px-10 py-6 pb-16 border border-gray-800 rounded-lg dark:text-gray-200 dark:border-gray-200">
            <h2 class="mb-4 text-2xl font-extrabold">{{ poll.question?.title }}</h2>
            <div v-for="(choice, index) in poll.question?.choices" :key="index" class="flex mb-3">
                <label class="w-full cursor-pointer">
                    <input type="radio" class="sr-only peer" v-model="selectedOption" />
                    <span
                        class="block w-full p-3 border-2 rounded-lg dark:text-gray-200 hover:shadow peer-checked:border-sky-500">
                        <span class="flex items-center justify-between">
                            <span class="pr-8 font-bold">{{ index + 1 + '. ' + choice?.title }}</span>
                        </span>
                    </span>
                </label>
            </div>
            <div class="absolute bottom-0 items-center py-2">
                <span>{{ '200' }} votes</span>
                <button @click.prevent=""
                    class="px-4 py-2 mb-2 font-semibold text-white rounded-lg bg-sky-500 hover:bg-slate-600 hover:cursor-pointer ml-72">Vote</button>
            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import PollData = App.DataTransferObjects.PollData;

withDefaults(defineProps<{
    polls: PollData[]
}>(), {});

let selectedOption = '';

</script>
