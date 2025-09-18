<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { ref } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
});

const avatarForm = useForm({
    avatar: null as File | null,
});

const avatarPreview = ref<string>('');
const fileInputRef = ref<HTMLInputElement>();

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const handleAvatarSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        avatarForm.avatar = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const submitAvatar = () => {
    if (!avatarForm.avatar) return;

    avatarForm.post(route('profile.avatar.update'), {
        preserveScroll: true,
        onSuccess: (page) => {
            avatarPreview.value = '';
            avatarForm.reset();
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }

            if (page.props.auth?.user) {
                Object.assign(user, page.props.auth.user);
            } else {
                window.location.reload();
            }
        },
    });
};

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const getAvatarUrl = () => {
    if (avatarPreview.value) {
        return avatarPreview.value;
    }
    if (user.photo) {
        return `/storage/${user.photo}?t=${Date.now()}`;
    }

    return user.photo_url || `https://api.dicebear.com/6.x/identicon/svg?seed=${user.name}`;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex flex-col space-y-4">
                    <HeadingSmall title="Profile photo" description="Update your profile photo" />

                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <img
                                :src="getAvatarUrl()"
                                :alt="user.name"
                                class="h-24 w-24 rounded-full object-cover border-2 border-border"
                            />
                            <button
                                @click="triggerFileInput"
                                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 hover:opacity-100 transition-opacity"
                            >
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-1 space-y-3">
                            <input
                                ref="fileInputRef"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleAvatarSelect"
                            />

                            <div class="flex items-center gap-3">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="triggerFileInput"
                                    :disabled="avatarForm.processing"
                                >
                                    Choose photo
                                </Button>

                                <Button
                                    v-if="avatarForm.avatar"
                                    @click="submitAvatar"
                                    :disabled="avatarForm.processing"
                                >
                                    Upload
                                </Button>
                            </div>

                            <p class="text-sm text-muted-foreground">
                                JPG, PNG or GIF. Max size 2MB.
                            </p>

                            <InputError :message="avatarForm.errors.avatar" />

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-show="avatarForm.recentlySuccessful" class="text-sm text-green-600">Photo updated.</p>
                            </Transition>
                        </div>
                    </div>
                </div>

                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
