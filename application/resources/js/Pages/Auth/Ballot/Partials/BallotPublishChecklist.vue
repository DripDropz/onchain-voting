<template>
  <div class="flow-root w-1/3 text-white border border-gray-300 rounded-lg dark:border-gray-700">
    <h2 class="p-4 mb-4 text-lg font-medium text-gray-900 border-b border-b-gray-300 dark:border-b-gray-600 dark:text-gray-100">
      Publish Checklist:
    </h2>
    <ul role="list" class="px-4 mt-8">
      <li v-for="(event, eventIdx) in timeline">
        <div class="relative pb-8">
          <span v-if="eventIdx !== timeline.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
            aria-hidden="true" />
          <div class="relative flex space-x-3">
            <div>
              <span class="flex items-center justify-center w-8 h-8 rounded-full ring-2 ring-white" :class="{
                'bg-green-500': event.datetime,
                'bg-blue-500': timeline?.[eventIdx - 1]?.datetime,
                'bg-gray-500': !timeline?.[eventIdx - 1]?.datetime && !event.datetime
              }">
                <HandThumbUpIcon v-if="event.datetime" class="w-5 h-5" aria-hidden="true" />
              </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
              <div>
                <p class="text-sm" :class="[event.datetime ? 'text-black dark:text-white' : 'text-gray-500']">
                  {{
                    event.datetime
                    ? event.target
                    : event.content
                  }}
                </p>
              </div>
              <div class="text-sm text-right whitespace-nowrap"
                :class="[event.datetime ? 'text-black dark:text-white' : 'text-gray-500']">
                <UseTimeAgo v-slot="{ timeAgo }" :time="event.datetime">
                  {{ timeAgo }}
                </UseTimeAgo>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div class="flex flex-col p-4 text-black lg:flex-row">
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-green-500 border" ></div>
        <span class="mr-3 text-xs text-gray-500">- Step complete</span>
      </div>
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-blue-500" />
        <span class="mr-3 text-xs text-gray-500">- Next Step</span>
      </div>
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-gray-500" />
        <span class="mr-3 text-xs text-gray-500">- Uncompleted Step(s)</span>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { HandThumbUpIcon } from "@heroicons/vue/20/solid";
import { UseTimeAgo } from "@vueuse/components";
import BallotData = App.DataTransferObjects.BallotData;
import { ref } from "vue";

const props = defineProps<{
  ballot?: BallotData;
}>();

let ballot = ref(props.ballot);

const timeline = [
  {
    content: "create Ballot",
    target: "Ballot created ",
    datetime: ballot.value?.created_at ?? "",
    show: true,
  },
  {
    content: "Create question",
    target: "question was created",
    datetime: ballot.value?.questions?.[0]?.created_at ?? "",
    show: ballot.value?.questions?.[0]?.created_at,
  },
  {
    content: "Add choices to question",
    target: "choices added",
    datetime: ballot.value?.questions?.[0]?.choices?.[0]?.created_at ?? "",
    show: ballot.value?.questions?.[0]?.choices?.[0]?.created_at,
  },
  {
    content: "Publish Ballot",
    target: "Ballot Published",
    datetime:ballot.value?.status=="published" ? ballot.value?.created_at : '' ?? "",
    show:
      ballot.value?.questions?.[0]?.choices?.[0]?.created_at &&
      ballot.value?.questions?.[0]?.created_at &&
      ballot.value?.created_at,
  },
];
</script>
