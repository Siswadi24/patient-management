<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patient Records',
        href: '/patient',
    },
];
</script>
<template>
     <div>
        <Head title="Patient Records" />

        <AppLayout :breadcrumbs="breadcrumbs">
            Patient Records Page
        </AppLayout>
    </div>
</template>
