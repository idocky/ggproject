<script setup>
import { computed, shallowRef } from 'vue';
import TeamsView from '../components/TeamsView.vue';
import MatchesView from '../components/MatchesView.vue';
import TournamentsView from '../components/TournamentsView.vue';

const tabs = [
    { key: 'teams', label: 'Команды', component: TeamsView },
    { key: 'matches', label: 'Матчи', component: MatchesView },
    { key: 'tournaments', label: 'Турниры', component: TournamentsView },
];

const activeKey = shallowRef('teams');
const activeComponent = computed(() => tabs.find((tab) => tab.key === activeKey.value)?.component);
</script>

<template>
    <div>
        <nav class="mb-6 flex gap-1 rounded-xl bg-neutral-100 p-1 dark:bg-neutral-900">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                class="flex h-10 flex-1 items-center justify-center rounded-lg px-4 text-sm font-medium transition-colors sm:flex-none sm:px-6"
                :class="
                    activeKey === tab.key
                        ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-800 dark:text-neutral-100'
                        : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200'
                "
                @click="activeKey = tab.key"
            >
                {{ tab.label }}
            </button>
        </nav>

        <KeepAlive>
            <component :is="activeComponent" :key="activeKey" />
        </KeepAlive>
    </div>
</template>
