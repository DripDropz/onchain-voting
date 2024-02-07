<template>
    <div class="">
        <ul
            role="list"
            class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8">

            <li v-for="ballot in ballots" :key="ballot.hash">
                <BallotListItem :ballot="ballot"/>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import BallotData = App.DataTransferObjects.BallotData;
import BallotListItem from "@/Pages/Auth/Ballot/Partials/BallotListItem.vue";

defineProps<{
    ballots: BallotData[];
}>();

</script>
