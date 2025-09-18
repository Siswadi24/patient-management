<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { Edit, Eye, Filter, MoreVertical, Trash } from 'lucide-vue-next';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Popover from 'primevue/popover';
import { computed, ref, watch } from 'vue';
import FilterModal from './component/filterModal.vue';
import ShowPatient from './showPatient.vue';
import TambahPatient from './tambahPatient.vue';
import EditPatient from './editPatient.vue';

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
    { label: 'Female', value: 'female' },
];

const bloodTypeOptions = [
    { label: 'All', value: '' },
    { label: 'A', value: 'A' },
    { label: 'B', value: 'B' },
    { label: 'AB', value: 'AB' },
    { label: 'O', value: 'O' },
];

const marriedStatusOptions = [
    { label: 'All', value: '' },
    { label: 'Kawin', value: 'Kawin' },
    { label: 'Belum Kawin', value: 'Belum Kawin' },
    { label: 'Cerai Hidup', value: 'Cerai Hidup' },
    { label: 'Cerai Mati', value: 'Cerai Mati' },
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
        filtered = filtered.filter(
            (patient) =>
                patient.first_name.toLowerCase().includes(searchTerm) ||
                patient.last_name.toLowerCase().includes(searchTerm) ||
                patient.rm_number.toLowerCase().includes(searchTerm) ||
                patient.phone_number.toLowerCase().includes(searchTerm) ||
                patient.city_address.toLowerCase().includes(searchTerm) ||
                (patient.blood_type && patient.blood_type.toLowerCase().includes(searchTerm)),
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
        filtered = filtered.filter(
            (patient) =>
                patient.first_name.toLowerCase().includes(searchTerm) ||
                patient.last_name.toLowerCase().includes(searchTerm) ||
                patient.rm_number.toLowerCase().includes(searchTerm) ||
                patient.phone_number.toLowerCase().includes(searchTerm) ||
                patient.city_address.toLowerCase().includes(searchTerm) ||
                (patient.blood_type && patient.blood_type.toLowerCase().includes(searchTerm)),
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
            break;
        case 'delete':
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
        per_page: event.rows,
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

// Translate gender to Indonesian
const translateGender = (gender: string) => {
    return gender === 'male' ? 'Laki-laki' : 'Perempuan';
};
</script>

<template>
    <div>
        <Head title="Data Pasien" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <!-- Patient Details Modal Component -->
            <ShowPatient v-model:visible="showPatientModal" :patient="selectedPatient" />

            <!-- Filter Modal Component -->
            <FilterModal
                v-model:visible="showFilterModal"
                v-model:selectedGender="selectedGender"
                v-model:selectedBloodType="selectedBloodType"
                v-model:selectedMarriedStatus="selectedMarriedStatus"
                v-model:selectedEthnic="selectedEthnic"
                v-model:selectedEducation="selectedEducation"
                v-model:selectedJob="selectedJob"
                @apply-filters="applyFilters"
                @clear-filters="clearFilters"
            />

            <div class="card rounded-xl border border-gray-700 bg-gray-800 px-3 py-3 shadow">
                <div class="mb-4 flex flex-col gap-4 rounded-t-lg bg-gray-800 p-4 md:flex-row md:items-center md:justify-between">
                    <div class="w-full md:w-auto">
                        <!-- <Button
                            label="Tambah Pasien"
                            class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto"
                        /> -->
                        <TambahPatient />
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center md:gap-2">
                        <!-- Search Input - Full width on mobile, larger on desktop -->
                        <IconField class="w-full md:w-96">
                            <InputIcon>
                                <i class="pi pi-search text-gray-400" />
                            </InputIcon>
                            <InputText
                                v-model="searchQuery"
                                placeholder="Cari kata kunci..."
                                class="w-full border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-blue-500"
                            />
                        </IconField>

                        <!-- Filter Button - Full width on mobile, compact on desktop -->
                        <Button
                            @click="showFilterModal = true"
                            class="w-full justify-center border-gray-600 bg-gray-600 text-white hover:bg-gray-700 md:w-auto md:justify-start"
                            severity="secondary"
                        >
                            <Filter class="h-4 w-4 md:mr-0" />
                            <span class="ml-2 md:hidden">Filter</span>
                        </Button>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="block md:hidden">
                    <div class="space-y-4">
                        <div v-for="patient in filteredPatients" :key="patient.id" class="rounded-lg border border-gray-600 bg-gray-700 p-4">
                            <div class="mb-3 flex items-start justify-between">
                                <div class="flex flex-1 items-center space-x-3">
                                    <img
                                        :src="patient.avatar"
                                        :alt="`${patient.first_name} ${patient.last_name}`"
                                        class="h-12 w-12 rounded-full border border-gray-500 object-cover"
                                    />
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-100">{{ patient.first_name }} {{ patient.last_name }}</h3>
                                        <p class="text-sm text-gray-400">{{ patient.rm_number }}</p>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <Button
                                        @click="togglePopover($event, patient.id)"
                                        icon
                                        severity="secondary"
                                        class="border-gray-600 bg-gray-600 p-2 text-white hover:bg-gray-700"
                                        size="small"
                                    >
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>

                                    <Popover :ref="(el) => (popoverRefs[`popover_${patient.id}`] = el)">
                                        <div class="flex w-40 flex-col gap-2 p-2">
                                            <Button
                                                @click="handleAction('show', patient)"
                                                severity="info"
                                                class="justify-start border-blue-600 bg-blue-600 py-2 text-sm text-white hover:bg-blue-700"
                                                size="small"
                                            >
                                                <Eye class="mr-2 h-4 w-4" />
                                                Lihat
                                            </Button>

                                            <Button
                                                @click="handleAction('edit', patient)"
                                                severity="warning"
                                                class="justify-start border-yellow-600 bg-yellow-600 py-2 text-sm text-white hover:bg-yellow-700"
                                                size="small"
                                            >
                                                <Edit class="mr-2 h-4 w-4" />
                                                Edit
                                            </Button>

                                            <Button
                                                @click="handleAction('delete', patient)"
                                                severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small"
                                            >
                                                <Trash class="mr-2 h-4 w-4" />
                                                Hapus
                                            </Button>
                                        </div>
                                    </Popover>
                                </div>
                            </div>

                            <div class="mb-3 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs tracking-wide text-gray-400 uppercase">Jenis Kelamin</p>
                                    <span :class="`inline-block rounded-full px-2 py-1 text-xs text-white ${getGenderBadge(patient.gender)}`">
                                        {{ translateGender(patient.gender) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs tracking-wide text-gray-400 uppercase">Umur</p>
                                    <p class="font-semibold text-gray-100">{{ calculateAge(patient.birth_date) }} tahun</p>
                                </div>
                            </div>

                            <div class="mb-3 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs tracking-wide text-gray-400 uppercase">Telepon</p>
                                    <p class="text-gray-100">{{ patient.phone_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs tracking-wide text-gray-400 uppercase">Kota</p>
                                    <p class="text-gray-100">{{ patient.city_address }}</p>
                                </div>
                            </div>

                            <div v-if="patient.blood_type" class="mb-3">
                                <p class="text-xs tracking-wide text-gray-400 uppercase">Golongan Darah</p>
                                <p class="text-sm text-gray-100">{{ patient.blood_type }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Pagination -->
                    <div class="mt-6 flex justify-center">
                        <Button
                            v-if="currentPage > 1"
                            @click="currentPage--"
                            class="mr-2 border-gray-600 bg-gray-600 text-white hover:bg-gray-700"
                            size="small"
                        >
                            Sebelumnya
                        </Button>
                        <span class="flex items-center px-4 text-gray-300"> Halaman {{ currentPage }} dari {{ totalPages }} </span>
                        <Button
                            v-if="currentPage < totalPages"
                            @click="currentPage++"
                            class="ml-2 border-gray-600 bg-gray-600 text-white hover:bg-gray-700"
                            size="small"
                        >
                            Selanjutnya
                        </Button>
                    </div>
                </div>

                <!-- Desktop Table View with Server-side Pagination -->
                <div class="hidden md:block">
                    <DataTable
                        :value="props.patients"
                        :filters="filters"
                        :globalFilterFields="['first_name', 'last_name', 'rm_number', 'phone_number', 'city_address', 'blood_type']"
                        lazy
                        paginator
                        :rows="serverPerPage"
                        :rowsPerPageOptions="[15, 30, 50, 100]"
                        :totalRecords="pagination?.total || 0"
                        :first="(serverCurrentPage - 1) * serverPerPage"
                        @page="onPageChange"
                        @rows-per-page-change="onRowsChange"
                        tableStyle="min-width: 50rem"
                        class="dark-table"
                    >
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
                                <img
                                    :src="data.avatar"
                                    :alt="`${data.first_name} ${data.last_name}`"
                                    class="h-10 w-10 rounded-full border border-gray-500 object-cover"
                                />
                            </template>
                        </Column>

                        <Column field="rm_number" header="No. RM" sortable style="width: 12%">
                            <template #body="{ data }">
                                <span class="font-mono text-gray-100">{{ data.rm_number }}</span>
                            </template>
                        </Column>

                        <Column field="first_name" header="Nama Pasien" sortable style="width: 20%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.first_name }} {{ data.last_name }}</span>
                            </template>
                        </Column>

                        <Column field="gender" header="Jenis Kelamin" sortable style="width: 10%">
                            <template #body="{ data }">
                                <span :class="`inline-block rounded-full px-2 py-1 text-xs text-white ${getGenderBadge(data.gender)}`">
                                    {{ translateGender(data.gender) }}
                                </span>
                            </template>
                        </Column>

                        <Column field="birth_date" header="Umur" sortable style="width: 8%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ calculateAge(data.birth_date) }} tahun</span>
                            </template>
                        </Column>

                        <Column field="phone_number" header="Telepon" style="width: 15%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.phone_number }}</span>
                            </template>
                        </Column>

                        <Column field="city_address" header="Kota" style="width: 12%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.city_address }}</span>
                            </template>
                        </Column>

                        <Column field="blood_type" header="Gol. Darah" style="width: 8%">
                            <template #body="{ data }">
                                <span class="text-gray-100">{{ data.blood_type || '-' }}</span>
                            </template>
                        </Column>

                        <Column field="actions" header="Aksi" style="width: 7%">
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
                                                Lihat
                                            </Button>
                                            <EditPatient :patient="data" />
                                            <Button
                                                @click="handleAction('delete', data)"
                                                severity="danger"
                                                class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
                                                size="small"
                                            >
                                                <Trash class="mr-2 h-4 w-4" />
                                                Hapus
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
