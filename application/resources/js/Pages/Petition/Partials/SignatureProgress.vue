<template>
    <div class="space-y-3">
        <!-- Counts row -->
        <div class="flex items-end justify-between">
            <div>
                <p class="text-3xl font-bold text-white tabular-nums leading-none">
                    {{ petition$.signatures_count?.toLocaleString() ?? 0 }}
                </p>
                <p class="text-xs font-medium text-gray-400 mt-0.5 uppercase tracking-wide">Signatures</p>
            </div>
            <div class="text-right">
                <template v-if="allGoalsAchieved$">
                    <p class="text-xl font-bold text-emerald-400 leading-none">100%</p>
                    <p class="text-xs font-medium text-gray-400 mt-0.5 uppercase tracking-wide">All goals met</p>
                </template>
                <template v-else-if="hasNextGoal$">
                    <p class="text-xl font-bold text-sky-400 leading-none">{{ nextGoal$?.toLocaleString() }}</p>
                    <p class="text-xs font-medium text-gray-400 mt-0.5 uppercase tracking-wide">Next goal</p>
                </template>
                <template v-else>
                    <p class="text-xl font-bold text-gray-500 leading-none">—</p>
                    <p class="text-xs font-medium text-gray-500 mt-0.5 uppercase tracking-wide">Goal pending</p>
                </template>
            </div>
        </div>

        <!-- Progress track -->
        <div class="relative">
            <div class="h-2.5 w-full rounded-full bg-gray-700/60 overflow-hidden">
                <div
                    v-if="hasNextGoal$ || allGoalsAchieved$"
                    class="h-full rounded-full transition-all duration-700 ease-out"
                    :class="allGoalsAchieved$ ? 'bg-emerald-400' : 'bg-sky-500'"
                    :style="{ width: progressWidth }"
                />
            </div>
        </div>

        <!-- Contextual label -->
        <p v-if="hasNextGoal$ && !allGoalsAchieved$" class="text-xs text-gray-400">
            <span class="font-semibold" :class="urgencyColor">{{ neededSupportesNextGoal$?.toLocaleString() }} more</span>
            needed to reach the next goal
        </p>
        <p v-else-if="allGoalsAchieved$" class="text-xs text-emerald-400 font-medium flex items-center gap-1">
            <CheckCircleIcon class="w-3.5 h-3.5" />
            All signature goals achieved!
        </p>
        <p v-else class="text-xs text-gray-500 italic">
            Admin will set the signature goal during review
        </p>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { CheckCircleIcon } from '@heroicons/vue/20/solid';
import { storeToRefs } from 'pinia';
import { usePetitionSignatureStore } from '@/Pages/Petition/stores/petition-signature-store';

const petitionSignatureStore = usePetitionSignatureStore();
const {
    petition$,
    currentGoalPercetage$,
    allGoalsAchieved$,
    nextGoal$,
    hasNextGoal$,
    lackingNextGoal$,
    neededSupportesNextGoal$,
} = storeToRefs(petitionSignatureStore);

const progressWidth = computed(() => {
    if (allGoalsAchieved$.value) return '100%';
    if (hasNextGoal$.value && currentGoalPercetage$.value != null) {
        return `${Math.min(currentGoalPercetage$.value, 100)}%`;
    }
    return '0%';
});

const urgencyColor = computed(() => {
    const pct = currentGoalPercetage$.value ?? 0;
    if (pct >= 80) return 'text-emerald-400';
    if (pct >= 50) return 'text-sky-400';
    return 'text-amber-400';
});
</script>
