<template>
  <div class="sticky w-full text-white border border-gray-300 rounded-lg top-6 dark:border-gray-700">
    <h2
        class="p-4 mb-4 text-lg font-medium text-gray-900 border-b border-b-gray-300 dark:border-b-gray-600 dark:text-gray-100">
      Publish Checklist:
    </h2>
    <form @submit.prevent="updateBallotStatus" class="relative">
      <ul role="list" class="px-4 mt-8">
        <li v-for="(event, eventIdx) in timeline">
          <div class="relative pb-8">
                        <span v-if="eventIdx !== timeline.length - 1"
                              class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-sky-600" aria-hidden="true"/>
            <div class="relative flex space-x-3">
              <div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full ring-2 ring-white"
                     :class="{
                                        'bg-green-500': event.stepComplete,
                                        'bg-sky-600': timeline?.[eventIdx - 1]?.stepComplete && !event.stepComplete || !event?.stepComplete && eventIdx == 0,
                                        'bg-gray-500': !timeline?.[eventIdx - 1]?.stepComplete && !event.stepComplete && eventIdx != 0
                                    }">
                  <HandThumbUpIcon v-if="event.stepComplete" class="w-5 h-5" aria-hidden="true"/>
                </div>
              </div>
              <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                <div class="flex gap-4">
                  <p v-if="!event.utility" class="text-sm flex items-center gap-1"
                     :class="[event.stepComplete ? 'text-black dark:text-white' : 'text-gray-500']">
                    {{ event.stepComplete ? event.target : event.content }}
                    <span v-if="event?.totalNumber" class="flex flex-col">
                      ({{ event?.totalNumber }})
                    </span>
                  </p>
                  <p v-else class="text-sm"
                     :class="[event.stepComplete ? 'text-black dark:text-white' : 'text-gray-500']">
                    {{ event.utility }}
                  </p>

                  <div class="text-sm text-right whitespace-nowrap" v-if="event.stepComplete"
                       :class="[event.datetime ? 'text-slate-400 dark:text-gray-500' : 'text-slate-100']">
                    <UseTimeAgo v-slot="{ timeAgo }" :time="toUserTimezone(event.datetime)">
                      {{ timeAgo }}
                    </UseTimeAgo>
                  </div>
                </div>

                <div class="flex gap-3">
                  <div class="" v-if="ballot?.status !== 'published'">
                    <div v-if="event?.content === 'Add Onchain Policy'">
                      <Link as="button" :href="event?.href"
                            :disabled="!timeline?.[eventIdx - 1]?.stepComplete || hasPolicies"
                            class=" w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                            :class="{ 'hover:bg-slate-500 bg-slate-500 cursor-not-allowed': !timeline?.[eventIdx - 1]?.stepComplete || hasPolicies }">
                        {{ event?.btnContent }}
                      </Link>
                    </div>
                    <div v-if="event?.btnContent && event?.content === 'Create question'">
                      <Link as="button" :href="event?.href"
                            class="w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                            :class="{ 'hover:bg-slate-500 bg-slate-500 cursor-not-allowed': !timeline?.[eventIdx - 1]?.stepComplete }">
                        {{ event?.btnContent }}
                      </Link>
                    </div>
                    <div
                        v-if="event?.btnContent && event?.content === 'Add choices to question' && ballot?.questions.length <= 1">
                      <Link as="button" :href="event?.href"
                            class="w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                            :class="{ 'hover:bg-slate-500 bg-slate-500 cursor-not-allowed': !timeline?.[eventIdx - 1]?.stepComplete }">
                        {{ event?.btnContent }}
                      </Link>
                    </div>
                    <div
                        v-if="event?.btnContent && event.content == 'Add choices to question' && ballot?.questions.length > 1">
                      <button @click.prevent="goToQuestions"
                              class="w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                        {{ event?.btnContent }}
                      </button>
                    </div>
                    <div
                        v-if="ballot?.created_at && event?.btnContent && event?.content === 'Link Snapshot' && !ballot?.snapshot">
                      <Link as="button" :href="event?.href"
                            class="w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                        {{ event?.btnContent }}
                      </Link>
                    </div>
                    <div v-if="event.content == 'Publish Ballot'">
                      <button
                          :disabled="!timeline?.[eventIdx - 1]?.stepComplete || ballot.status == 'published' || !walletFunded"
                          class="w-full px-2 text-sm font-semibold text-white bg-sky-600 rounded-md shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600"
                          :class="{ 'hover:bg-slate-500 bg-slate-500 cursor-not-allowed': !timeline?.[eventIdx - 1]?.stepComplete || ballot.status == 'published' || !walletFunded}">
                        Publish Ballot
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </form>
    <div class="flex flex-col p-4 text-black lg:flex-row">
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-green-500 border"></div>
        <span class="mr-3 text-xs text-gray-500">- Step complete</span>
      </div>
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-sky-600"/>
        <span class="mr-3 text-xs text-gray-500">- Next Step</span>
      </div>
      <div class="flex flex-row items-center">
        <div class="w-2 h-2 bg-gray-500"/>
        <span class="mr-3 text-xs text-gray-500">- Uncompleted Step(s)</span>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {HandThumbUpIcon} from "@heroicons/vue/20/solid";
import {UseTimeAgo} from "@vueuse/components";
import BallotData = App.DataTransferObjects.BallotData;
import QuestionData = App.DataTransferObjects.QuestionData;
import {computed, ref} from "vue";
import moment from "moment-timezone";
import {VARIABLES} from "@/models/variables";
import AlertService from '@/shared/Services/alert-service';
import {useForm} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";
import CreateBallotPolicyWallet from "../../Policies/Partials/CreateBallotPolicyWallet.vue";

const props = defineProps<{
  ballot?: BallotData;
  question?: QuestionData;
}>();

const form = useForm({
  hash: props?.ballot?.hash,
  status: props?.ballot?.status ?? 'Select Status',
});


function updateBallotStatus() {
  form.patch(route('admin.ballots.status.update', {ballot: props.ballot?.hash}), {
    preserveScroll: false,
    preserveState: false,
    onSuccess: () => {
      AlertService.show(['Ballot published'], 'success')
    },
    onError: (errors) => {
      AlertService.show(
          Object
              .entries(errors)
              .map(([key, value]) => value)
      );
    },
  });
}

let ballot = ref(props.ballot);
let question = ref(props.question)
let userTimeZone = moment.tz.guess();

let toUserTimezone = (targetTime: any) => {
  return moment.utc(targetTime).tz(userTimeZone).format('YYYY-MM-DD HH:mm:ss');
}

let hasPolicies = computed(() => {
  const hasVotingContext = ballot.value?.policies?.some((obj) => obj?.context === VARIABLES.VOTING);
  const hasRegisteredContext = ballot.value?.policies?.some((obj) => obj?.context === VARIABLES.REGISTRATION);
  return hasVotingContext && hasRegisteredContext
})

let policiesCreatedAt = computed(() => {
  const timestamp1 = moment(ballot?.value?.policies?.[0]?.created_at, 'YYYY-MM-DD HH:mm:ss');
  const timestamp2 = moment(ballot?.value?.policies?.[1]?.created_at, 'YYYY-MM-DD HH:mm:ss');
  let latestTime = null;

  if (timestamp1.isAfter(timestamp2)) {
    return timestamp1.format('YYYY-MM-DD HH:mm:ss');
  } else {
    return timestamp2.format('YYYY-MM-DD HH:mm:ss');
  }
})

let walletFunded = computed(() => {
  if (!props.ballot || !props.ballot.policies) {
    return false;
  }

  const votingPolicy = props.ballot.policies.find(policy => policy.context === 'voting');
  if (!votingPolicy || votingPolicy.wallet_funded === false) {
    return false;
  }

  return true;
})

const timeline = [
  {
    content: "create Ballot",
    target: "Ballot created ",
    datetime: ballot.value?.created_at ?? "",
    stepComplete: ballot.value?.created_at,
  },
  {
    content: "Create question",
    target: "Question was created",
    btnContent: !ballot.value?.questions?.length ? "Add Question" : "Add Another",
    href: ballot.value?.hash ? route('admin.ballots.questions.create', {'ballot': ballot.value?.hash}) : null,
    totalNumber: ballot.value?.questions?.length,
    //utility: ballot.value?.questions?.[0]?.created_at && !ballot.value?.questions?.[0]?.choices?.[0]?.created_at ? "Now add choices to complete step" : null,
    datetime: ballot.value?.questions?.[0]?.created_at ?? "",
    stepComplete: ballot.value?.questions?.[0]?.created_at,
  },
  {
    content: "Add choices to question",
    target: "Choices added",
    btnContent: !ballot.value?.questions?.[0]?.choices.length ? "Add Choice" : "Add Another",
    href: ballot.value?.hash && ballot.value?.questions?.[0]?.hash ? route('admin.ballots.questions.choices.create', {'ballot': props.ballot?.hash, 'question': ballot.value?.questions?.[0]?.hash}) : '',
    totalNumber: ballot.value?.questions.reduce((total, question) => total + (question.choices ? question.choices.length : 0), 0),
    datetime: ballot.value?.questions?.[0]?.choices?.[0]?.created_at ?? "",
    stepComplete: ballot.value?.questions?.[0]?.choices?.[0]?.created_at,
  },
  {
    content: "Add Onchain Policy",
    target: !hasPolicies ? "Policy added" : "Policies added",
    btnContent: !props.ballot?.policies?.[0]?.context ? "Registration Policy" : "Add Voting Policy",
    href: ballot.value?.hash ? route('admin.ballots.policies.create', {'ballot': ballot.value?.hash}) : null,
    // utility: !ballot.value?.questions?.[0]?.created_at || !ballot.value?.questions?.[0]?.choices?.[0]?.created_at ? "Policy added, now add questions and choices" : null,
    datetime: policiesCreatedAt.value ?? "",
    stepComplete: hasPolicies.value && ballot.value?.questions?.[0]?.choices?.[0]?.created_at,
  },
  {
    content: "Fund voting wallet",
    target: "Voting wallet funded",
    utility: !walletFunded.value ? "Add ada to the voting policy wallet" : null,
    datetime: "",
    stepComplete: walletFunded.value,
  },
  {
    content: "Link Snapshot",
    target: "Snapshot linked",
    btnContent: "Link Snapshot",
    href: ballot.value?.hash ? route('admin.ballots.snapshots.link.view', {'ballot': ballot.value.hash}) : null,
    datetime: ballot.value?.snapshot?.created_at ?? "",
    stepComplete: ballot.value?.snapshot?.created_at,
  },
  {
    content: "Publish Ballot",
    target: "Ballot Published",
    datetime: ballot.value?.status == "published" ? ballot.value?.created_at : '' ?? "",
    stepComplete:
        hasPolicies.value &&
        ballot.value?.status == "published" &&
        ballot.value?.questions?.[0]?.choices?.[0]?.created_at,
  },
];

function goToQuestions() {
  const questionsSection = document.getElementById('ballot-questions');
  questionsSection.scrollIntoView({behavior: 'smooth'});
}
</script>
