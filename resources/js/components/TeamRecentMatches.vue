<script setup>
import { formatDateTime } from '../utils/format';

const props = defineProps({
    team: { type: Object, required: true },
    matches: { type: Array, default: () => [] },
});

function opponent(match) {
    return match.team_a?.id === props.team.id ? match.team_b : match.team_a;
}

function score(match) {
    const maps = match.maps ?? [];
    const isTeamA = match.team_a?.id === props.team.id;

    if ((match.format ?? 1) >= 2) {
        return {
            for: maps.filter((m) => m.winner_team_id === props.team.id).length,
            against: maps.filter((m) => m.winner_team_id && m.winner_team_id !== props.team.id).length,
        };
    }

    const onlyMap = maps[0];

    return {
        for: isTeamA ? (onlyMap?.score?.team_a ?? null) : (onlyMap?.score?.team_b ?? null),
        against: isTeamA ? (onlyMap?.score?.team_b ?? null) : (onlyMap?.score?.team_a ?? null),
    };
}
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <RouterLink
            v-for="match in matches"
            :key="match.id"
            :to="`/matches/${match.id}`"
            class="flex items-center gap-3 rounded-xl bg-neutral-50 px-4 py-2.5 transition-[background-color,transform] hover:bg-neutral-100 active:scale-[0.96] dark:bg-neutral-800/60 dark:hover:bg-neutral-800"
        >
            <span class="min-w-0 flex-1 truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">
                {{ opponent(match)?.name ?? 'TBD' }}
            </span>

            <div class="flex shrink-0 items-center gap-1.5 text-sm font-semibold tabular-nums">
                <span :class="score(match).for > score(match).against ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-400 dark:text-neutral-500'">
                    {{ score(match).for ?? '–' }}
                </span>
                <span class="text-neutral-300 dark:text-neutral-600">:</span>
                <span :class="score(match).against > score(match).for ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-400 dark:text-neutral-500'">
                    {{ score(match).against ?? '–' }}
                </span>
            </div>

            <span class="hidden shrink-0 text-xs tabular-nums text-neutral-400 sm:inline dark:text-neutral-500">
                {{ formatDateTime(match.date_time) }}
            </span>
        </RouterLink>

        <p
            v-if="!matches.length"
            class="rounded-xl bg-neutral-50 px-4 py-3 text-sm text-neutral-400 dark:bg-neutral-800/60 dark:text-neutral-500"
        >
            Нет предыдущих матчей
        </p>
    </div>
</template>
