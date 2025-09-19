<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Edit } from 'lucide-vue-next';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface Patient {
    id: number;
    rm_number: string;
    first_name: string;
    last_name: string;
    gender: string;
    birth_place: string;
    birth_date: string;
    phone_number: string;
    street_address: string;
    city_address: string;
    state_address: string;
    emergency_full_name: string;
    emergency_phone_number: string;
    identity_number: string;
    bpjs_number: string | null;
    ethnic: string;
    education: string;
    communication_barrier: string | null;
    disability_status: string | null;
    married_status: string;
    father_name: string;
    mother_name: string;
    job: string;
    blood_type: string;
    avatar: string;
}

const props = defineProps<{
    patient: Patient;
}>();

const visible = ref(false);
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');

const form = useForm({
    rm_number: '',
    first_name: '',
    last_name: '',
    gender: '',
    birth_place: '',
    birth_date: new Date(),
    phone_number: '',
    street_address: '',
    city_address: '',
    state_address: '',
    emergency_full_name: '',
    emergency_phone_number: '',
    identity_number: '',
    bpjs_number: '',
    ethnic: '',
    education: '',
    communication_barrier: '',
    disability_status: '',
    married_status: '',
    father_name: '',
    mother_name: '',
    job: '',
    blood_type: '',
    avatar: null as File | null,
});

const genderOptions = [
    { label: 'Laki-laki', value: 'male' },
    { label: 'Perempuan', value: 'female' },
];

const bloodTypeOptions = [
    { label: 'A', value: 'A' },
    { label: 'B', value: 'B' },
    { label: 'O', value: 'O' },
    { label: 'AB', value: 'AB' },
];

const ethnicOptions = [
    { label: 'Jawa', value: 'Jawa' },
    { label: 'Sunda', value: 'Sunda' },
    { label: 'Batak', value: 'Batak' },
    { label: 'Minang', value: 'Minang' },
    { label: 'Bugis', value: 'Bugis' },
    { label: 'Papua', value: 'Papua' },
    { label: 'Lainnya', value: 'Lainnya' },
];

const educationOptions = [
    { label: 'SD', value: 'SD' },
    { label: 'SMP', value: 'SMP' },
    { label: 'SMA', value: 'SMA' },
    { label: 'D1', value: 'D1' },
    { label: 'D2', value: 'D2' },
    { label: 'D3', value: 'D3' },
    { label: 'D4', value: 'D4' },
    { label: 'S1', value: 'S1' },
    { label: 'S2', value: 'S2' },
    { label: 'S3', value: 'S3' },
    { label: 'Pendidikan Profesi', value: 'Pendidikan Profesi' },
];

const marriedStatusOptions = [
    { label: 'Belum Kawin', value: 'Belum Kawin' },
    { label: 'Kawin', value: 'Kawin' },
    { label: 'Cerai Hidup', value: 'Cerai Hidup' },
    { label: 'Cerai Mati', value: 'Cerai Mati' },
];

const jobOptions = [
    { label: 'Pelajar', value: 'Pelajar' },
    { label: 'Mahasiswa', value: 'Mahasiswa' },
    { label: 'Pegawai Negeri', value: 'Pegawai Negeri' },
    { label: 'Pegawai Swasta', value: 'Pegawai Swasta' },
    { label: 'Wiraswasta', value: 'Wiraswasta' },
    { label: 'Petani', value: 'Petani' },
    { label: 'Nelayan', value: 'Nelayan' },
    { label: 'Buruh', value: 'Buruh' },
    { label: 'Ibu Rumah Tangga', value: 'Ibu Rumah Tangga' },
    { label: 'Tidak Bekerja', value: 'Tidak Bekerja' },
    { label: 'Pensiunan', value: 'Pensiunan' },
    { label: 'Lainnya', value: 'Lainnya' },
];

async function openEditDialog() {
    try {
        form.rm_number = props.patient.rm_number;
        form.first_name = props.patient.first_name;
        form.last_name = props.patient.last_name;
        form.gender = props.patient.gender;
        form.birth_place = props.patient.birth_place;
        form.birth_date = new Date(props.patient.birth_date);
        form.phone_number = props.patient.phone_number;
        form.street_address = props.patient.street_address;
        form.city_address = props.patient.city_address;
        form.state_address = props.patient.state_address;
        form.emergency_full_name = props.patient.emergency_full_name;
        form.emergency_phone_number = props.patient.emergency_phone_number;
        form.identity_number = props.patient.identity_number;
        form.bpjs_number = props.patient.bpjs_number || '';
        form.ethnic = props.patient.ethnic;
        form.education = props.patient.education;
        form.communication_barrier = props.patient.communication_barrier || '';
        form.disability_status = props.patient.disability_status || '';
        form.married_status = props.patient.married_status;
        form.father_name = props.patient.father_name;
        form.mother_name = props.patient.mother_name;
        form.job = props.patient.job;
        form.blood_type = props.patient.blood_type;

        visible.value = true;
    } catch (error) {
        showErrorAlert(['Gagal memuat data pasien']);
    }
}

function handleSubmit() {
    if (form.processing) return;

    let formattedDate = '';
    if (form.birth_date instanceof Date) {
        const year = form.birth_date.getFullYear();
        const month = (form.birth_date.getMonth() + 1).toString().padStart(2, '0');
        const day = form.birth_date.getDate().toString().padStart(2, '0');
        formattedDate = `${year}-${month}-${day}`;
    }

    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('rm_number', form.rm_number || '');
    formData.append('first_name', form.first_name || '');
    formData.append('last_name', form.last_name || '');
    formData.append('gender', form.gender || '');
    formData.append('birth_place', form.birth_place || '');
    formData.append('birth_date', formattedDate);
    formData.append('phone_number', form.phone_number || '');
    formData.append('street_address', form.street_address || '');
    formData.append('city_address', form.city_address || '');
    formData.append('state_address', form.state_address || '');
    formData.append('emergency_full_name', form.emergency_full_name || '');
    formData.append('emergency_phone_number', form.emergency_phone_number || '');
    formData.append('identity_number', form.identity_number || '');
    formData.append('bpjs_number', form.bpjs_number || '');
    formData.append('ethnic', form.ethnic || '');
    formData.append('education', form.education || '');
    formData.append('communication_barrier', form.communication_barrier || '');
    formData.append('disability_status', form.disability_status || '');
    formData.append('married_status', form.married_status || '');
    formData.append('father_name', form.father_name || '');
    formData.append('mother_name', form.mother_name || '');
    formData.append('job', form.job || '');
    formData.append('blood_type', form.blood_type || '');

    if (form.avatar) {
        formData.append('avatar', form.avatar);
    }

    router.post(route('user.patient.updatePatient', props.patient.id), formData, {
        forceFormData: true,
        onSuccess: () => {
            visible.value = false;
            resetForm();
            showSuccessAlert();
        },
        onError: (errors) => {
            showErrorAlert(errors);
        },
    });
}

function onFileSelect(event: any) {
    const file = event.files[0];
    if (file) {
        form.avatar = file;
    }
}

function showSuccessAlert() {
    alertTitle.value = 'Berhasil!';
    alertMessage.value = 'Data pasien berhasil diperbarui.';
    showAlert.value = true;
}

function showErrorAlert(errors: any) {
    alertTitle.value = 'Error!';

    let errorMessages = '';
    if (typeof errors === 'object') {
        Object.keys(errors).forEach((key) => {
            if (Array.isArray(errors[key])) {
                errorMessages += errors[key].join('\n') + '\n';
            } else {
                errorMessages += errors[key] + '\n';
            }
        });
    } else {
        errorMessages = 'Terjadi kesalahan saat memperbarui data.';
    }

    alertMessage.value = errorMessages.trim();
    showAlert.value = true;
}

function closeDialog() {
    if (!form.processing) {
        visible.value = false;
        resetForm();
    }
}

function resetForm() {
    form.reset();
    form.clearErrors();
}

function closeAlert() {
    showAlert.value = false;
}

function handleModalClick(event: Event) {
    event.stopPropagation();
}
</script>

<template>
    <div>
        <AlertRS v-if="showAlert && !visible" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button @click="openEditDialog" size="small"
            class="justify-start border-yellow-600 bg-yellow-600 py-2 text-sm text-white hover:bg-yellow-700">
            <Edit class="mr-2 h-4 w-4" />
            <span>Edit</span>
        </Button>

        <Dialog v-model:visible="visible" modal header="Edit Pasien" :style="{ width: '60rem' }" class="dark"
            :closable="!form.processing" @hide="closeDialog">
            <template #header>
                <h3 class="text-lg font-semibold">Edit Pasien</h3>
            </template>

            <div @click="handleModalClick">
                <span class="text-surface-500 dark:text-surface-400 mb-8 block">Edit data pasien yang sudah ada.</span>

                <form @submit.prevent="handleSubmit">
                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Informasi Dasar</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="rm_number" class="font-semibold">Nomor RM</label>
                                <InputText id="rm_number" v-model="form.rm_number" class="flex-auto" autocomplete="off"
                                    :class="{ 'border-red-500': form.errors.rm_number }" :disabled="form.processing"
                                    readonly />
                                <div v-if="form.errors.rm_number" class="text-sm text-red-400">
                                    {{ form.errors.rm_number }}
                                </div>
                            </div>
                            <div></div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Informasi Pribadi</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="first_name" class="font-semibold">Nama Depan</label>
                                <InputText id="first_name" v-model="form.first_name" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.first_name }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.first_name" class="text-sm text-red-400">
                                    {{ form.errors.first_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="last_name" class="font-semibold">Nama Belakang</label>
                                <InputText id="last_name" v-model="form.last_name" class="flex-auto" autocomplete="off"
                                    :class="{ 'border-red-500': form.errors.last_name }" :disabled="form.processing" />
                                <div v-if="form.errors.last_name" class="text-sm text-red-400">
                                    {{ form.errors.last_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="gender" class="font-semibold">Jenis Kelamin</label>
                                <Select id="gender" v-model="form.gender" :options="genderOptions" optionLabel="label"
                                    optionValue="value" placeholder="Pilih Jenis Kelamin" class="w-full"
                                    :class="{ 'border-red-500': form.errors.gender }" :disabled="form.processing" />
                                <div v-if="form.errors.gender" class="text-sm text-red-400">
                                    {{ form.errors.gender }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="birth_place" class="font-semibold">Tempat Lahir</label>
                                <InputText id="birth_place" v-model="form.birth_place" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.birth_place }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.birth_place" class="text-sm text-red-400">
                                    {{ form.errors.birth_place }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="birth_date" class="font-semibold">Tanggal Lahir</label>
                                <Calendar id="birth_date" v-model="form.birth_date" dateFormat="dd/mm/yy" showIcon
                                    class="w-full" :class="{ 'border-red-500': form.errors.birth_date }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.birth_date" class="text-sm text-red-400">
                                    {{ form.errors.birth_date }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="blood_type" class="font-semibold">Golongan Darah</label>
                                <Select id="blood_type" v-model="form.blood_type" :options="bloodTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Pilih Golongan Darah"
                                    class="w-full" :class="{ 'border-red-500': form.errors.blood_type }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.blood_type" class="text-sm text-red-400">
                                    {{ form.errors.blood_type }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Kontak & Identitas</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="phone_number" class="font-semibold">Nomor Telepon</label>
                                <InputText id="phone_number" v-model="form.phone_number" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.phone_number }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.phone_number" class="text-sm text-red-400">
                                    {{ form.errors.phone_number }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="identity_number" class="font-semibold">Nomor Identitas (KTP)</label>
                                <InputText id="identity_number" v-model="form.identity_number" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.identity_number }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.identity_number" class="text-sm text-red-400">
                                    {{ form.errors.identity_number }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="bpjs_number" class="font-semibold">Nomor BPJS (Opsional)</label>
                                <InputText id="bpjs_number" v-model="form.bpjs_number" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.bpjs_number }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.bpjs_number" class="text-sm text-red-400">
                                    {{ form.errors.bpjs_number }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="avatar" class="font-semibold">Foto Pasien (Opsional)</label>
                                <FileUpload mode="basic" accept="image/*" :maxFileSize="2000000" @select="onFileSelect"
                                    chooseLabel="Pilih Foto Baru" class="w-full"
                                    :class="{ 'border-red-500': form.errors.avatar }" :disabled="form.processing" />
                                <div v-if="form.errors.avatar" class="text-sm text-red-400">
                                    {{ form.errors.avatar }}
                                </div>
                                <div v-if="props.patient.avatar" class="mt-2 text-sm text-gray-400">
                                    Foto saat ini: Avatar pasien
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Alamat</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col md:col-span-2">
                                <label for="street_address" class="font-semibold">Alamat Jalan</label>
                                <Textarea id="street_address" v-model="form.street_address" class="flex-auto" rows="2"
                                    :class="{ 'border-red-500': form.errors.street_address }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.street_address" class="text-sm text-red-400">
                                    {{ form.errors.street_address }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="city_address" class="font-semibold">Kota</label>
                                <InputText id="city_address" v-model="form.city_address" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.city_address }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.city_address" class="text-sm text-red-400">
                                    {{ form.errors.city_address }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="state_address" class="font-semibold">Provinsi</label>
                                <InputText id="state_address" v-model="form.state_address" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.state_address }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.state_address" class="text-sm text-red-400">
                                    {{ form.errors.state_address }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Kontak Darurat</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="emergency_full_name" class="font-semibold">Nama Lengkap</label>
                                <InputText id="emergency_full_name" v-model="form.emergency_full_name" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.emergency_full_name }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.emergency_full_name" class="text-sm text-red-400">
                                    {{ form.errors.emergency_full_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="emergency_phone_number" class="font-semibold">Nomor Telepon</label>
                                <InputText id="emergency_phone_number" v-model="form.emergency_phone_number"
                                    class="flex-auto" autocomplete="off"
                                    :class="{ 'border-red-500': form.errors.emergency_phone_number }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.emergency_phone_number" class="text-sm text-red-400">
                                    {{ form.errors.emergency_phone_number }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Informasi Keluarga</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="father_name" class="font-semibold">Nama Ayah</label>
                                <InputText id="father_name" v-model="form.father_name" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.father_name }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.father_name" class="text-sm text-red-400">
                                    {{ form.errors.father_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="mother_name" class="font-semibold">Nama Ibu</label>
                                <InputText id="mother_name" v-model="form.mother_name" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.mother_name }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.mother_name" class="text-sm text-red-400">
                                    {{ form.errors.mother_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="married_status" class="font-semibold">Status Pernikahan</label>
                                <Select id="married_status" v-model="form.married_status"
                                    :options="marriedStatusOptions" optionLabel="label" optionValue="value"
                                    placeholder="Pilih Status Pernikahan" class="w-full"
                                    :class="{ 'border-red-500': form.errors.married_status }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.married_status" class="text-sm text-red-400">
                                    {{ form.errors.married_status }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Informasi Lainnya</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <label for="ethnic" class="font-semibold">Suku/Etnis</label>
                                <Select id="ethnic" v-model="form.ethnic" :options="ethnicOptions" optionLabel="label"
                                    optionValue="value" placeholder="Pilih Suku/Etnis" class="w-full"
                                    :class="{ 'border-red-500': form.errors.ethnic }" :disabled="form.processing" />
                                <div v-if="form.errors.ethnic" class="text-sm text-red-400">
                                    {{ form.errors.ethnic }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="education" class="font-semibold">Pendidikan</label>
                                <Select id="education" v-model="form.education" :options="educationOptions"
                                    optionLabel="label" optionValue="value" placeholder="Pilih Pendidikan"
                                    class="w-full" :class="{ 'border-red-500': form.errors.education }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.education" class="text-sm text-red-400">
                                    {{ form.errors.education }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="job" class="font-semibold">Pekerjaan</label>
                                <Select id="job" v-model="form.job" :options="jobOptions" optionLabel="label"
                                    optionValue="value" placeholder="Pilih Pekerjaan" class="w-full"
                                    :class="{ 'border-red-500': form.errors.job }" :disabled="form.processing" />
                                <div v-if="form.errors.job" class="text-sm text-red-400">
                                    {{ form.errors.job }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label for="communication_barrier" class="font-semibold">Hambatan Komunikasi
                                    (Opsional)</label>
                                <InputText id="communication_barrier" v-model="form.communication_barrier"
                                    class="flex-auto" autocomplete="off"
                                    :class="{ 'border-red-500': form.errors.communication_barrier }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.communication_barrier" class="text-sm text-red-400">
                                    {{ form.errors.communication_barrier }}
                                </div>
                            </div>

                            <div class="flex flex-col md:col-span-2">
                                <label for="disability_status" class="font-semibold">Status Disabilitas
                                    (Opsional)</label>
                                <InputText id="disability_status" v-model="form.disability_status" class="flex-auto"
                                    autocomplete="off" :class="{ 'border-red-500': form.errors.disability_status }"
                                    :disabled="form.processing" />
                                <div v-if="form.errors.disability_status" class="text-sm text-red-400">
                                    {{ form.errors.disability_status }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button type="button" label="Batal" severity="secondary" @click="closeDialog"
                            class="border-gray-600 bg-gray-600 text-white hover:bg-gray-700"
                            :disabled="form.processing" />
                        <Button type="submit" label="Update" :loading="form.processing"
                            class="border-blue-600 bg-blue-600 text-white hover:bg-blue-700" />
                    </div>
                </form>
            </div>
        </Dialog>
    </div>
</template>
