<script setup>
import { RouterView } from 'vue-router';
</script>

<template>
    <div class="min-h-screen bg-neutral-50 text-neutral-900 antialiased dark:bg-neutral-950 dark:text-neutral-100">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <RouterLink to="/" class="mb-6 inline-block text-2xl font-semibold text-balance">GG Project</RouterLink>

            <RouterView v-slot="{ Component, route }">
                <Transition name="page" mode="out-in">
                    <KeepAlive include="HomeView">
                        <component :is="Component" :key="route.path" />
                    </KeepAlive>
                </Transition>
            </RouterView>
        </div>
    </div>
</template>

<style>
.page-enter-active {
    transition:
        opacity 0.2s cubic-bezier(0.2, 0, 0, 1),
        transform 0.2s cubic-bezier(0.2, 0, 0, 1);
}

.page-leave-active {
    transition:
        opacity 0.15s cubic-bezier(0.4, 0, 1, 1),
        transform 0.15s cubic-bezier(0.4, 0, 1, 1);
}

.page-enter-from {
    opacity: 0;
    transform: translateY(4px);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
