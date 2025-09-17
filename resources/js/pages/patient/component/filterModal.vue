<script setup lang="ts">
import { computed } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';

const props = defineProps<{
    visible: boolean;
    selectedGender: string;
    selectedBloodType: string;
    selectedMarriedStatus: string;
    selectedEthnic: string;
    selectedEducation: string;
    selectedJob: string;
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean];
    'update:selectedGender': [value: string];
    'update:selectedBloodType': [value: string];
    'update:selectedMarriedStatus': [value: string];
    'update:selectedEthnic': [value: string];
    'update:selectedEducation': [value: string];
    'update:selectedJob': [value: string];
    'apply-filters': [];
    'clear-filters': [];
}>();

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const genderValue = computed({
    get: () => props.selectedGender,
    set: (value) => emit('update:selectedGender', value)
});

const bloodTypeValue = computed({
    get: () => props.selectedBloodType,
    set: (value) => emit('update:selectedBloodType', value)
});

const marriedStatusValue = computed({
    get: () => props.selectedMarriedStatus,
    set: (value) => emit('update:selectedMarriedStatus', value)
});

const ethnicValue = computed({
    get: () => props.selectedEthnic,
    set: (value) => emit('update:selectedEthnic', value)
});

const educationValue = computed({
    get: () => props.selectedEducation,
    set: (value) => emit('update:selectedEducation', value)
});

const jobValue = computed({
    get: () => props.selectedJob,
    set: (value) => emit('update:selectedJob', value)
});

// Filter options in Indonesian
const genderOptions = [
    { label: 'Semua', value: '' },
    { label: 'Laki-laki', value: 'male' },
    { label: 'Perempuan', value: 'female' }
];

const bloodTypeOptions = [
    { label: 'Semua', value: '' },
    { label: 'A', value: 'A' },
    { label: 'B', value: 'B' },
    { label: 'AB', value: 'AB' },
    { label: 'O', value: 'O' }
];

const marriedStatusOptions = [
    { label: 'Semua', value: '' },
    { label: 'Kawin', value: 'Kawin' },
    { label: 'Belum Kawin', value: 'Belum Kawin' },
    { label: 'Cerai Hidup', value: 'Cerai Hidup' },
    { label: 'Cerai Mati', value: 'Cerai Mati' }
];

const handleApplyFilters = () => {
    emit('apply-filters');
    dialogVisible.value = false;
};

const handleClearFilters = () => {
    emit('clear-filters');
    dialogVisible.value = false;
};
</script>

<template>
    <Dialog
        v-model:visible="dialogVisible"
        modal
        header="Filter Pasien"
        :style="{ width: '50rem' }"
        class="dark"
    >
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Jenis Kelamin</label>
                    <Select
                        v-model="genderValue"
                        :options="genderOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih Jenis Kelamin"
                        class="w-full bg-gray-700 border-gray-600"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Golongan Darah</label>
                    <Select
                        v-model="bloodTypeValue"
                        :options="bloodTypeOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih Golongan Darah"
                        class="w-full bg-gray-700 border-gray-600"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Status Pernikahan</label>
                    <Select
                        v-model="marriedStatusValue"
                        :options="marriedStatusOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih Status Pernikahan"
                        class="w-full bg-gray-700 border-gray-600"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Suku/Etnis</label>
                    <InputText
                        v-model="ethnicValue"
                        placeholder="Masukkan suku/etnis"
                        class="w-full bg-gray-700 border-gray-600 text-gray-100"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Pendidikan</label>
                    <InputText
                        v-model="educationValue"
                        placeholder="Masukkan pendidikan"
                        class="w-full bg-gray-700 border-gray-600 text-gray-100"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Pekerjaan</label>
                    <InputText
                        v-model="jobValue"
                        placeholder="Masukkan pekerjaan"
                        class="w-full bg-gray-700 border-gray-600 text-gray-100"
                    />
                </div>
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <Button
                    @click="handleClearFilters"
                    label="Hapus Semua"
                    severity="secondary"
                    class="bg-gray-600 border-gray-600 text-white hover:bg-gray-700"
                />
                <Button
                    @click="handleApplyFilters"
                    label="Terapkan Filter"
                    class="bg-blue-600 border-blue-600 text-white hover:bg-blue-700"
                />
            </div>
        </div>
    </Dialog>
</template>

<style scoped>
</style>
