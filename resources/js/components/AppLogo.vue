<script setup lang="ts">
// import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3'

const appName = usePage().props.appName as string
</script>

<template>
    <!-- <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div> -->
    <img src="/assets/logoADA.png" alt="Logo Simku" class="h-20 object-contain" />
    <div class="grid flex-1 text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold tracking-tight">{{ appName }}</span>
        <span class="text-xs text-muted-foreground">Manage Pasien Lebih Rapi & Friendly</span>
    </div>
</template>
