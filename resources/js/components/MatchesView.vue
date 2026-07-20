<script setup>
import { usePaginatedFetch } from '../composables/useApi';
import { formatDateTime, formatFormat, initials } from '../utils/format';
import Pagination from './Pagination.vue';

const { items: matches, page, lastPage, total, loading, error, reload, goToPage } = usePaginatedFetch('/api/matches');
</script>

<template>
    <div>
        <div v-if="loading" class="flex flex-col gap-3">
            <div
                v-for="n in 5"
                :key="n"
                class="h-20 animate-pulse rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10"
            ></div>
        </div>

        <div
            v-else-if="error"
            class="flex flex-col items-start gap-3 rounded-2xl bg-red-50 p-5 text-sm text-red-700 ring-1 ring-red-600/10 dark:bg-red-950/30 dark:text-red-300"
        >
            <p>Ошибка загрузки матчей: {{ error }}</p>
            <button
                type="button"
                class="rounded-lg bg-red-600 px-3 py-1.5 text-white transition-transform active:scale-[0.96]"
                @click="reload"
            >
                Повторить
            </button>
        </div>

        <div
            v-else-if="matches.length === 0"
            class="rounded-2xl bg-white p-8 text-center text-sm text-neutral-500 shadow-sm ring-1 ring-black/5 dark:bg-neutral-900 dark:text-neutral-400 dark:ring-white/10"
        >
            Матчи не найдены.
        </div>

        <template v-else>
            <div class="flex flex-col gap-3">
                <RouterLink
                    v-for="match in matches"
                    :key="match.id"
                    :to="`/matches/${match.id}`"
                    class="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm shadow-black/5 ring-1 ring-black/5 transition-[box-shadow,transform] hover:shadow-md active:scale-[0.96] sm:flex-row sm:items-center sm:justify-between dark:bg-neutral-900 dark:ring-white/10"
                >
                    <div class="flex flex-1 items-center justify-center gap-3 sm:justify-start">
                        <div class="flex min-w-0 flex-1 items-center gap-2 sm:justify-end">
                            <span class="truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ match.team_a?.name ?? 'TBD' }}
                            </span>
                            <img
                                v-if="match.team_a?.logo"
                                :src="match.team_a.logo"
                                class="h-8 w-8 shrink-0 rounded-full object-cover outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10"
                            />
                            <div
                                v-else
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                            >
                                {{ initials(match.team_a?.name) }}
                            </div>
                        </div>

                        <span class="shrink-0 text-xs font-medium tracking-wide text-neutral-400">VS</span>

                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <img
                                v-if="match.team_b?.logo"
                                :src="match.team_b.logo"
                                class="h-8 w-8 shrink-0 rounded-full object-cover outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10"
                            />
                            <div
                                v-else
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                            >
                                {{ initials(match.team_b?.name) }}
                            </div>
                            <span class="truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ match.team_b?.name ?? 'TBD' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center justify-center gap-3 text-xs text-neutral-500 sm:justify-end dark:text-neutral-400">
                        <span class="truncate">{{ match.tournament?.name ?? 'Без турнира' }}</span>
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            {{ formatFormat(match.format) }}
                        </span>
                        <span class="tabular-nums">{{ formatDateTime(match.date_time) }}</span>
                    </div>
                </RouterLink>
            </div>

            <Pagination :page="page" :last-page="lastPage" :total="total" @change="goToPage" />
        </template>
    </div>
</template>
