<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="w-full max-w-md space-y-8">
            <!-- Alert for messages -->
            <div
                v-if="$page.props.flash?.message"
                class="rounded-md border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/50"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-green-400 dark:text-green-300"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-300">
                            {{ $page.props.flash.message }}
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">Verifikasi Reset Password</h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">Masukkan kode OTP yang telah dikirim ke email {{ email }}</p>
            </div>

            <form @submit.prevent="submitOtp" class="mt-8 space-y-6">
                <div class="flex justify-center text-center">
                    <div>
                        <label for="otp_code" class="sr-only">Kode OTP</label>
                        <InputOtp v-model="form.otp_code" :length="4" integerOnly />
                        <div v-if="errors.otp_code" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ errors.otp_code }}
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="group relative flex w-full cursor-pointer justify-center rounded-md border border-transparent bg-[#10B981] px-4 py-2 text-sm font-medium text-white hover:bg-[#059669] focus:ring-2 focus:ring-[#10B981] focus:ring-offset-2 focus:outline-none disabled:opacity-50 dark:bg-[#10B981] dark:hover:bg-[#059669]"
                    >
                        <span v-if="processing">Memverifikasi...</span>
                        <span v-else>Verifikasi & Lanjutkan</span>
                    </button>
                </div>

                <div class="text-center">
                    <button
                        type="button"
                        @click="resendOtp"
                        :disabled="resendProcessing || resendCountdown > 0"
                        class="cursor-pointer text-sm text-red-600 hover:text-red-500 disabled:opacity-50 dark:text-red-400 dark:hover:text-red-300"
                    >
                        <span v-if="resendCountdown > 0"> Kirim ulang dalam {{ resendCountdown }} detik </span>
                        <span v-else-if="resendProcessing">Mengirim...</span>
                        <span v-else>Kirim ulang kode OTP</span>
                    </button>
                </div>

                <div class="text-center">
                    <a
                        :href="route('password.request')"
                        class="text-sm text-gray-600 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Kembali ke halaman forgot password
                    </a>
                </div>
            </form>

            <div class="mt-12 border-t border-gray-200 pt-8 dark:border-gray-700">
                <p class="text-center text-xs text-gray-500 dark:text-gray-400">Copyright © 2025 RS All Rights Reserved.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputOtp from 'primevue/inputotp';
import { onMounted, onUnmounted, ref } from 'vue';

interface Props {
    email: string;
    errors: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
    email: props.email,
    otp_code: '',
});

const processing = ref<boolean>(false);
const resendProcessing = ref<boolean>(false);
const resendCountdown = ref<number>(0);
let countdownInterval: NodeJS.Timeout | null = null;

const submitOtp = (): void => {
    processing.value = true;
    form.post(route('password.reset.verify.post'), {
        onFinish: () => {
            processing.value = false;
        },
    });
};

const resendOtp = (): void => {
    resendProcessing.value = true;
    useForm({ email: props.email }).post(route('password.reset.resend'), {
        onSuccess: () => {
            resendCountdown.value = 60;
            startCountdown();
        },
        onFinish: () => {
            resendProcessing.value = false;
        },
    });
};

const startCountdown = (): void => {
    countdownInterval = setInterval(() => {
        resendCountdown.value--;
        if (resendCountdown.value <= 0 && countdownInterval) {
            clearInterval(countdownInterval);
        }
    }, 1000);
};

onMounted(() => {
    resendCountdown.value = 60;
    startCountdown();
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>
