<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

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
    birth_date: null,
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
    avatar: null,
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

function handleSubmit() {
    let formattedDate = '';
    if (form.birth_date instanceof Date) {
        const year = form.birth_date.getFullYear();
        const month = (form.birth_date.getMonth() + 1).toString().padStart(2, '0');
        const day = form.birth_date.getDate().toString().padStart(2, '0');
        formattedDate = `${year}-${month}-${day}`;
    } else if (!form.birth_date) {
        formattedDate = '';
    }

    form.transform((data) => ({
        ...data,
        birth_date: formattedDate,
        rm_number: data.rm_number?.trim() || null,
        bpjs_number: data.bpjs_number?.trim() || null,
        communication_barrier: data.communication_barrier?.trim() || null,
        disability_status: data.disability_status?.trim() || null,
        avatar: data.avatar || null,
    })).post(route('user.patient.storePatient'), {
        preserveScroll: true,
        onSuccess: (page) => {
            closeDialog();
            resetForm();
            showSuccessAlert();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        },
        onError: (errors) => {
            if (errors.api) {
                showErrorAlert({ api: [errors.api] });
            } else {
                showErrorAlert(errors);
            }
        },
    });
}

function onFileSelect(event: any) {
    const file = event.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            showErrorAlert({ avatar: ['Ukuran file maksimal 2MB'] });
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showErrorAlert({ avatar: ['Format file harus jpeg, png, jpg, atau gif'] });
            return;
        }

        form.avatar = file;
    }
}

function showSuccessAlert() {
    alertTitle.value = 'Berhasil!';
    alertMessage.value = 'Data pasien berhasil ditambahkan. Halaman akan dimuat ulang untuk menampilkan data terbaru.';
    showAlert.value = true;

    setTimeout(() => {
        showAlert.value = false;
    }, 3000);
}

function showErrorAlert(errors: any) {
    alertTitle.value = 'Error!';

    let errorMessages = '';
    if (typeof errors === 'object' && errors !== null) {
        Object.keys(errors).forEach((key) => {
            if (Array.isArray(errors[key])) {
                errorMessages += errors[key].join(', ') + '\n';
            } else {
                errorMessages += errors[key] + '\n';
            }
        });
    } else {
        errorMessages = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
    }

    alertMessage.value = errorMessages.trim();
    showAlert.value = true;
}

function closeAlert() {
    showAlert.value = false;
}

function closeDialog() {
    visible.value = false;
    form.clearErrors();
}

function resetForm() {
    form.reset();
    form.clearErrors();
    form.birth_date = null;
}

function openDialog() {
    visible.value = true;
}

defineExpose({
    openDialog,
});
</script>

<template>
    <div class="card flex justify-center">
        <AlertRS v-if="showAlert" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button label="Tambah Pasien" @click="visible = true"
            class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" />

        <Dialog v-model:visible="visible" modal header="Tambah Pasien" :style="{ width: '60rem' }" class="dark"
            :closable="!form.processing" :modal="true">
            <span class="text-surface-500 dark:text-surface-400 mb-8 block"> Tambahkan data pasien baru ke dalam sistem.
            </span>

            <form @submit.prevent="handleSubmit">
                <div class="mb-6">
                    <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Informasi Dasar</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col">
                            <label for="rm_number" class="font-semibold">Nomor RM (Opsional)</label>
                            <InputText id="rm_number" v-model="form.rm_number"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                placeholder="Kosongkan untuk generate otomatis" maxlength="6"
                                :class="{ 'border-red-500': form.errors.rm_number }" />
                            <small class="mt-1 text-xs text-gray-400"> Harus 6 digit angka. Jika kosong, akan digenerate
                                otomatis. </small>
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
                            <InputText id="first_name" v-model="form.first_name"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.first_name }" />
                            <div v-if="form.errors.first_name" class="text-sm text-red-400">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="last_name" class="font-semibold">Nama Belakang</label>
                            <InputText id="last_name" v-model="form.last_name"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.last_name }" />
                            <div v-if="form.errors.last_name" class="text-sm text-red-400">
                                {{ form.errors.last_name }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="gender" class="font-semibold">Jenis Kelamin</label>
                            <Select id="gender" v-model="form.gender" :options="genderOptions" optionLabel="label"
                                optionValue="value" placeholder="Pilih Jenis Kelamin"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.gender }" />
                            <div v-if="form.errors.gender" class="text-sm text-red-400">
                                {{ form.errors.gender }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="birth_place" class="font-semibold">Tempat Lahir</label>
                            <InputText id="birth_place" v-model="form.birth_place"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.birth_place }" />
                            <div v-if="form.errors.birth_place" class="text-sm text-red-400">
                                {{ form.errors.birth_place }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="birth_date" class="font-semibold">Tanggal Lahir</label>
                            <Calendar id="birth_date" v-model="form.birth_date" dateFormat="dd/mm/yy" showIcon
                                class="w-full" :class="{ 'border-red-500': form.errors.birth_date }" />
                            <div v-if="form.errors.birth_date" class="text-sm text-red-400">
                                {{ form.errors.birth_date }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="blood_type" class="font-semibold">Golongan Darah</label>
                            <Select id="blood_type" v-model="form.blood_type" :options="bloodTypeOptions"
                                optionLabel="label" optionValue="value" placeholder="Pilih Golongan Darah"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.blood_type }" />
                            <div v-if="form.errors.blood_type" class="text-sm text-red-400">
                                {{ form.errors.blood_type }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Kontak & Identitas</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Nomor Telepon -->
                        <div class="flex flex-col">
                            <label for="phone_number" class="font-semibold">Nomor Telepon</label>
                            <InputText id="phone_number" v-model="form.phone_number"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.phone_number }" />
                            <div v-if="form.errors.phone_number" class="text-sm text-red-400">
                                {{ form.errors.phone_number }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="identity_number" class="font-semibold">Nomor Identitas (KTP)</label>
                            <InputText id="identity_number" v-model="form.identity_number"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.identity_number }" />
                            <div v-if="form.errors.identity_number" class="text-sm text-red-400">
                                {{ form.errors.identity_number }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="bpjs_number" class="font-semibold">Nomor BPJS (Opsional)</label>
                            <InputText id="bpjs_number" v-model="form.bpjs_number"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.bpjs_number }" />
                            <div v-if="form.errors.bpjs_number" class="text-sm text-red-400">
                                {{ form.errors.bpjs_number }}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label for="avatar" class="font-semibold">Foto Pasien (Opsional)</label>
                            <FileUpload mode="basic" accept="image/*" :maxFileSize="2000000" @select="onFileSelect"
                                chooseLabel="Pilih Foto" class="w-full"
                                :class="{ 'border-red-500': form.errors.avatar }" />
                            <div v-if="form.errors.avatar" class="text-sm text-red-400">
                                {{ form.errors.avatar }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="mb-4 border-b border-gray-600 pb-2 text-lg font-semibold">Alamat</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col md:col-span-2">
                            <label for="street_address" class="font-semibold">Alamat Jalan</label>
                            <Textarea id="street_address" v-model="form.street_address"
                                class="flex-auto border-gray-600 bg-gray-700" rows="2"
                                :class="{ 'border-red-500': form.errors.street_address }" />
                            <div v-if="form.errors.street_address" class="text-sm text-red-400">
                                {{ form.errors.street_address }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="city_address" class="font-semibold">Kota</label>
                            <InputText id="city_address" v-model="form.city_address"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.city_address }" />
                            <div v-if="form.errors.city_address" class="text-sm text-red-400">
                                {{ form.errors.city_address }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="state_address" class="font-semibold">Provinsi</label>
                            <InputText id="state_address" v-model="form.state_address"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.state_address }" />
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
                            <InputText id="emergency_full_name" v-model="form.emergency_full_name"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.emergency_full_name }" />
                            <div v-if="form.errors.emergency_full_name" class="text-sm text-red-400">
                                {{ form.errors.emergency_full_name }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="emergency_phone_number" class="font-semibold">Nomor Telepon</label>
                            <InputText id="emergency_phone_number" v-model="form.emergency_phone_number"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.emergency_phone_number }" />
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
                            <InputText id="father_name" v-model="form.father_name"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.father_name }" />
                            <div v-if="form.errors.father_name" class="text-sm text-red-400">
                                {{ form.errors.father_name }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="mother_name" class="font-semibold">Nama Ibu</label>
                            <InputText id="mother_name" v-model="form.mother_name"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.mother_name }" />
                            <div v-if="form.errors.mother_name" class="text-sm text-red-400">
                                {{ form.errors.mother_name }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="married_status" class="font-semibold">Status Pernikahan</label>
                            <Select id="married_status" v-model="form.married_status" :options="marriedStatusOptions"
                                optionLabel="label" optionValue="value" placeholder="Pilih Status Pernikahan"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.married_status }" />
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
                                optionValue="value" placeholder="Pilih Suku/Etnis"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.ethnic }" />
                            <div v-if="form.errors.ethnic" class="text-sm text-red-400">
                                {{ form.errors.ethnic }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="education" class="font-semibold">Pendidikan</label>
                            <Select id="education" v-model="form.education" :options="educationOptions"
                                optionLabel="label" optionValue="value" placeholder="Pilih Pendidikan"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.education }" />
                            <div v-if="form.errors.education" class="text-sm text-red-400">
                                {{ form.errors.education }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="job" class="font-semibold">Pekerjaan</label>
                            <Select id="job" v-model="form.job" :options="jobOptions" optionLabel="label"
                                optionValue="value" placeholder="Pilih Pekerjaan"
                                class="w-full border-gray-600 bg-gray-700"
                                :class="{ 'border-red-500': form.errors.job }" />
                            <div v-if="form.errors.job" class="text-sm text-red-400">
                                {{ form.errors.job }}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label for="communication_barrier" class="font-semibold">Hambatan Komunikasi
                                (Opsional)</label>
                            <InputText id="communication_barrier" v-model="form.communication_barrier"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.communication_barrier }" />
                            <div v-if="form.errors.communication_barrier" class="text-sm text-red-400">
                                {{ form.errors.communication_barrier }}
                            </div>
                        </div>
                        <div class="flex flex-col md:col-span-2">
                            <label for="disability_status" class="font-semibold">Status Disabilitas (Opsional)</label>
                            <InputText id="disability_status" v-model="form.disability_status"
                                class="flex-auto border-gray-600 bg-gray-700" autocomplete="off"
                                :class="{ 'border-red-500': form.errors.disability_status }" />
                            <div v-if="form.errors.disability_status" class="text-sm text-red-400">
                                {{ form.errors.disability_status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="button" label="Batal" severity="secondary" @click="closeDialog"
                        class="border-gray-600 bg-gray-600 text-white hover:bg-gray-700" :disabled="form.processing" />
                    <Button type="submit" :label="form.processing ? 'Menyimpan...' : 'Simpan'"
                        :loading="form.processing" class="border-blue-600 bg-blue-600 text-white hover:bg-blue-700"
                        :disabled="form.processing" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
