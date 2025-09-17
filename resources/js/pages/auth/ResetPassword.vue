<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Button from 'primevue/button';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Reset password" />

    <div class="flex min-h-screen">
        <!-- Left Section - Reset Password Form -->
        <div class="flex flex-1 flex-col justify-center bg-white dark:bg-gray-900 px-8 py-12 lg:px-16 xl:px-20">
            <div class="mx-auto w-full max-w-md">
                <!-- Logo/Brand -->
                <div class="mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#10B981]">
                            <span class="text-sm font-bold text-white">R</span>
                        </div>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white">RS</span>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="mb-8">
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">Reset Password</h1>
                    <p class="text-gray-600 dark:text-gray-300">Masukkan password baru Anda di bawah ini.</p>
                </div>

                <!-- Reset Password Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field (readonly) -->
                    <div>
                        <Label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            v-model="form.email"
                            readonly
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <Label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru</Label>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            v-model="form.password"
                            autofocus
                            placeholder="Masukkan password baru"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <Label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            v-model="form.password_confirmation"
                            placeholder="Konfirmasi password baru"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-3 font-medium text-white transition-colors duration-200"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Reset Password
                    </Button>

                    <!-- Login Link -->
                    <div class="pt-4 text-center">
                        <span class="text-gray-600 dark:text-gray-400">Ingat password Anda? </span>
                        <TextLink :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            Kembali ke Login.
                        </TextLink>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-8">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Copyright © 2025 RS All Rights Reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Section - Hero/Promotional Content -->
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-purple-600 to-purple-800 dark:from-purple-700 dark:to-purple-900 lg:flex lg:flex-1">
            <div class="relative z-10 flex flex-col justify-center px-12 text-white xl:px-16">
                <h2 class="mb-4 text-4xl font-bold">Hampir Selesai!</h2>
                <p class="mb-8 text-lg text-purple-100 dark:text-purple-200">
                    Buat password baru yang kuat untuk melindungi akun Anda dan pastikan keamanan data keuangan tetap terjaga.
                </p>

                <!-- Security Tips -->
                <div class="space-y-6">
                    <!-- Tip 1 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Password yang Kuat</h3>
                            <p class="text-sm text-purple-200 dark:text-purple-300">Gunakan kombinasi huruf, angka, dan simbol minimal 8 karakter.</p>
                        </div>
                    </div>

                    <!-- Tip 2 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-0.257-0.257A6 6 0 1118 8zM2 8a6 6 0 1012 0A6 6 0 002 8zm8-4a4 4 0 100 8 4 4 0 000-8zm-2 4a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Keamanan Berlapis</h3>
                            <p class="text-sm text-purple-200 dark:text-purple-300">Akun Anda dilindungi dengan enkripsi tingkat militer.</p>
                        </div>
                    </div>

                    <!-- Tip 3 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Verifikasi Berhasil</h3>
                            <p class="text-sm text-purple-200 dark:text-purple-300">Identitas Anda telah diverifikasi dengan aman.</p>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="mt-12 border-t border-purple-500/30 pt-8">
                    <h3 class="mb-4 text-lg font-semibold">Syarat Password:</h3>
                    <div class="space-y-2 text-sm text-purple-100 dark:text-purple-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-300 rounded-full"></div>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-300 rounded-full"></div>
                            <span>Kombinasi huruf besar dan kecil</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-300 rounded-full"></div>
                            <span>Setidaknya satu angka</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-300 rounded-full"></div>
                            <span>Karakter khusus (!@#$%^&*)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background decoration -->
            <div class="absolute inset-0 bg-gradient-to-t from-purple-900/50 dark:from-purple-950/70 to-transparent"></div>
            <div class="absolute top-0 right-0 h-96 w-96 translate-x-48 -translate-y-48 rounded-full bg-white/5 dark:bg-white/3"></div>
            <div class="absolute bottom-0 left-0 h-64 w-64 -translate-x-32 translate-y-32 rounded-full bg-white/5 dark:bg-white/3"></div>
        </div>
    </div>
</template>
