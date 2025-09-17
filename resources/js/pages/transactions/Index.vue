<template>
    <div>
        <Head title="Transactions" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <!-- Photo Modal -->
            <Dialog
                v-model:visible="showPhotoModal"
                modal
                header="Transaction Photo"
                :style="{ width: '50rem' }"
                class="dark"
                @hide="closePhotoModal"
            >
                <div class="flex justify-center">
                    <img
                        :src="selectedPhoto"
                        alt="Transaction Photo"
                        class="max-w-full max-h-96 object-contain rounded-lg"
                    />
                </div>
            </Dialog>

            <div class="card rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 shadow">
                <div class="mb-4 flex flex-col gap-2 rounded-t-lg bg-gray-800 p-4 md:flex-row md:justify-between">
                    <div>
                        <TambahTransaction :categories="categories" />
                        <!-- <Button label="Tambah Transaksi" class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" /> -->
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
                            v-for="transaction in filteredTransactions"
                            :key="transaction.id"
                            class="bg-gray-700 rounded-lg p-4 border border-gray-600"
                        >
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="text-gray-100 font-semibold text-lg">{{ transaction.name_transaction }}</h3>
                                    <p class="text-gray-400 text-sm">{{ transaction.category_transaction?.name_category || '-' }}</p>
                                </div>
                                <div class="ml-4">
                                    <Button
                                        @click="togglePopover($event, transaction.id)"
                                        icon
                                        severity="secondary"
                                        class="border-gray-600 bg-gray-600 p-2 text-white hover:bg-gray-700"
                                        size="small"
                                    >
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>

                                    <Popover :ref="(el) => (popoverRefs[`popover_${transaction.id}`] = el)">
                                        <div class="flex w-40 flex-col gap-2 p-2">
                                            <Button
                                                @click="handleAction('show', transaction)"
                                                severity="info"
                                                class="justify-start border-blue-600 bg-blue-600 py-2 text-sm text-white hover:bg-blue-700"
                                                size="small"
                                            >
                                                <Eye class="mr-2 h-4 w-4" />
                                                Show
                                            </Button>

                                            <Button
                                                @click="handleAction('edit', transaction)"
                                                severity="warning"
                                                class="justify-start border-yellow-600 bg-yellow-600 py-2 text-sm text-white hover:bg-yellow-700"
                                                size="small"
                                            >
                                                <Edit class="mr-2 h-4 w-4" />
                                                Edit
                                            </Button>

                                            <Button
                                                @click="handleAction('delete', transaction)"
                                                severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small"
                                            >
                                                <Trash class="mr-2 h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </Popover>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">Amount</p>
                                    <p class="text-gray-100 font-semibold">{{ formatCurrency(transaction.amount_transaction) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">Date & Time</p>
                                    <p class="text-gray-100">{{ formatDate(transaction.transaction_date) }}</p>
                                    <p class="text-gray-300 text-sm">{{ formatTime(transaction.transaction_time) }}</p>
                                </div>
                            </div>

                            <div class="mb-3" v-if="transaction.description_transaction">
                                <p class="text-gray-400 text-xs uppercase tracking-wide">Description</p>
                                <p class="text-gray-100 text-sm">{{ transaction.description_transaction }}</p>
                            </div>

                            <div v-if="transaction.photo_transaction" class="flex justify-center">
                                <img
                                    :src="getPhotoUrl(transaction.photo_transaction)"
                                    alt="Transaction Photo"
                                    class="h-16 w-16 cursor-pointer rounded object-cover border border-gray-500 hover:border-gray-300 transition-colors"
                                    @click="openPhotoModal(transaction.photo_transaction)"
                                    @error="$event.target.style.display='none'"
                                />
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
                        :value="props.transactions"
                        :filters="filters"
                        :globalFilterFields="['name_transaction', 'description_transaction', 'categoryTransaction.name_category']"
                        paginator
                        :rows="15"
                        :rowsPerPageOptions="[15, 30, 50, 100]"
                        tableStyle="min-width: 50rem"
                        class="dark-table"
                    >
                        <Column field="category_transaction.name_category" header="Category" sortable style="width: 15%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.category_transaction?.name_category || '-' }}</span>
                            </template>
                        </Column>

                        <Column field="name_transaction" header="Transaction Name" sortable style="width: 20%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.name_transaction }}</span>
                            </template>
                        </Column>

                        <Column field="amount_transaction" header="Amount" sortable style="width: 15%">
                            <template #body="{ data }">
                                <span class="font-semibold text-gray-100">{{ formatCurrency(data.amount_transaction) }}</span>
                            </template>
                        </Column>

                        <Column field="description_transaction" header="Description" style="width: 25%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.description_transaction || '-' }}</span>
                            </template>
                        </Column>

                        <Column field="transaction_date" header="Date" sortable style="width: 10%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ formatDate(data.transaction_date) }}</span>
                            </template>
                        </Column>

                        <Column field="transaction_time" header="Time" sortable style="width: 10%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ formatTime(data.transaction_time) }}</span>
                            </template>
                        </Column>

                        <Column field="photo_transaction" header="Photo" style="width: 5%">
                            <template #body="{ data }">
                                <div v-if="data.photo_transaction" class="flex justify-center">
                                    <img
                                        :src="getPhotoUrl(data.photo_transaction)"
                                        alt="Transaction Photo"
                                        class="h-8 w-8 cursor-pointer rounded object-cover border border-gray-500 hover:border-gray-300 transition-colors"
                                        @click="openPhotoModal(data.photo_transaction)"
                                        @error="$event.target.style.display='none'"
                                    />
                                </div>
                                <span v-else class="text-gray-400">-</span>
                            </template>
                        </Column>

                        <Column field="actions" header="Actions" style="width: 10%">
                            <template #body="{ data }">
                                <div class="flex justify-center">
                                    <Button
                                        @click="togglePopover($event, data.id)"
                                        icon
                                        severity="secondary"
                                        class="border-gray-600 bg-gray-600 p-2 text-white hover:bg-gray-700"
                                    >
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>

                                    <Popover :ref="(el) => (popoverRefs[`popover_${data.id}`] = el)">
                                        <div class="flex w-40 flex-col gap-2 p-2">
                                            <Button
                                                @click="handleAction('show', data)"
                                                severity="info"
                                                class="justify-start border-blue-600 bg-blue-600 py-2 text-sm text-white hover:bg-blue-700"
                                                size="small"
                                            >
                                                <Eye class="mr-2 h-4 w-4" />
                                                Show
                                            </Button>
                                            <EditTransaction :transaction="data" :categories="categories" />
                                            <Button
                                                @click="handleAction('delete', data)"
                                                severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small"
                                            >
                                                <Trash class="mr-2 h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </Popover>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { Edit, Eye, MoreVertical, Trash } from 'lucide-vue-next';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Popover from 'primevue/popover';
import Dialog from 'primevue/dialog';
import { computed, ref } from 'vue';
import TambahTransaction from './TambahTransaction.vue';
import EditTransaction from './EditTransaction.vue';

const props = defineProps<{
    transactions: any[];
    categories: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/user/transactions',
    },
];

const currentPage = ref(1);
const itemsPerPage = 15;
const showPhotoModal = ref(false);
const selectedPhoto = ref('');
const popoverRefs = ref<{ [key: string]: any }>({});
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID');
};

const formatTime = (time: string) => {
    return time.substring(0, 5); // Format HH:MM
};

const togglePopover = (event: Event, transactionId: string) => {
    const popover = popoverRefs.value[`popover_${transactionId}`];
    if (popover) {
        popover.toggle(event);
    }
};

const handleAction = (action: string, transaction: any) => {
    // Close popover after action
    const popover = popoverRefs.value[`popover_${transaction.id}`];
    if (popover) {
        popover.hide();
    }

    // Handle different actions
    switch (action) {
        case 'show':
            console.log('Show transaction:', transaction);
            break;
        case 'edit':
            console.log('Edit transaction:', transaction);
            break;
        case 'delete':
            console.log('Delete transaction:', transaction);
            break;
    }
};

const filteredTransactions = computed(() => {
    let filtered = props.transactions;

    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(transaction =>
            transaction.name_transaction.toLowerCase().includes(searchTerm) ||
            transaction.description_transaction?.toLowerCase().includes(searchTerm) ||
            transaction.category_transaction?.name_category?.toLowerCase().includes(searchTerm)
        );
    }

    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filtered.slice(start, end);
});

const totalPages = computed(() => {
    let filtered = props.transactions;

    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(transaction =>
            transaction.name_transaction.toLowerCase().includes(searchTerm) ||
            transaction.description_transaction?.toLowerCase().includes(searchTerm) ||
            transaction.category_transaction?.name_category?.toLowerCase().includes(searchTerm)
        );
    }

    return Math.ceil(filtered.length / itemsPerPage);
});

function openPhotoModal(photoPath: string) {
    if (photoPath) {
        selectedPhoto.value = `/storage/${photoPath}`;
        showPhotoModal.value = true;
    }
}

function closePhotoModal() {
    showPhotoModal.value = false;
    selectedPhoto.value = '';
}

function getPhotoUrl(photoPath: string | null): string {
    if (!photoPath) return '';
    return `/storage/${photoPath}`;
}
</script>

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

:deep(.p-popover) {
    background: #374151;
    border: 1px solid #4b5563;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}

:deep(.p-popover .p-popover-content) {
    background: #374151;
    color: #f3f4f6;
    padding: 0;
}

:deep(.p-button) {
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
}

:deep(.p-dialog .p-dialog-header) {
    background: #1f2937;
    color: #f3f4f6;
    border-bottom: 1px solid #374151;
}

:deep(.p-dialog .p-dialog-content) {
    background: #1f2937;
    color: #f3f4f6;
}
</style>
