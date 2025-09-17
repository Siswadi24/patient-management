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
</script>

<template>
    <Dialog
        v-model:visible="dialogVisible"
        modal
        header="Patient Details"
        :style="{ width: '50rem' }"
        class="dark"
    >
        <div v-if="patient" class="space-y-6">
            <!-- Patient Header -->
            <div class="flex items-center space-x-4 border-b border-gray-600 pb-4">
                <img
                    :src="patient.avatar"
                    :alt="`${patient.first_name} ${patient.last_name}`"
                    class="h-20 w-20 rounded-full object-cover border-2 border-gray-500"
                />
                <div>
                    <h2 class="text-2xl font-bold text-gray-100">
                        {{ patient.first_name }} {{ patient.last_name }}
                    </h2>
                    <p class="text-gray-400">RM: {{ patient.rm_number }}</p>
                    <span :class="`inline-block px-3 py-1 text-sm rounded-full text-white ${getGenderBadge(patient.gender)}`">
                        {{ patient.gender === 'male' ? 'Male' : 'Female' }}
                    </span>
                </div>
            </div>

            <!-- Patient Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-100 border-b border-gray-600 pb-2">Personal Information</h3>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Age</label>
                        <p class="text-gray-100 font-medium">{{ calculateAge(patient.birth_date) }} years old</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Birth Date & Place</label>
                        <p class="text-gray-100">{{ formatDate(patient.birth_date) }}</p>
                        <p class="text-gray-300 text-sm">{{ patient.birth_place }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Blood Type</label>
                        <p class="text-gray-100">{{ patient.blood_type || '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Identity Number</label>
                        <p class="text-gray-100 font-mono">{{ patient.identity_number }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">BPJS Number</label>
                        <p class="text-gray-100 font-mono">{{ patient.bpjs_number || '-' }}</p>
                    </div>
                </div>

                <!-- Contact & Address Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-100 border-b border-gray-600 pb-2">Contact & Address</h3>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Phone Number</label>
                        <p class="text-gray-100">{{ patient.phone_number }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Address</label>
                        <p class="text-gray-100">{{ patient.street_address }}</p>
                        <p class="text-gray-300 text-sm">{{ patient.city_address }}, {{ patient.state_address }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Emergency Contact</label>
                        <p class="text-gray-100">{{ patient.emergency_full_name }}</p>
                        <p class="text-gray-300 text-sm">{{ patient.emergency_phone_number }}</p>
                    </div>
                </div>

                <!-- Family Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-100 border-b border-gray-600 pb-2">Family Information</h3>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Father's Name</label>
                        <p class="text-gray-100">{{ patient.father_name }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Mother's Name</label>
                        <p class="text-gray-100">{{ patient.mother_name }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Marital Status</label>
                        <p class="text-gray-100">{{ patient.married_status }}</p>
                    </div>
                </div>

                <!-- Other Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-100 border-b border-gray-600 pb-2">Other Information</h3>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Job</label>
                        <p class="text-gray-100">{{ patient.job }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Education</label>
                        <p class="text-gray-100">{{ patient.education }}</p>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm uppercase tracking-wide">Ethnic</label>
                        <p class="text-gray-100">{{ patient.ethnic }}</p>
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
