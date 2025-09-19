<script setup lang="ts">
import { computed } from 'vue';
import Dialog from 'primevue/dialog';

const props = defineProps<{
    visible: boolean;
    patient: any;
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean];
}>();

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
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

const translateGender = (gender: string) => {
    return gender === 'male' ? 'Laki-laki' : 'Perempuan';
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" modal header="Detail Pasien" :style="{ width: '60rem' }" class="dark">
        <div v-if="patient" class="space-y-6">
            <div class="flex items-center space-x-4 border-b border-gray-600 pb-4">
                <img :src="patient.avatar" :alt="`${patient.first_name} ${patient.last_name}`"
                    class="h-20 w-20 rounded-full object-cover border-2 border-gray-500" />
                <div>
                    <h2 class="text-2xl font-bold ">
                        {{ patient.first_name }} {{ patient.last_name }}
                    </h2>
                    <p class="text-gray-500">No. RM: {{ patient.rm_number }}</p>
                    <span
                        :class="`inline-block px-3 py-1 text-sm rounded-full text-white ${getGenderBadge(patient.gender)}`">
                        {{ translateGender(patient.gender) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold  border-b border-gray-600 pb-2">Informasi Pribadi</h3>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Umur</label>
                        <p class=" font-medium">{{ calculateAge(patient.birth_date) }} tahun</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Tanggal & Tempat Lahir</label>
                        <p class="">{{ formatDate(patient.birth_date) }}</p>
                        <p class="text-sm">{{ patient.birth_place }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Golongan Darah</label>
                        <p class="">{{ patient.blood_type || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Nomor Identitas (KTP)</label>
                        <p class=" font-mono">{{ patient.identity_number || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Nomor BPJS</label>
                        <p class=" font-mono">{{ patient.bpjs_number || '-' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold  border-b border-gray-600 pb-2">Kontak & Alamat</h3>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Nomor Telepon</label>
                        <p class="">{{ patient.phone_number || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Alamat Lengkap</label>
                        <p class="">{{ patient.street_address || '-' }}</p>
                        <p class="text-sm">{{ patient.city_address || '-' }}, {{ patient.state_address || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Kontak Darurat</label>
                        <p class="">{{ patient.emergency_full_name || '-' }}</p>
                        <p class="text-sm">{{ patient.emergency_phone_number || '-' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold  border-b border-gray-600 pb-2">Informasi Keluarga</h3>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Nama Ayah</label>
                        <p class="">{{ patient.father_name || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Nama Ibu</label>
                        <p class="">{{ patient.mother_name || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Status Pernikahan</label>
                        <p class="">{{ patient.married_status || '-' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold  border-b border-gray-600 pb-2">Informasi Lainnya</h3>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Pekerjaan</label>
                        <p class="">{{ patient.job || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Pendidikan</label>
                        <p class="">{{ patient.education || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Suku/Etnis</label>
                        <p class="">{{ patient.ethnic || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Hambatan Komunikasi</label>
                        <p class="">{{ patient.communication_barrier || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Status Disabilitas</label>
                        <p class="">{{ patient.disability_status || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Dibuat Pada</label>
                        <p class="">{{ formatDate(patient.created_at) }}</p>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-wide">Terakhir Diperbarui</label>
                        <p class="">{{ formatDate(patient.updated_at) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<style scoped>
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
