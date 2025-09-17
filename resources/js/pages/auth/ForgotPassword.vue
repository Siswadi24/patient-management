<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Button from 'primevue/button';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot password" />

    <div class="flex min-h-screen">
        <!-- Left Section - Forgot Password Form -->
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
                <div class="mb-8">
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">Lupa Kata Sandi?</h1>
                    <p class="text-gray-600 dark:text-gray-300">Masukkan email Anda untuk menerima kode OTP reset password.</p>
                </div>
                <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 rounded-md">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ status }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="off"
                            v-model="form.email"
                            autofocus
                            placeholder="contoh:mail@example.com"
                            required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-3 font-medium text-white transition-colors duration-200"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Kirim Kode OTP
                    </Button>

                    <!-- Login Link -->
                    <div class="pt-4 text-center">
                        <span class="text-gray-600 dark:text-gray-400">Ingat kata sandi Anda? </span>
                        <TextLink :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            Masuk Sekarang.
                        </TextLink>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-8">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Copyright © 2025 RS All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
</template>
