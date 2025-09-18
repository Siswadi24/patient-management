<template>
    <AlertRS v-if="showAlert" :title="alertTitle" :message="alertMessage" @close="closeAlert" />
    <ConfirmDialog />

    <Button
        @click="confirmDelete()"
        severity="danger"
        class="justify-start border-red-600 bg-red-600 py-2 text-sm text-white hover:bg-red-700"
        size="small"
        :loading="isDeleting"
        :disabled="isDeleting"
    >
        <Trash class="mr-2 h-4 w-4" />
        Hapus
    </Button>
</template>

<script setup lang="ts">
import AlertRS from '@/components/ui/AlertRS/AlertRS.vue';
import { router } from '@inertiajs/vue3';
import { Trash } from 'lucide-vue-next';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { ref } from 'vue';

const props = defineProps({
    patient: {
        type: Object,
        required: true
    }
});

const confirm = useConfirm();
const showAlert = ref(false);
const alertTitle = ref('');
const alertMessage = ref('');
const isDeleting = ref(false);

const confirmDelete = () => {
    confirm.require({
        message: `Apakah Anda yakin ingin menghapus data pasien "${props.patient.first_name} ${props.patient.last_name}" dengan No. RM ${props.patient.rm_number}?`,
        header: 'Konfirmasi Hapus',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger'
        },
        accept: () => {
            handleDelete();
        },
        reject: () => {
            showInfoAlert();
        }
    });
};

const handleDelete = () => {
    if (isDeleting.value) return;

    isDeleting.value = true;

    router.delete(route('user.patient.deletePatient', props.patient.id), {
        onSuccess: () => {
            isDeleting.value = false;
            showSuccessAlert();

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        },
        onError: (errors) => {
            isDeleting.value = false;
            showErrorAlert(errors);
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

function showSuccessAlert() {
    alertTitle.value = 'Berhasil!';
    alertMessage.value = `Data pasien ${props.patient.first_name} ${props.patient.last_name} berhasil dihapus.`;
    showAlert.value = true;

    setTimeout(() => {
        showAlert.value = false;
    }, 3000);
}

function showInfoAlert() {
    alertTitle.value = 'Dibatalkan';
    alertMessage.value = 'Penghapusan data pasien dibatalkan.';
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
        errorMessages = 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.';
    }

    alertMessage.value = errorMessages.trim();
    showAlert.value = true;

    setTimeout(() => {
        showAlert.value = false;
    }, 5000);
}

function closeAlert() {
    showAlert.value = false;
}
</script>
