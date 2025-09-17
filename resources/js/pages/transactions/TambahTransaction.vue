<template>
    <div class="card flex justify-center">
        <!-- AlertRS Component -->
        <AlertRS v-if="showAlert" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button label="Tambah Transaksi" @click="visible = true" class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" />

        <Dialog v-model:visible="visible" modal header="Tambah Transaksi" :style="{ width: '40rem' }" class="dark">
            <span class="text-surface-500 dark:text-surface-400 mb-8 block">Tambahkan transaksi baru ke dalam sistem.</span>

            <form @submit.prevent="handleSubmit">
                <!-- Category Selection -->
                <div class="mb-4 flex flex-col">
                    <label for="category_transaction_id" class="font-semibold">Kategori Transaksi</label>
                    <Select
                        id="category_transaction_id"
                        v-model="form.category_transaction_id"
                        :options="props.categories"
                        optionLabel="name_category"
                        showClear
                        optionValue="id"
                        placeholder="Pilih Kategori"
                        class="w-full border-gray-600 bg-gray-700 text-gray-100"
                        :class="{ 'border-red-500': form.errors.category_transaction_id }"
                    />
                    <div v-if="form.errors.category_transaction_id" class="text-sm text-red-400">
                        {{ form.errors.category_transaction_id }}
                    </div>
                </div>

                <!-- Transaction Name -->
                <div class="mb-4 flex flex-col">
                    <label for="name_transaction" class="font-semibold">Nama Transaksi</label>
                    <InputText
                        id="name_transaction"
                        v-model="form.name_transaction"
                        class="flex-auto border-gray-600 bg-gray-700 text-gray-100"
                        autocomplete="off"
                        :class="{ 'border-red-500': form.errors.name_transaction }"
                    />
                    <div v-if="form.errors.name_transaction" class="text-sm text-red-400">
                        {{ form.errors.name_transaction }}
                    </div>
                </div>

                <!-- Amount -->
                <div class="mb-4 flex flex-col">
                    <label for="amount_transaction" class="font-semibold">Jumlah Transaksi</label>
                    <InputNumber
                        id="amount_transaction"
                        v-model="form.amount_transaction"
                        mode="currency"
                        currency="IDR"
                        locale="id-ID"
                        class="flex-auto"
                        :class="{ 'border-red-500': form.errors.amount_transaction }"
                    />
                    <div v-if="form.errors.amount_transaction" class="text-sm text-red-400">
                        {{ form.errors.amount_transaction }}
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label for="transaction_date" class="font-semibold">Tanggal Transaksi</label>
                        <Calendar
                            id="transaction_date"
                            v-model="form.transaction_date"
                            dateFormat="dd/mm/yy"
                            showIcon
                            class="w-full"
                            :class="{ 'border-red-500': form.errors.transaction_date }"
                        />
                        <div v-if="form.errors.transaction_date" class="text-sm text-red-400">
                            {{ form.errors.transaction_date }}
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label for="transaction_time" class="font-semibold">Waktu Transaksi</label>
                        <Calendar
                            id="transaction_time"
                            v-model="form.transaction_time"
                            timeOnly
                            showIcon
                            class="w-full"
                            :class="{ 'border-red-500': form.errors.transaction_time }"
                        />
                        <div v-if="form.errors.transaction_time" class="text-sm text-red-400">
                            {{ form.errors.transaction_time }}
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4 flex flex-col">
                    <label for="description_transaction" class="font-semibold">Deskripsi Transaksi</label>
                    <Textarea
                        id="description_transaction"
                        v-model="form.description_transaction"
                        class="flex-auto border-gray-600 bg-gray-700 text-gray-100"
                        rows="3"
                        :class="{ 'border-red-500': form.errors.description_transaction }"
                    />
                    <div v-if="form.errors.description_transaction" class="text-sm text-red-400">
                        {{ form.errors.description_transaction }}
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="mb-8 flex flex-col">
                    <label for="photo_transaction" class="font-semibold">Foto Transaksi (Opsional)</label>
                    <FileUpload
                        mode="basic"
                        accept="image/*"
                        :maxFileSize="2000000"
                        @select="onFileSelect"
                        chooseLabel="Pilih Foto"
                        class="w-full"
                        :class="{ 'border-red-500': form.errors.photo_transaction }"
                    />
                    <div v-if="form.errors.photo_transaction" class="text-sm text-red-400">
                        {{ form.errors.photo_transaction }}
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        @click="closeDialog"
                        class="border-gray-600 bg-gray-600 text-white hover:bg-gray-700"
                    />
                    <Button type="submit" label="Save" :loading="form.processing" class="border-blue-600 bg-blue-600 text-white hover:bg-blue-700" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

const props = defineProps<{
    categories: any[];
}>();

const visible = ref(false);
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');

const form = useForm({
    category_transaction_id: null,
    name_transaction: '',
    amount_transaction: null,
    description_transaction: '',
    transaction_date: new Date(),
    transaction_time: new Date(),
    photo_transaction: null,
});

function handleSubmit() {
    // Validate required fields before sending
    if (!form.category_transaction_id) {
        showErrorAlert({ category_transaction_id: ['Kategori Transaksi harus dipilih'] });
        return;
    }

    if (!form.name_transaction.trim()) {
        showErrorAlert({ name_transaction: ['Nama Transaksi harus diisi'] });
        return;
    }

    if (!form.amount_transaction || form.amount_transaction <= 0) {
        showErrorAlert({ amount_transaction: ['Jumlah Transaksi harus diisi dan lebih dari 0'] });
        return;
    }

    // Format date to YYYY-MM-DD
    let formattedDate = '';
    if (form.transaction_date instanceof Date) {
        // Use local date without timezone conversion
        const year = form.transaction_date.getFullYear();
        const month = (form.transaction_date.getMonth() + 1).toString().padStart(2, '0');
        const day = form.transaction_date.getDate().toString().padStart(2, '0');
        formattedDate = `${year}-${month}-${day}`;
    } else if (typeof form.transaction_date === 'string') {
        formattedDate = new Date(form.transaction_date).toISOString().split('T')[0];
    }

    // Format time to HH:MM - more robust approach
    let formattedTime = '';
    if (form.transaction_time instanceof Date) {
        // Get local time without timezone conversion
        const hours = form.transaction_time.getHours().toString().padStart(2, '0');
        const minutes = form.transaction_time.getMinutes().toString().padStart(2, '0');
        formattedTime = `${hours}:${minutes}`;
    } else if (typeof form.transaction_time === 'string') {
        // Handle different string formats
        let timeStr = form.transaction_time;

        // If it's an ISO datetime string, extract just time
        if (timeStr.includes('T')) {
            const dateObj = new Date(timeStr);
            const hours = dateObj.getHours().toString().padStart(2, '0');
            const minutes = dateObj.getMinutes().toString().padStart(2, '0');
            formattedTime = `${hours}:${minutes}`;
        }
        // If it's already in HH:MM or HH:MM:SS format
        else if (timeStr.match(/^\d{1,2}:\d{2}/)) {
            const timeParts = timeStr.split(':');
            const hours = timeParts[0].padStart(2, '0');
            const minutes = timeParts[1].padStart(2, '0');
            formattedTime = `${hours}:${minutes}`;
        }
        // Default case
        else {
            // Try to parse as date and extract time
            try {
                const dateObj = new Date(timeStr);
                if (!isNaN(dateObj.getTime())) {
                    const hours = dateObj.getHours().toString().padStart(2, '0');
                    const minutes = dateObj.getMinutes().toString().padStart(2, '0');
                    formattedTime = `${hours}:${minutes}`;
                } else {
                    // Fallback to current time
                    const now = new Date();
                    formattedTime = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
                }
            } catch (e) {
                // Fallback to current time
                const now = new Date();
                formattedTime = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
            }
        }
    }

    // Validate that formattedTime is in correct format
    if (!formattedTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
        const now = new Date();
        formattedTime = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    }

    console.log('Formatted time being sent:', formattedTime);
    console.log('Formatted date being sent:', formattedDate);
    console.log('All form data:', {
        category_transaction_id: form.category_transaction_id,
        name_transaction: form.name_transaction,
        amount_transaction: form.amount_transaction,
        description_transaction: form.description_transaction,
        transaction_date: formattedDate,
        transaction_time: formattedTime
    });

    // Use regular form submission instead of FormData for simpler debugging
    form.transform((data) => ({
        category_transaction_id: form.category_transaction_id,
        name_transaction: form.name_transaction,
        amount_transaction: form.amount_transaction,
        description_transaction: form.description_transaction || '',
        transaction_date: formattedDate,
        transaction_time: formattedTime,
        photo_transaction: form.photo_transaction
    })).post(route('user.transactions.store'), {
        onSuccess: () => {
            closeDialog();
            resetForm();
            showSuccessAlert();
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
            showErrorAlert(errors);
        },
    });
}

function onFileSelect(event: any) {
    const file = event.files[0];
    if (file) {
        form.photo_transaction = file;
    }
}

function showSuccessAlert() {
    alertTitle.value = 'Berhasil!';
    alertMessage.value = 'Transaksi berhasil ditambahkan.';
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
        errorMessages = 'Terjadi kesalahan saat menyimpan data.';
    }

    alertMessage.value = errorMessages.trim();
    showAlert.value = true;
}

function closeAlert() {
    showAlert.value = false;
}

function closeDialog() {
    visible.value = false;
}

function resetForm() {
    form.reset();
    form.clearErrors();
}
</script>

<style scoped></style>
