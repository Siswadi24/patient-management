<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { Edit, Trash } from 'lucide-vue-next';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import { ref, computed } from 'vue';
import TambahCategories from './TambahCategories.vue';
import EditCategories from './EditCategories.vue';

const props = defineProps<{
    categories: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Category Transactions',
        href: '/user/categories',
    },
];

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const currentPage = ref(1);
const itemsPerPage = 10;

const filteredCategories = computed(() => {
    let filtered = props.categories;

    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(category =>
            category.name_category.toLowerCase().includes(searchTerm) ||
            category.description?.toLowerCase().includes(searchTerm)
        );
    }

    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filtered.slice(start, end);
});

const totalPages = computed(() => {
    let filtered = props.categories;

    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(category =>
            category.name_category.toLowerCase().includes(searchTerm) ||
            category.description?.toLowerCase().includes(searchTerm)
        );
    }

    return Math.ceil(filtered.length / itemsPerPage);
});

const getStatusSeverity = (isActive: boolean) => {
    return isActive ? 'success' : 'danger';
};

const getStatusLabel = (isActive: boolean) => {
    return isActive ? 'Active' : 'Inactive';
};
</script>

<template>
    <Head title="Category Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="card rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 shadow">
            <div class="mb-4 flex flex-col gap-2 rounded-t-lg bg-gray-800 p-4 md:flex-row md:justify-between">
                <div>
                    <!-- <Button label="Tambah Kategori" class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" /> -->
                    <TambahCategories />
                </div>
                <div class="w-full md:w-auto">
                    <IconField class="w-full sm:w-64">
                        <InputIcon>
                            <i class="pi pi-search text-gray-400" />
                        </InputIcon>
                        <InputText
                            v-model="filters['global'].value"
                            placeholder="Keyword Search"
                            class="w-full border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-blue-500"
                        />
                    </IconField>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden">
                <div class="space-y-4">
                    <div
                        v-for="category in filteredCategories"
                        :key="category.id"
                        class="bg-gray-700 rounded-lg p-4 border border-gray-600"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="text-gray-100 font-semibold text-lg">{{ category.name_category }}</h3>
                                <p class="text-gray-400 text-sm" v-if="category.description">{{ category.description }}</p>
                                <p class="text-gray-500 text-sm" v-else>No description</p>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                <Tag :value="getStatusLabel(category.is_active)" :severity="getStatusSeverity(category.is_active)" />
                                <EditCategories :category="category" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <p class="text-gray-400 text-xs uppercase tracking-wide">Created At</p>
                                <p class="text-gray-100">{{ new Date(category.created_at).toLocaleDateString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Pagination -->
                <div class="mt-6 flex justify-center">
                    <Button
                        v-if="currentPage > 1"
                        @click="currentPage--"
                        class="mr-2 bg-gray-600 hover:bg-gray-700 text-white border-gray-600"
                        size="small"
                    >
                        Previous
                    </Button>
                    <span class="flex items-center px-4 text-gray-300">
                        Page {{ currentPage }} of {{ totalPages }}
                    </span>
                    <Button
                        v-if="currentPage < totalPages"
                        @click="currentPage++"
                        class="ml-2 bg-gray-600 hover:bg-gray-700 text-white border-gray-600"
                        size="small"
                    >
                        Next
                    </Button>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <DataTable
                    :value="props.categories"
                    :filters="filters"
                    :globalFilterFields="['name_category', 'description']"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    tableStyle="min-width: 50rem"
                    class="dark-table"
                >
                    <Column field="name_category" header="Category Name" sortable style="width: 30%">
                        <template #body="{ data }">
                            <span class="text-gray-100">{{ data.name_category }}</span>
                        </template>
                    </Column>
                    <Column field="description" header="Description" style="width: 40%">
                        <template #body="{ data }">
                            <span class="text-gray-100">{{ data.description || '-' }}</span>
                        </template>
                    </Column>
                    <Column field="is_active" header="Status" style="width: 15%">
                        <template #body="{ data }">
                            <Tag :value="getStatusLabel(data.is_active)" :severity="getStatusSeverity(data.is_active)" />
                        </template>
                    </Column>
                    <Column field="created_at" header="Created At" style="width: 15%">
                        <template #body="{ data }">
                            <span class="text-gray-100">{{ new Date(data.created_at).toLocaleDateString() }}</span>
                        </template>
                    </Column>
                    <Column field="actions" header="Actions" style="width: 15%">
                        <template #body="{ data }">
                            <EditCategories :category="data" />
                            <!-- <Button label="Delete" severity="danger" class="my-3 bg-red-500">
                                <span class="text-white">
                                    <Trash />
                                </span>
                            </Button> -->
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-datatable) {
    background: #1f2937;
    border: 1px solid #374151;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
    background: #374151;
    color: #f3f4f6;
    border-bottom: 1px solid #4b5563;
}

:deep(.p-datatable .p-datatable-tbody > tr) {
    background: #1f2937;
    border-bottom: 1px solid #374151;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
    background: #374151;
}

:deep(.p-paginator) {
    background: #1f2937;
    border-top: 1px solid #374151;
    color: #f3f4f6;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page) {
    background: #374151;
    border: 1px solid #4b5563;
    color: #f3f4f6;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page:hover) {
    background: #4b5563;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
    background: #3b82f6;
    border-color: #3b82f6;
}
</style>
