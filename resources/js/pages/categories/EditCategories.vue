<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Edit } from 'lucide-vue-next';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

interface Category {
    id: number;
    name_category: string;
    description: string | null;
    is_active: boolean;
}

const props = defineProps<{
    category: Category;
}>();

const visible = ref(false);
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');

const form = useForm({
    name_category: '',
    description: '',
    is_active: null as boolean | null,
});

const statusOptions = ref([
    { name: 'Active', code: true },
    { name: 'Inactive', code: false },
]);

async function openEditDialog() {
    try {
        // Fetch category data
        const response = await axios.get(route('user.categories.show', props.category.id));
        const categoryData = response.data;

        // Populate form with existing data
        form.name_category = categoryData.name_category;
        form.description = categoryData.description || '';
        // Convert 1/0 to boolean for proper selection
        form.is_active = Boolean(categoryData.is_active);

        visible.value = true;
    } catch (error) {
        showErrorAlert(['Gagal memuat data kategori']);
    }
}

function handleSubmit() {
    form.put(route('user.categories.update', props.category.id), {
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
    alertMessage.value = 'Kategori berhasil diperbarui.';
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
    visible.value = false;
}

function resetForm() {
    form.reset();
    form.clearErrors();
}

function closeAlert() {
    showAlert.value = false;
}
</script>

<template>
    <div>
        <!-- AlertRS Component -->
        <AlertRS v-if="showAlert" :title="alertTitle" :message="alertMessage" @close="closeAlert" />

        <Button @click="openEditDialog" size="small" class="border-yellow-600 bg-yellow-600 text-white hover:bg-yellow-700">
            <Edit />
        </Button>

        <Dialog v-model:visible="visible" modal header="Edit Kategori" :style="{ width: '30rem' }" class="dark">
            <span class="text-surface-500 dark:text-surface-400 mb-8 block">Edit kategori yang sudah ada.</span>

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
                        class="w-full border-gray-600 bg-gray-700 text-gray-100"
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
                    <Button
                        type="submit"
                        label="Update"
                        :loading="form.processing"
                        class="border-blue-600 bg-blue-600 text-white hover:bg-blue-700"
                    />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<style scoped></style>
