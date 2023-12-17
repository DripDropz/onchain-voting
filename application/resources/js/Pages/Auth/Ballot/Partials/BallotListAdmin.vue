<template>
    <ul role="list" class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8">
        <li v-for="ballot in ballots.data" :key="ballot.hash"
            class="overflow-hidden border border-gray-200 rounded-xl dark:border-gray-700">
            <div class="flex items-center p-6 border-b gap-x-4 border-gray-900/5 bg-gray-50 dark:bg-gray-800">
                <div class="flex gap-3 flex-fow">
                    <div class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                        {{ ballot.title }}
                    </div>
                    <BallotStatusBadge :ballot="ballot"></BallotStatusBadge>
                </div>
                <Menu as="div" class="relative ml-auto">
                    <MenuButton class="-m-2.5 block p-2.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Open options</span>
                        <EllipsisHorizontalIcon class="w-5 h-5" aria-hidden="true" />
                    </MenuButton>
                    <transition enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                        <MenuItems
                            class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-sky-100 dark:bg-gray-700 py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                            <MenuItem v-slot="{ active }">
                            <Link :href="route('admin.ballots.view', ballot.hash)
                                " :class="[
        active
            ? 'bg-gray-50 dark:bg-gray-900'
            : '',
        'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300',
    ]">View<span class="sr-only">, {{ ballot.title }}</span>
                            </Link>
                            </MenuItem>
                            <MenuItem v-if="!ballot.live" v-slot="{ active }">
                            <Link :href="route('admin.ballots.edit', ballot.hash)
                                " :class="[
        active
            ? 'bg-gray-50 dark:bg-gray-900'
            : '',
        'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300',
    ]">
                            Edit<span class="sr-only">, {{ ballot.title }}</span>
                            </Link>
                            </MenuItem>
                            <MenuItem v-if="ballot.status !== 'published'" v-slot="{ active }">
                            <a @click.prevent="publishBallot(ballot.hash)" :class="[
                                active
                                    ? 'bg-gray-50 dark:bg-gray-900 cursor-pointer'
                                    : '',
                                'block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-300',
                            ]">
                                Publish<span class="sr-only">, {{ ballot?.title }}</span>
                            </a>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <dl
                class="px-6 py-4 -my-3 text-sm leading-6 bg-gray-100 divide-y divide-gray-100 dark:divide-gray-700 dark:bg-gray-900">
                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">
                        Ballot Opens
                    </dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <time datetime="2020-01-07">{{
                            ballot.started_at
                        }}</time>
                    </dd>
                </div>
                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">
                        Ballot Close
                    </dt>
                    <dd class="text-gray-700 dark:text-gray-100">
                        <time datetime="2020-01-07">{{ ballot.ended_at }}</time>
                    </dd>
                </div>
                <div class="flex justify-between py-3 gap-x-4">
                    <dt class="text-gray-600 dark:text-gray-200">
                        Total Votes
                    </dt>
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 dark:text-gray-200">
                            {{
                                ballot.totalVotes
                                ? humanNumber(ballot.totalVotes, 4)
                                : 0
                            }}
                        </div>
                    </dd>
                </div>
            </dl>
        </li>

        <li
            class="py-16 overflow-hidden border border-gray-400 border-dashed rounded-xl dark:border-gray-700 hover:border-sky-600">
            <Link as="button" :href="route('admin.ballots.create')"
                class="flex flex-col items-center justify-center w-full h-full gap-2 px-6 py-4 leading-6 text-gray-500 text-md xl:text-xl dark:text-gray-400">
            <PlusIcon class="w-6 h-6" />
            <span>Create Ballot</span>
            </Link>
        </li>
    </ul>
    <slot name="footer">
        <div class="flex flex-row items-center justify-between w-full pt-4">
            <div class="border-2 border-sky-600">
                <p class="p-4 text-sm text-gray-900 dark:text-gray-300">
                    {{ `Showing ${props.ballots.meta.from} to ${(props.ballots.meta.to < props.ballots.meta.total) ?
                        props.ballots.meta.to : props.ballots.meta.total} of ${props.ballots.meta.total} results` }} </p>
            </div>
            <Paginator :pagination="ballots.meta" @paginated="(payload: number) => emit('curr-page', payload)"
                @perPageUpdated="(payload: number) => emit('per-page', payload)">
            </Paginator>
        </div>
    </slot>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import humanNumber from "@/utils/human-number";
import { PlusIcon, EllipsisHorizontalIcon } from "@heroicons/vue/20/solid";
import BallotData = App.DataTransferObjects.BallotData;
import { Link, router, useForm } from "@inertiajs/vue3";
import BallotStatusBadge from "@/Pages/Auth/Ballot/Partials/BallotStatusBadge.vue";
import AlertService from "@/shared/Services/alert-service";
import Paginator from "@/shared/components/Paginator.vue";
import Pagination from "@/types/pagination";



const props = defineProps<{
    ballots: {
        data: BallotData[];
        links: [];
        meta: Pagination;

    };
}>();

const emit = defineEmits<{
    (e: 'curr-page', page: number): void
    (e: 'per-page', perPage: number): void
}>();


const statuses = {
    Published: "text-green-700 bg-green-50 ring-green-600/20",
    Draft: "text-gray-600 bg-gray-50 ring-gray-500/10",
    Overdue: "text-red-700 bg-red-50 ring-red-600/10",
};

const form = useForm({
    status: "published",
});

let publishBallot = (ballotHash: string) =>
    form.patch(route("admin.ballots.update", { ballot: ballotHash }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            AlertService.show(["Ballot published"], "success");
        },
        onError: (errors) => {
            AlertService.show(
                Object.entries(errors).map(([key, value]) => value)
            );
        },
    });
</script>
