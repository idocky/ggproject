<script setup>
defineProps({
    page: { type: Number, required: true },
    lastPage: { type: Number, required: true },
    total: { type: Number, required: true },
});

const emit = defineEmits(['change']);
</script>

<template>
    <div v-if="lastPage > 1" class="mt-6 flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400">
        <p class="tabular-nums">Всего: {{ total }}</p>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-white px-3 font-medium text-neutral-600 shadow-sm ring-1 ring-black/5 transition-transform disabled:cursor-not-allowed disabled:opacity-40 active:scale-[0.96] dark:bg-neutral-900 dark:text-neutral-300 dark:ring-white/10"
                :disabled="page <= 1"
                @click="emit('change', page - 1)"
            >
                Назад
            </button>

            <span class="tabular-nums px-2">{{ page }} / {{ lastPage }}</span>

            <button
                type="button"
                class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-white px-3 font-medium text-neutral-600 shadow-sm ring-1 ring-black/5 transition-transform disabled:cursor-not-allowed disabled:opacity-40 active:scale-[0.96] dark:bg-neutral-900 dark:text-neutral-300 dark:ring-white/10"
                :disabled="page >= lastPage"
                @click="emit('change', page + 1)"
            >
                Вперёд
            </button>
        </div>
    </div>
</template>
