<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Check, X } from 'lucide-vue-next';
import Button from 'primevue/button';
import { computed } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Password validation states
const passwordValidation = computed(() => {
    const password = form.password;
    return {
        hasNumber: /\d/.test(password),
        hasUppercase: /[A-Z]/.test(password),
        hasLowercase: /[a-z]/.test(password),
        minLength: password.length >= 7,
        isValid: /\d/.test(password) && /[A-Z]/.test(password) && /[a-z]/.test(password) && password.length >= 7
    };
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex">
        <!-- Left Section - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-8 py-12 lg:px-16 xl:px-20 bg-white dark:bg-gray-900">
            <div class="max-w-md w-full mx-auto">
                <!-- Logo/Brand -->
                <div class="mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-[#10B981] rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">R</span>
                        </div>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white">RS</span>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Selamat Datang Kembali</h1>
                    <p class="text-gray-600 dark:text-gray-300">Masukkan email dan kata sandi Anda untuk mengakses akun Anda.</p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-md">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ status }}</p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <Label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="sellostore@company.com"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <Label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</Label>
                            <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                Lupa Kata Sandi?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Sellostore."
                            :class="[
                                'w-full px-4 py-3 border rounded-lg focus:ring-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400',
                                form.password && !passwordValidation.isValid 
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500 dark:border-red-600' 
                                    : form.password && passwordValidation.isValid 
                                        ? 'border-green-300 focus:border-green-500 focus:ring-green-500 dark:border-green-600'
                                        : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600'
                            ]"
                        />
                        
                        <!-- Password Requirements -->
                        <div v-if="form.password" class="mt-2 space-y-1">
                            <div class="flex items-center space-x-2 text-xs">
                                <Check v-if="passwordValidation.hasNumber" class="h-3 w-3 text-green-500" />
                                <X v-else class="h-3 w-3 text-red-500" />
                                <span :class="passwordValidation.hasNumber ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                    Mengandung angka
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <Check v-if="passwordValidation.hasUppercase" class="h-3 w-3 text-green-500" />
                                <X v-else class="h-3 w-3 text-red-500" />
                                <span :class="passwordValidation.hasUppercase ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                    Mengandung huruf kapital
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <Check v-if="passwordValidation.hasLowercase" class="h-3 w-3 text-green-500" />
                                <X v-else class="h-3 w-3 text-red-500" />
                                <span :class="passwordValidation.hasLowercase ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                    Mengandung huruf non kapital
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <Check v-if="passwordValidation.minLength" class="h-3 w-3 text-green-500" />
                                <X v-else class="h-3 w-3 text-red-500" />
                                <span :class="passwordValidation.minLength ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                    Minimal password 7 huruf
                                </span>
                            </div>
                        </div>
                        
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <Checkbox id="remember" v-model="form.remember" class="mr-3" />
                        <Label for="remember" class="text-sm text-gray-700 dark:text-gray-300">Remember Me</Label>
                    </div>

                    <!-- Login Button -->
                    <Button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing || (form.password && !passwordValidation.isValid)"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Log In
                    </Button>

                    <!-- Register Link -->
                    <div class="text-center pt-4">
                        <span class="text-gray-600 dark:text-gray-400">Tidak punya akun? </span>
                        <TextLink :href="route('register')" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            Daftar Sekarang.
                        </TextLink>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Copyright © 2025 RS All Rights Reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Section - Hero/Promotional Content -->
        <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-green-600 to-green-800 dark:from-green-700 dark:to-green-900 relative overflow-hidden">
            <div class="flex flex-col justify-center px-12 xl:px-16 text-white relative z-10">
                <h2 class="text-4xl font-bold mb-4">Kelola pasien dengan Lebih Mudah.</h2>
                <p class="text-green-100 dark:text-green-200 text-lg mb-8">Daftar sekarang untuk mulai mencatat, melacak, dan menganalisis pasien Anda dengan sistem yang rapi dan terorganisir.</p>

                <!-- Dashboard Preview Image/Mockup -->
                <div class="bg-white/10 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/20 dark:border-white/10">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <!-- Revenue Card -->
                        <div class="bg-white/20 dark:bg-white/10 rounded-xl p-4">
                            <p class="text-green-200 dark:text-green-300 text-sm mb-1">Total Pasien</p>
                            <p class="text-2xl font-bold">189,374</p>
                        </div>
                        <!-- Sales Card -->
                        <div class="bg-white/20 dark:bg-white/10 rounded-xl p-4">
                            <p class="text-green-200 dark:text-green-300 text-sm mb-1">Overview Pasien</p>
                            <p class="text-2xl font-bold">00:01:30</p>
                        </div>
                    </div>

                    <!-- Chart Area -->
                    <div class="bg-white/10 dark:bg-white/5 rounded-xl p-4 mb-4">
                        <div class="flex items-end space-x-2 h-20">
                            <div class="bg-white/40 dark:bg-white/30 w-4 h-8 rounded-sm"></div>
                            <div class="bg-white/40 dark:bg-white/30 w-4 h-12 rounded-sm"></div>
                            <div class="bg-white/60 dark:bg-white/50 w-4 h-16 rounded-sm"></div>
                            <div class="bg-white/40 dark:bg-white/30 w-4 h-10 rounded-sm"></div>
                            <div class="bg-white/40 dark:bg-white/30 w-4 h-14 rounded-sm"></div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-green-200 dark:text-green-300">Jam Pelayanan</span>
                            <span>6.248 Jam</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-green-200 dark:text-green-300">Pasien Dilayani</span>
                            <span>6.248 Pasien</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background decoration -->
            <div class="absolute inset-0 bg-gradient-to-t from-green-900/50 dark:from-green-950/70 to-transparent"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 dark:bg-white/3 rounded-full -translate-y-48 translate-x-48"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 dark:bg-white/3 rounded-full translate-y-32 -translate-x-32"></div>
        </div>
    </div>
</template>
