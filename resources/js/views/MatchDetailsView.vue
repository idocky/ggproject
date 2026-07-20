<script setup>
import { computed } from 'vue';
import { useFetch } from '../composables/useApi';
import { capitalize, formatDateTime, formatFormat, initials } from '../utils/format';
import TeamRecentMatches from '../components/TeamRecentMatches.vue';

const props = defineProps({
    id: { type: [String, Number], required: true },
});

const { data: match, loading, error, reload } = useFetch(`/api/matches/${props.id}`);
const { data: recentMatches, loading: recentLoading } = useFetch(`/api/matches/${props.id}/recent-matches`);

const seriesScore = computed(() => {
    const maps = match.value?.maps ?? [];

    return {
        teamA: maps.filter((m) => m.winner_team_id === match.value.team_a.id).length,
        teamB: maps.filter((m) => m.winner_team_id === match.value.team_b.id).length,
    };
});
</script>

<template>
    <div>
        <RouterLink
            to="/"
            class="mb-6 inline-flex h-10 items-center gap-1.5 rounded-lg px-2 text-sm font-medium text-neutral-500 transition-[background-color,transform] hover:bg-neutral-100 hover:text-neutral-700 active:scale-[0.96] dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-200"
        >
            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4 shrink-0">
                <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Матчи
        </RouterLink>

        <div v-if="loading" class="flex flex-col gap-4">
            <div class="h-40 animate-pulse rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10"></div>
            <div class="h-64 animate-pulse rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10"></div>
        </div>

        <div
            v-else-if="error"
            class="flex flex-col items-start gap-3 rounded-2xl bg-red-50 p-5 text-sm text-red-700 ring-1 ring-red-600/10 dark:bg-red-950/30 dark:text-red-300"
        >
            <p>Ошибка загрузки матча: {{ error }}</p>
            <button
                type="button"
                class="rounded-lg bg-red-600 px-3 py-1.5 text-white transition-transform active:scale-[0.96]"
                @click="reload"
            >
                Повторить
            </button>
        </div>

        <Transition v-else name="fade-up" appear>
            <div class="flex flex-col gap-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10">
                    <div class="mb-4 flex flex-wrap items-center justify-center gap-2 text-xs text-neutral-500 sm:justify-between dark:text-neutral-400">
                        <span class="truncate font-medium text-neutral-700 dark:text-neutral-300">
                            {{ match.tournament?.name ?? 'Без турнира' }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-neutral-100 px-2.5 py-1 font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                                {{ formatFormat(match.format) }}
                            </span>
                            <span class="tabular-nums">{{ formatDateTime(match.date_time) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 sm:gap-8">
                        <div class="flex min-w-0 flex-1 flex-col items-center gap-3 text-center">
                            <img
                                v-if="match.team_a?.logo"
                                :src="match.team_a.logo"
                                :alt="match.team_a.name"
                                class="h-16 w-16 shrink-0 rounded-full object-cover outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10"
                            />
                            <div
                                v-else
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-base font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                            >
                                {{ initials(match.team_a?.name) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-neutral-900 dark:text-neutral-100">{{ match.team_a?.name ?? 'TBD' }}</p>
                                <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ match.team_a?.country ?? 'Страна не указана' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2 text-2xl font-semibold tabular-nums text-neutral-900 dark:text-neutral-100">
                            <span :class="{ 'text-neutral-300 dark:text-neutral-600': seriesScore.teamB > seriesScore.teamA }">{{ seriesScore.teamA }}</span>
                            <span class="text-base text-neutral-300 dark:text-neutral-600">:</span>
                            <span :class="{ 'text-neutral-300 dark:text-neutral-600': seriesScore.teamA > seriesScore.teamB }">{{ seriesScore.teamB }}</span>
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col items-center gap-3 text-center">
                            <img
                                v-if="match.team_b?.logo"
                                :src="match.team_b.logo"
                                :alt="match.team_b.name"
                                class="h-16 w-16 shrink-0 rounded-full object-cover outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10"
                            />
                            <div
                                v-else
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-base font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                            >
                                {{ initials(match.team_b?.name) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-neutral-900 dark:text-neutral-100">{{ match.team_b?.name ?? 'TBD' }}</p>
                                <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ match.team_b?.country ?? 'Страна не указана' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10">
                    <h2 class="mb-4 text-sm font-medium text-neutral-500 dark:text-neutral-400">Составы команд</h2>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div v-for="team in [match.team_a, match.team_b]" :key="team?.id ?? team?.name" class="min-w-0">
                            <p class="mb-2 truncate text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ team?.name ?? 'TBD' }}</p>

                            <p v-if="!team?.players?.length" class="rounded-xl bg-neutral-50 px-4 py-3 text-sm text-neutral-400 dark:bg-neutral-800/60 dark:text-neutral-500">
                                Состав недоступен
                            </p>

                            <div v-else class="flex flex-col gap-1.5">
                                <div
                                    v-for="player in team.players"
                                    :key="player.id"
                                    class="flex items-center gap-3 rounded-xl bg-neutral-50 px-4 py-2.5 dark:bg-neutral-800/60"
                                >
                                    <div
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                                    >
                                        {{ initials(player.nickname) }}
                                    </div>
                                    <span class="truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ player.nickname }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10">
                    <h2 class="mb-4 text-sm font-medium text-neutral-500 dark:text-neutral-400">Карты</h2>

                    <p v-if="!match.maps?.length" class="py-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        Результаты карт пока недоступны.
                    </p>

                    <TransitionGroup v-else name="stagger" tag="div" appear class="flex flex-col gap-2">
                        <div
                            v-for="(gameMap, index) in match.maps"
                            :key="gameMap.id"
                            :style="{ transitionDelay: `${index * 100}ms` }"
                            class="flex items-center gap-4 rounded-xl bg-neutral-50 px-4 py-3 dark:bg-neutral-800/60"
                        >
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-medium tabular-nums text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                            >
                                {{ gameMap.pick }}
                            </span>

                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ capitalize(gameMap.map) }}
                            </span>

                            <div class="flex shrink-0 items-center gap-2 text-sm tabular-nums">
                                <span
                                    class="font-semibold"
                                    :class="
                                        gameMap.winner_team_id === match.team_a.id
                                            ? 'text-neutral-900 dark:text-neutral-100'
                                            : 'text-neutral-400 dark:text-neutral-500'
                                    "
                                >
                                    {{ gameMap.score?.team_a ?? '–' }}
                                </span>
                                <span class="text-neutral-300 dark:text-neutral-600">:</span>
                                <span
                                    class="font-semibold"
                                    :class="
                                        gameMap.winner_team_id === match.team_b.id
                                            ? 'text-neutral-900 dark:text-neutral-100'
                                            : 'text-neutral-400 dark:text-neutral-500'
                                    "
                                >
                                    {{ gameMap.score?.team_b ?? '–' }}
                                </span>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10">
                    <h2 class="mb-4 text-sm font-medium text-neutral-500 dark:text-neutral-400">Последние матчи</h2>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="min-w-0">
                            <p class="mb-2 truncate text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ match.team_a?.name ?? 'TBD' }}</p>

                            <div v-if="recentLoading" class="flex flex-col gap-1.5">
                                <div v-for="n in 3" :key="n" class="h-11 animate-pulse rounded-xl bg-neutral-50 dark:bg-neutral-800/60"></div>
                            </div>
                            <TeamRecentMatches v-else :team="match.team_a" :matches="recentMatches?.team_a ?? []" />
                        </div>

                        <div class="min-w-0">
                            <p class="mb-2 truncate text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ match.team_b?.name ?? 'TBD' }}</p>

                            <div v-if="recentLoading" class="flex flex-col gap-1.5">
                                <div v-for="n in 3" :key="n" class="h-11 animate-pulse rounded-xl bg-neutral-50 dark:bg-neutral-800/60"></div>
                            </div>
                            <TeamRecentMatches v-else :team="match.team_b" :matches="recentMatches?.team_b ?? []" />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fade-up-enter-active {
    transition:
        opacity 0.25s cubic-bezier(0.2, 0, 0, 1),
        transform 0.25s cubic-bezier(0.2, 0, 0, 1);
}

.fade-up-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

.stagger-enter-active {
    transition:
        opacity 0.2s cubic-bezier(0.2, 0, 0, 1),
        transform 0.2s cubic-bezier(0.2, 0, 0, 1);
}

.stagger-enter-from {
    opacity: 0;
    transform: translateY(4px);
}
</style>
