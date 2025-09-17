<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                            <!-- <AppLogoIcon class="size-9 fill-current text-[var(--foreground)] dark:text-white" /> -->
                            <div class="mb-8">
                                <div class="flex items-center space-x-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#10B981]">
                                        <span class="text-sm font-bold text-white">R</span>
                                    </div>
                                    <span class="text-xl font-semibold text-gray-900">RS</span>
                                </div>
                            </div>
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
