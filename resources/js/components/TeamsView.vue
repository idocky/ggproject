<script setup>
import { usePaginatedFetch } from '../composables/useApi';
import { initials } from '../utils/format';
import Pagination from './Pagination.vue';

const { items: teams, page, lastPage, total, loading, error, reload, goToPage } = usePaginatedFetch('/api/teams');
</script>

<template>
    <div>
        <div v-if="loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="n in 6"
                :key="n"
                class="h-28 animate-pulse rounded-2xl bg-white p-5 shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10"
            ></div>
        </div>

        <div
            v-else-if="error"
            class="flex flex-col items-start gap-3 rounded-2xl bg-red-50 p-5 text-sm text-red-700 ring-1 ring-red-600/10 dark:bg-red-950/30 dark:text-red-300"
        >
            <p>Ошибка загрузки команд: {{ error }}</p>
            <button
                type="button"
                class="rounded-lg bg-red-600 px-3 py-1.5 text-white transition-colors active:scale-[0.96] transition-transform"
                @click="reload"
            >
                Повторить
            </button>
        </div>

        <div
            v-else-if="teams.length === 0"
            class="rounded-2xl bg-white p-8 text-center text-sm text-neutral-500 shadow-sm ring-1 ring-black/5 dark:bg-neutral-900 dark:text-neutral-400 dark:ring-white/10"
        >
            Команды не найдены.
        </div>

        <template v-else>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="team in teams"
                    :key="team.id"
                    class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm shadow-black/5 ring-1 ring-black/5 transition-shadow hover:shadow-md dark:bg-neutral-900 dark:ring-white/10"
                >
                    <img
                        v-if="team.logo"
                        :src="team.logo"
                        :alt="team.name"
                        class="h-14 w-14 shrink-0 rounded-full object-cover outline outline-1 -outline-offset-1 outline-black/10 dark:outline-white/10"
                    />
                    <div
                        v-else
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-sm font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                    >
                        {{ initials(team.name) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-neutral-900 dark:text-neutral-100">{{ team.name }}</p>
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ team.country ?? 'Страна не указана' }}
                        </p>
                    </div>

                    <span
                        v-if="team.ranking"
                        class="shrink-0 rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium tabular-nums text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                    >
                        #{{ team.ranking }}
                    </span>
                </div>
            </div>

            <Pagination :page="page" :last-page="lastPage" :total="total" @change="goToPage" />
        </template>
    </div>
</template>
