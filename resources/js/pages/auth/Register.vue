<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Button from 'primevue/button';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="flex min-h-screen">
        <!-- Left Section - Register Form -->
        <div class="flex flex-1 flex-col justify-center bg-white px-8 py-12 lg:px-16 xl:px-20 dark:bg-gray-900">
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
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">Buat Akun Anda</h1>
                    <p class="text-gray-600 dark:text-gray-300">Masukkan detail Anda di bawah ini untuk membuat akun baru.</p>
                </div>

                <!-- Register Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <Label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            autocomplete="name"
                            v-model="form.name"
                            placeholder="John Doe"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <Label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="john@company.com"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <Label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            v-model="form.password"
                            placeholder="Create a strong password"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <Label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Confirm Password</Label
                        >
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            v-model="form.password_confirmation"
                            placeholder="Confirm your password"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-1" />
                    </div>

                    <!-- Register Button -->
                    <Button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 px-4 py-3 font-medium text-white transition-colors duration-200 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Create Account
                    </Button>

                    <!-- Login Link -->
                    <div class="pt-4 text-center">
                        <span class="text-gray-600 dark:text-gray-400">Sudah punya akun? </span>
                        <TextLink
                            :href="route('login')"
                            class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            Masuk Sekarang.
                        </TextLink>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-12 border-t border-gray-200 pt-8 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Copyright © 2025 RS All Rights Reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Section - Hero/Promotional Content -->
        <div
            class="relative hidden overflow-hidden bg-gradient-to-br from-green-600 to-green-800 lg:flex lg:flex-1 dark:from-green-700 dark:to-green-900"
        >
            <div class="relative z-10 flex flex-col justify-center px-12 text-white xl:px-16">
                <h2 class="mb-4 text-4xl font-bold">Kendalikan Pasien Anda dengan Lebih Cerdas.</h2>
                <p class="mb-8 text-lg text-green-100 dark:text-green-200">
                    Daftar sekarang untuk mulai mencatat, melacak, dan menganalisis pasien Anda dengan sistem secara otomatis dan terorganisir.
                </p>

                <!-- Features Preview -->
                <div class="space-y-6">
                    <!-- Feature 1 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Tracking Pasien Harian</h3>
                            <p class="text-sm text-green-200 dark:text-green-300">Catat dan lacak semua pasien dengan mudah.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Laporan Kesehatan Otomatis</h3>
                            <p class="text-sm text-green-200 dark:text-green-300">Dapatkan insight jelas melalui grafik & laporan real-time.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 dark:bg-white/10">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Aman & Terpercaya</h3>
                            <p class="text-sm text-green-200 dark:text-green-300">Data kesehatan Anda terlindungi dengan enkripsi tingkat tinggi.</p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-12 border-t border-green-500/30 pt-8">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="text-2xl font-bold">50K+</div>
                            <div class="text-sm text-green-200 dark:text-green-300">Transaksi Tercatat</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">15K+</div>
                            <div class="text-sm text-green-200 dark:text-green-300">Pengguna Aktif</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">100%</div>
                            <div class="text-sm text-green-200 dark:text-green-300">Kontrol Akses Pribadi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background decoration -->
            <div class="absolute inset-0 bg-gradient-to-t from-green-900/50 to-transparent dark:from-green-950/70"></div>
            <div class="absolute top-0 right-0 h-96 w-96 translate-x-48 -translate-y-48 rounded-full bg-white/5 dark:bg-white/3"></div>
            <div class="absolute bottom-0 left-0 h-64 w-64 -translate-x-32 translate-y-32 rounded-full bg-white/5 dark:bg-white/3"></div>
        </div>
    </div>
</template>
