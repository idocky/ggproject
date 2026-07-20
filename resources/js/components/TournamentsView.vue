<script setup>
import { usePaginatedFetch } from '../composables/useApi';
import Pagination from './Pagination.vue';

const {
    items: tournaments,
    page,
    lastPage,
    total,
    loading,
    error,
    reload,
    goToPage,
} = usePaginatedFetch('/api/tournaments');
</script>

<template>
    <div>
        <div v-if="loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="n in 6"
                :key="n"
                class="h-20 animate-pulse rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-black/5 dark:bg-neutral-900 dark:ring-white/10"
            ></div>
        </div>

        <div
            v-else-if="error"
            class="flex flex-col items-start gap-3 rounded-2xl bg-red-50 p-5 text-sm text-red-700 ring-1 ring-red-600/10 dark:bg-red-950/30 dark:text-red-300"
        >
            <p>Ошибка загрузки турниров: {{ error }}</p>
            <button
                type="button"
                class="rounded-lg bg-red-600 px-3 py-1.5 text-white transition-transform active:scale-[0.96]"
                @click="reload"
            >
                Повторить
            </button>
        </div>

        <div
            v-else-if="tournaments.length === 0"
            class="rounded-2xl bg-white p-8 text-center text-sm text-neutral-500 shadow-sm ring-1 ring-black/5 dark:bg-neutral-900 dark:text-neutral-400 dark:ring-white/10"
        >
            Турниры не найдены.
        </div>

        <template v-else>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="tournament in tournaments"
                    :key="tournament.id"
                    class="flex items-center justify-between gap-4 rounded-2xl bg-white p-5 shadow-sm shadow-black/5 ring-1 ring-black/5 transition-shadow hover:shadow-md dark:bg-neutral-900 dark:ring-white/10"
                >
                    <p class="truncate font-medium text-neutral-900 dark:text-neutral-100">{{ tournament.name }}</p>
                    <span
                        class="shrink-0 rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium tabular-nums text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                    >
                        {{ tournament.matches_count }} матчей
                    </span>
                </div>
            </div>

            <Pagination :page="page" :last-page="lastPage" :total="total" @change="goToPage" />
        </template>
    </div>
</template>
