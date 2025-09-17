<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Edit } from 'lucide-vue-next';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface Transaction {
    id: number;
    category_transaction_id: number;
    name_transaction: string;
    amount_transaction: number;
    description_transaction: string | null;
    transaction_date: string;
    transaction_time: string;
    photo_transaction: string | null;
    categoryTransaction?: {
        id: number;
        name_category: string;
    };
}

const props = defineProps<{
    transaction: Transaction;
    categories: any[];
}>();

const visible = ref(false);
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');

const form = useForm({
    category_transaction_id: null as number | null,
    name_transaction: '',
    amount_transaction: null as number | null,
    description_transaction: '',
    transaction_date: new Date(),
    transaction_time: new Date(),
    photo_transaction: null as File | null,
});

async function openEditDialog() {
    try {
        // Fetch transaction data
        const response = await axios.get(route('user.transactions.show', props.transaction.id));
        const transactionData = response.data;

        // Populate form with existing data
        form.category_transaction_id = transactionData.category_transaction_id;
        form.name_transaction = transactionData.name_transaction;
        form.amount_transaction = transactionData.amount_transaction;
        form.description_transaction = transactionData.description_transaction || '';

        // Parse date and time
        form.transaction_date = new Date(transactionData.transaction_date);

        // Parse time - handle HH:MM:SS format
        const timeParts = transactionData.transaction_time.split(':');
        const timeDate = new Date();
        timeDate.setHours(parseInt(timeParts[0]), parseInt(timeParts[1]), 0, 0);
        form.transaction_time = timeDate;

        visible.value = true;
    } catch (error) {
        showErrorAlert(['Gagal memuat data transaksi']);
    }
}

function handleSubmit() {
    // Prevent modal from closing during submission
    if (form.processing) return;

    // Format date to YYYY-MM-DD
    let formattedDate = '';
    if (form.transaction_date instanceof Date) {
        const year = form.transaction_date.getFullYear();
        const month = (form.transaction_date.getMonth() + 1).toString().padStart(2, '0');
        const day = form.transaction_date.getDate().toString().padStart(2, '0');
        formattedDate = `${year}-${month}-${day}`;
    }

    // Format time to HH:MM
    let formattedTime = '';
    if (form.transaction_time instanceof Date) {
        const hours = form.transaction_time.getHours().toString().padStart(2, '0');
        const minutes = form.transaction_time.getMinutes().toString().padStart(2, '0');
        formattedTime = `${hours}:${minutes}`;
    }

    // Create form data object with _method for Laravel method spoofing
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('category_transaction_id', form.category_transaction_id?.toString() || '');
    formData.append('name_transaction', form.name_transaction || '');
    formData.append('amount_transaction', form.amount_transaction?.toString() || '');
    formData.append('description_transaction', form.description_transaction || '');
    formData.append('transaction_date', formattedDate);
    formData.append('transaction_time', formattedTime);

    // Only append photo if a new file is selected
    if (form.photo_transaction) {
        formData.append('photo_transaction', form.photo_transaction);
    }

    // Use POST with method spoofing for file uploads
    router.post(route('user.transactions.update', props.transaction.id), formData, {
        forceFormData: true,
        onSuccess: () => {
            visible.value = false;
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
    alertMessage.value = 'Transaksi berhasil diperbarui.';
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

// Prevent modal from closing when clicking inside
function handleModalClick(event: Event) {
    event.stopPropagation();
}
</script>

<template>
    <div>
        <!-- AlertRS Component - Position outside modal -->
        <AlertRS v-if="showAlert && !visible" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button @click="openEditDialog" size="small" class="border-yellow-600 bg-yellow-600 text-white hover:bg-yellow-700">
            <Edit class="h-4 w-4" />
            <span>Edit</span>
        </Button>

        <Dialog
            v-model:visible="visible"
            modal
            header="Edit Transaksi"
            :style="{ width: '40rem' }"
            class="dark"
            :closable="!form.processing"
            @hide="closeDialog"
        >
            <template #header>
                <h3 class="text-lg font-semibold">Edit Transaksi</h3>
            </template>

            <div @click="handleModalClick">
                <span class="text-surface-500 dark:text-surface-400 mb-8 block">Edit transaksi yang sudah ada.</span>

                <form @submit.prevent="handleSubmit">
                    <!-- Category Selection -->
                    <div class="mb-4 flex flex-col">
                        <label for="category_transaction_id" class="font-semibold">Kategori Transaksi</label>
                        <Select
                            id="category_transaction_id"
                            v-model="form.category_transaction_id"
                            :options="props.categories"
                            optionLabel="name_category"
                            optionValue="id"
                            placeholder="Pilih Kategori"
                            class="w-full"
                            :class="{ 'border-red-500': form.errors.category_transaction_id }"
                            :disabled="form.processing"
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
                            class="flex-auto"
                            autocomplete="off"
                            :class="{ 'border-red-500': form.errors.name_transaction }"
                            :disabled="form.processing"
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
                            :disabled="form.processing"
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
                                :disabled="form.processing"
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
                                :disabled="form.processing"
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
                            class="flex-auto"
                            rows="3"
                            :class="{ 'border-red-500': form.errors.description_transaction }"
                            :disabled="form.processing"
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
                            :maxFileSize="5000000"
                            @select="onFileSelect"
                            chooseLabel="Pilih Foto Baru"
                            class="w-full"
                            :class="{ 'border-red-500': form.errors.photo_transaction }"
                            :disabled="form.processing"
                        />
                        <div v-if="form.errors.photo_transaction" class="text-sm text-red-400">
                            {{ form.errors.photo_transaction }}
                        </div>
                        <div v-if="props.transaction.photo_transaction" class="mt-2 text-sm text-gray-400">
                            Foto saat ini: {{ props.transaction.photo_transaction }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button
                            type="button"
                            label="Cancel"
                            severity="secondary"
                            @click="closeDialog"
                            class="border-gray-600 bg-gray-600 text-white hover:bg-gray-700"
                            :disabled="form.processing"
                        />
                        <Button
                            type="submit"
                            label="Update"
                            :loading="form.processing"
                            class="border-blue-600 bg-blue-600 text-white hover:bg-blue-700"
                        />
                    </div>
                </form>
            </div>
        </Dialog>
    </div>
</template>
