<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { Edit, Eye, MoreVertical, Trash, Filter } from 'lucide-vue-next';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Popover from 'primevue/popover';
import { computed, ref, watch } from 'vue';
import ShowPatient from './showPatient.vue';
import FilterModal from './component/filterModal.vue';

const props = defineProps<{
    patients: any[];
    pagination: any;
    filters: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patient Records',
        href: '/user/patient',
    },
];

const popoverRefs = ref<{ [key: string]: any }>({});
const showPatientModal = ref(false);
const showFilterModal = ref(false);
const selectedPatient = ref<any>(null);

// DataTable filters for client-side search only
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Server-side filters and search
const searchQuery = ref(props.filters?.search || '');
const selectedGender = ref(props.filters?.gender || '');
const selectedEthnic = ref(props.filters?.ethnic || '');
const selectedEducation = ref(props.filters?.education || '');
const selectedMarriedStatus = ref(props.filters?.married_status || '');
const selectedJob = ref(props.filters?.job || '');
const selectedBloodType = ref(props.filters?.blood_type || '');

// Mobile pagination
const currentPage = ref(1);
const itemsPerPage = 5;

// Server-side pagination info
const serverCurrentPage = computed(() => props.pagination?.current_page || 1);
const serverTotalPages = computed(() => props.pagination?.last_page || 1);
const serverPerPage = computed(() => props.pagination?.per_page || 15);

// Filter options
const genderOptions = [
    { label: 'All', value: '' },
    { label: 'Male', value: 'male' },
    { label: 'Female', value: 'female' }
];

const bloodTypeOptions = [
    { label: 'All', value: '' },
    { label: 'A', value: 'A' },
    { label: 'B', value: 'B' },
    { label: 'AB', value: 'AB' },
    { label: 'O', value: 'O' }
];

const marriedStatusOptions = [
    { label: 'All', value: '' },
    { label: 'Kawin', value: 'Kawin' },
    { label: 'Belum Kawin', value: 'Belum Kawin' },
    { label: 'Cerai Hidup', value: 'Cerai Hidup' },
    { label: 'Cerai Mati', value: 'Cerai Mati' }
];

// Watch for search changes and debounce
let searchTimeout: NodeJS.Timeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Mobile pagination - client-side filtering for display
const filteredPatients = computed(() => {
    let filtered = props.patients;

    if (searchQuery.value) {
        const searchTerm = searchQuery.value.toLowerCase();
        filtered = filtered.filter(patient =>
            patient.first_name.toLowerCase().includes(searchTerm) ||
            patient.last_name.toLowerCase().includes(searchTerm) ||
            patient.rm_number.toLowerCase().includes(searchTerm) ||
            patient.phone_number.toLowerCase().includes(searchTerm) ||
            patient.city_address.toLowerCase().includes(searchTerm) ||
            (patient.blood_type && patient.blood_type.toLowerCase().includes(searchTerm))
        );
    }

    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filtered.slice(start, end);
});

const totalPages = computed(() => {
    let filtered = props.patients;

    if (searchQuery.value) {
        const searchTerm = searchQuery.value.toLowerCase();
        filtered = filtered.filter(patient =>
            patient.first_name.toLowerCase().includes(searchTerm) ||
            patient.last_name.toLowerCase().includes(searchTerm) ||
            patient.rm_number.toLowerCase().includes(searchTerm) ||
            patient.phone_number.toLowerCase().includes(searchTerm) ||
            patient.city_address.toLowerCase().includes(searchTerm) ||
            (patient.blood_type && patient.blood_type.toLowerCase().includes(searchTerm))
        );
    }

    return Math.ceil(filtered.length / itemsPerPage);
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID');
};

const calculateAge = (birthDate: string) => {
    const today = new Date();
    const birth = new Date(birthDate);
    const age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        return age - 1;
    }
    return age;
};

const getGenderBadge = (gender: string) => {
    return gender === 'male' ? 'bg-blue-600' : 'bg-pink-600';
};

const togglePopover = (event: Event, patientId: string) => {
    const popover = popoverRefs.value[`popover_${patientId}`];
    if (popover) {
        popover.toggle(event);
    }
};

const handleAction = (action: string, patient: any) => {
    const popover = popoverRefs.value[`popover_${patient.id}`];
    if (popover) {
        popover.hide();
    }

    switch (action) {
        case 'show':
            showPatientDetails(patient);
            break;
        case 'edit':
            console.log('Edit patient:', patient);
            break;
        case 'delete':
            console.log('Delete patient:', patient);
            break;
    }
};

const showPatientDetails = (patient: any) => {
    selectedPatient.value = patient;
    showPatientModal.value = true;
};

const closePatientModal = () => {
    showPatientModal.value = false;
    selectedPatient.value = null;
};

const applyFilters = () => {
    const params: any = { page: 1 }; // Reset to page 1 when filtering

    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedGender.value) params.gender = selectedGender.value;
    if (selectedEthnic.value) params.ethnic = selectedEthnic.value;
    if (selectedEducation.value) params.education = selectedEducation.value;
    if (selectedMarriedStatus.value) params.married_status = selectedMarriedStatus.value;
    if (selectedJob.value) params.job = selectedJob.value;
    if (selectedBloodType.value) params.blood_type = selectedBloodType.value;

    router.visit('/user/patient', {
        method: 'get',
        data: params,
        preserveState: false,
        preserveScroll: false,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedGender.value = '';
    selectedEthnic.value = '';
    selectedEducation.value = '';
    selectedMarriedStatus.value = '';
    selectedJob.value = '';
    selectedBloodType.value = '';

    router.visit('/user/patient', {
        method: 'get',
        data: { page: 1 },
        preserveState: false,
        preserveScroll: false,
    });
};

// Server-side pagination handlers
const goToServerPage = (page: number) => {
    const params: any = { page };

    // Preserve current filters
    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedGender.value) params.gender = selectedGender.value;
    if (selectedEthnic.value) params.ethnic = selectedEthnic.value;
    if (selectedEducation.value) params.education = selectedEducation.value;
    if (selectedMarriedStatus.value) params.married_status = selectedMarriedStatus.value;
    if (selectedJob.value) params.job = selectedJob.value;
    if (selectedBloodType.value) params.blood_type = selectedBloodType.value;

    router.visit('/user/patient', {
        method: 'get',
        data: params,
        preserveState: false,
        preserveScroll: true,
    });
};

const onPageChange = (event: any) => {
    goToServerPage(event.page + 1); // PrimeVue uses 0-based page numbers
};

const onRowsChange = (event: any) => {
    const params: any = {
        page: 1,
        per_page: event.rows
    };

    // Preserve current filters
    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedGender.value) params.gender = selectedGender.value;
    if (selectedEthnic.value) params.ethnic = selectedEthnic.value;
    if (selectedEducation.value) params.education = selectedEducation.value;
    if (selectedMarriedStatus.value) params.married_status = selectedMarriedStatus.value;
    if (selectedJob.value) params.job = selectedJob.value;
    if (selectedBloodType.value) params.blood_type = selectedBloodType.value;

    router.visit('/user/patient', {
        method: 'get',
        data: params,
        preserveState: false,
        preserveScroll: false,
    });
};
</script>

<template>
    <div>

        <Head title="Patient Records" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <!-- Patient Details Modal Component -->
            <ShowPatient v-model:visible="showPatientModal" :patient="selectedPatient" />

            <!-- Filter Modal Component -->
            <FilterModal v-model:visible="showFilterModal" v-model:selectedGender="selectedGender"
                v-model:selectedBloodType="selectedBloodType" v-model:selectedMarriedStatus="selectedMarriedStatus"
                v-model:selectedEthnic="selectedEthnic" v-model:selectedEducation="selectedEducation"
                v-model:selectedJob="selectedJob" @apply-filters="applyFilters" @clear-filters="clearFilters" />

            <div class="card rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 shadow">
                <div class="mb-4 flex flex-col gap-2 rounded-t-lg bg-gray-800 p-4 md:flex-row md:justify-between">
                    <div>
                        <Button label="Add Patient"
                            class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" />
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <IconField class="flex-1 sm:w-64">
                            <InputIcon>
                                <i class="pi pi-search text-gray-400" />
                            </InputIcon>
                            <InputText v-model="searchQuery" placeholder="Keyword Search"
                                class="w-full border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-blue-500" />
                        </IconField>
                        <Button @click="showFilterModal = true" icon severity="secondary"
                            class="border-gray-600 bg-gray-600 text-white hover:bg-gray-700">
                            <Filter class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="block md:hidden">
                    <div class="space-y-4">
                        <div v-for="patient in filteredPatients" :key="patient.id"
                            class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-3 flex-1">
                                    <img :src="patient.avatar" :alt="`${patient.first_name} ${patient.last_name}`"
                                        class="h-12 w-12 rounded-full object-cover border border-gray-500" />
                                    <div>
                                        <h3 class="text-gray-100 font-semibold text-lg">{{ patient.first_name }} {{
                                            patient.last_name }}</h3>
                                        <p class="text-gray-400 text-sm">{{ patient.rm_number }}</p>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <Button @click="togglePopover($event, patient.id)" icon severity="secondary"
                                        class="border-gray-600 bg-gray-600 p-2 text-white hover:bg-gray-700"
                                        size="small">
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>

                                    <Popover :ref="(el) => (popoverRefs[`popover_${patient.id}`] = el)">
                                        <div class="flex w-40 flex-col gap-2 p-2">
                                            <Button @click="handleAction('show', patient)" severity="info"
                                                class="justify-start border-blue-600 bg-blue-600 py-2 text-sm text-white hover:bg-blue-700"
                                                size="small">
                                                <Eye class="mr-2 h-4 w-4" />
                                                Show
                                            </Button>

                                            <Button @click="handleAction('edit', patient)" severity="warning"
                                                class="justify-start border-yellow-600 bg-yellow-600 py-2 text-sm text-white hover:bg-yellow-700"
                                                size="small">
                                                <Edit class="mr-2 h-4 w-4" />
                                                Edit
                                            </Button>

                                            <Button @click="handleAction('delete', patient)" severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small">
                                                <Trash class="mr-2 h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </Popover>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">Gender</p>
                                    <span
                                        :class="`inline-block px-2 py-1 text-xs rounded-full text-white ${getGenderBadge(patient.gender)}`">
                                        {{ patient.gender === 'male' ? 'Male' : 'Female' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">Age</p>
                                    <p class="text-gray-100 font-semibold">{{ calculateAge(patient.birth_date) }} years
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">Phone</p>
                                    <p class="text-gray-100">{{ patient.phone_number }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">City</p>
                                    <p class="text-gray-100">{{ patient.city_address }}</p>
                                </div>
                            </div>

                            <div v-if="patient.blood_type" class="mb-3">
                                <p class="text-gray-400 text-xs uppercase tracking-wide">Blood Type</p>
                                <p class="text-gray-100 text-sm">{{ patient.blood_type }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Pagination -->
                    <div class="mt-6 flex justify-center">
                        <Button v-if="currentPage > 1" @click="currentPage--"
                            class="mr-2 bg-gray-600 hover:bg-gray-700 text-white border-gray-600" size="small">
                            Previous
                        </Button>
                        <span class="flex items-center px-4 text-gray-300">
                            Page {{ currentPage }} of {{ totalPages }}
                        </span>
                        <Button v-if="currentPage < totalPages" @click="currentPage++"
                            class="ml-2 bg-gray-600 hover:bg-gray-700 text-white border-gray-600" size="small">
                            Next
                        </Button>
                    </div>
                </div>

                <!-- Desktop Table View with Server-side Pagination -->
                <div class="hidden md:block">
                    <DataTable :value="props.patients" :filters="filters"
                        :globalFilterFields="['first_name', 'last_name', 'rm_number', 'phone_number', 'city_address', 'blood_type']"
                        lazy paginator :rows="serverPerPage" :rowsPerPageOptions="[15, 30, 50, 100]"
                        :totalRecords="pagination?.total || 0" :first="((serverCurrentPage - 1) * serverPerPage)"
                        @page="onPageChange" @rows-per-page-change="onRowsChange" tableStyle="min-width: 50rem"
                        class="dark-table">
                        <!-- Index Column -->
                        <Column field="index" header="#" style="width: 4%">
                            <template #body="{ index }">
                                <span class="text-gray-100">
                                    {{ (serverCurrentPage - 1) * serverPerPage + (index + 1) }}
                                </span>
                            </template>
                        </Column>

                        <Column field="avatar" header="Avatar" style="width: 8%">
                            <template #body="{ data }">
                                <img :src="data.avatar" :alt="`${data.first_name} ${data.last_name}`"
                                    class="h-10 w-10 rounded-full object-cover border border-gray-500" />
                            </template>
                        </Column>

                        <Column field="rm_number" header="RM Number" sortable style="width: 12%">
                            <template #body="{ data }">
                                <span class="text-gray-100 font-mono">{{ data.rm_number }}</span>
                            </template>
                        </Column>

                        <Column field="first_name" header="Patient Name" sortable style="width: 20%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.first_name }} {{ data.last_name }}</span>
                            </template>
                        </Column>

                        <Column field="gender" header="Gender" sortable style="width: 10%">
                            <template #body="{ data }">
                                <span
                                    :class="`inline-block px-2 py-1 text-xs rounded-full text-white ${getGenderBadge(data.gender)}`">
                                    {{ data.gender === 'male' ? 'Male' : 'Female' }}
                                </span>
                            </template>
                        </Column>

                        <Column field="birth_date" header="Age" sortable style="width: 8%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ calculateAge(data.birth_date) }} years</span>
                            </template>
                        </Column>

                        <Column field="phone_number" header="Phone" style="width: 15%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.phone_number }}</span>
                            </template>
                        </Column>

                        <Column field="city_address" header="City" style="width: 12%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.city_address }}</span>
                            </template>
                        </Column>

                        <Column field="blood_type" header="Blood Type" style="width: 8%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.blood_type || '-' }}</span>
                            </template>
                        </Column>

                        <Column field="actions" header="Actions" style="width: 7%">
                            <template #body="{ data }">
                                <div class="flex justify-center">
                                    <Button @click="togglePopover($event, data.id)" icon severity="secondary"
                                        class="border-gray-600 bg-gray-600 p-2 text-white hover:bg-gray-700">
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>

                                    <Popover :ref="(el) => (popoverRefs[`popover_${data.id}`] = el)">
                                        <div class="flex w-40 flex-col gap-2 p-2">
                                            <Button @click="handleAction('show', data)" severity="info"
                                                class="justify-start border-blue-600 bg-blue-600 py-2 text-sm text-white hover:bg-blue-700"
                                                size="small">
                                                <Eye class="mr-2 h-4 w-4" />
                                                Show
                                            </Button>
                                            <Button @click="handleAction('edit', data)" severity="warning"
                                                class="justify-start border-yellow-600 bg-yellow-600 py-2 text-sm text-white hover:bg-yellow-700"
                                                size="small">
                                                <Edit class="mr-2 h-4 w-4" />
                                                Edit
                                            </Button>
                                            <Button @click="handleAction('delete', data)" severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small">
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

:deep(.p-dropdown) {
    background: #374151;
    border: 1px solid #4b5563;
}

:deep(.p-dropdown .p-dropdown-label) {
    color: #f3f4f6;
}

:deep(.p-dropdown-panel) {
    background: #374151;
    border: 1px solid #4b5563;
}

:deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item) {
    color: #f3f4f6;
}

:deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item:hover) {
    color: #f3f4f6;
    background: #4b5563;
}

:deep(.p-select) {
    background: #374151;
    border: 1px solid #4b5563;
}

:deep(.p-select .p-select-label) {
    color: #f3f4f6;
}

:deep(.p-select-overlay) {
    background: #4b5563;
}

:deep(.p-select-overlay .p-select-option) {
    color: #f3f4f6;
}

:deep(.p-select-overlay .p-select-option:hover) {
    background: #4b5563;
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
