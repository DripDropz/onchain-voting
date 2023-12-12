<template>
    <div class="w-full  ">
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-6 mb-6 " v-for="(group,index) in groupedBallots">
            <div class="col-span-1 lg:col-span-3 h-80 lg:h-full bg-sky-500 px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 rounded-lg " v-if="group[0]" 
            :class="{'order-last': (index+1) % 2 == 0}">
                <BallotCard :ballot="group[0]" />
            </div>

            <div class="col-span-1 lg:col-span-4 flex flex-col gap-6" >
                <div class="w-full h-64 md:h-72 bg-sky-500 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 " v-if="group[1]">
                    <BallotCard :ballot="group[1]" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full h-48 bg-sky-500 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 " v-if="group.length > 2">
                        <BallotCard :ballot="group[2]" size="mini" />
                    </div>
                    <div class="w-full h-48 bg-sky-500 rounded-lg px-4 py-6 lg:px-5 lg:py-7 xl:px-8 xl:py-9 " v-if="group.length > 3">
                        <BallotCard :ballot="group[3]" size="mini" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  <script lang="ts" setup>
  import BallotData = App.DataTransferObjects.BallotData;
  import BallotCard from "@/Pages/Ballot/Partials/BallotCard.vue";
  import {computed} from 'vue'
  
  const props = defineProps<{
      ballots: BallotData[];
  }>();
  
  const groupedBallots = computed(() => {
    const groups = [];
    for (let i = 0; i < props.ballots.length; i += 4) {
      groups.push(props.ballots.slice(i, i + 4));
    }
    return groups;
  });
  </script>
  