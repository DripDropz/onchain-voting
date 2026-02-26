<template>
    <div>
        <div
            class="flex flex-row items-center justify-between border border-l-0 border-r-0 border-t-0 border-b-1 w-full">
            <div>

            </div>
<!--            <div class="ml-auto">-->
<!--                <button-->
<!--                    class="font-semibold py-2 px-8 dark:text-white rounded-lg mb-2 border border-black dark:border-white text-black hover:bg-gray-200 transition duration-300 ease-in-out">-->
<!--                    Settings-->
<!--                </button>-->
<!--            </div>-->
        </div>

        <div>
            <PollListItem v-for="poll in polls" :key="poll.hash" :poll="poll"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import PollData = App.DataTransferObjects.PollData;
import PollListItem from "@/Pages/Auth/Poll/Partials/PollListItem.vue";

const props = defineProps<{
    polls: PollData[];
}>();
</script>
