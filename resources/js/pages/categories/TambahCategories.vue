<template>
    <div class="card flex justify-center">
        <!-- AlertRS Component -->
        <AlertRS v-if="showAlert" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button label="Tambah Kategori" @click="visible = true" class="w-full border-blue-600 bg-blue-600 text-white hover:bg-blue-700 md:w-auto" />
        <Dialog v-model:visible="visible" modal header="Tambah Kategori" :style="{ width: '30rem' }" class="dark">
            <span class="text-surface-500 dark:text-surface-400 mb-8 block">Tambahkan Kategori Yang Kamu Inginkan.</span>

            <form @submit.prevent="handleSubmit">
                <div class="mb-4 flex flex-col">
                    <label for="name_category" class="font-semibold">Nama Kategori</label>
                    <InputText
                        id="name_category"
                        v-model="form.name_category"
                        class="flex-auto border-gray-600 bg-gray-700 text-gray-100"
                        autocomplete="off"
                        :class="{ 'border-red-500': form.errors.name_category }"
                    />
                    <div v-if="form.errors.name_category" class="text-sm text-red-400">
                        {{ form.errors.name_category }}
                    </div>
                </div>

                <div class="mb-4 flex flex-col">
                    <label for="description" class="font-semibold">Deskripsi Kategori</label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        class="flex-auto border-gray-600 bg-gray-700 text-gray-100"
                        autocomplete="off"
                        :class="{ 'border-red-500': form.errors.description }"
                    />
                    <div v-if="form.errors.description" class="text-sm text-red-400">
                        {{ form.errors.description }}
                    </div>
                </div>

                <div class="mb-8 flex flex-col">
                    <label for="is_active" class="font-semibold">Status Kategori</label>
                    <Select
                        id="is_active"
                        v-model="form.is_active"
                        :options="statusOptions"
                        showClear
                        optionLabel="name"
                        optionValue="code"
                        placeholder="Pilih Status"
                        class="w-full bg-amber-400"
                        :class="{ 'border-red-500': form.errors.is_active }"
                    />
                    <div v-if="form.errors.is_active" class="text-sm text-red-400">
                        {{ form.errors.is_active }}
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
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

const visible = ref(false);
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');

const form = useForm({
    name_category: '',
    description: '',
    is_active: null,
});

const statusOptions = ref([
    { name: 'Active', code: true },
    { name: 'Inactive', code: false },
]);

function handleSubmit() {
    form.post(route('user.categories.store'), {
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

function showSuccessAlert() {
    alertTitle.value = 'Berhasil!';
    alertMessage.value = 'Kategori berhasil ditambahkan.';
    showAlert.value = true;
}

function showErrorAlert(errors: any) {
    alertTitle.value = 'Error!';

    // Format error messages
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
