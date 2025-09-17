<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, BadgeDollarSign, Layers, Wallet, PersonStanding } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/user/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Patients',
        href: '/user/patient',
        icon: PersonStanding,
    },
    {
        title: 'Financial',
        icon: Wallet,
        items: [
            {
                title: 'Categories Transactions',
                href: '/user/categories',
                icon: Layers,
            },
            {
                title: 'Transactions',
                href: '/user/transactions',
                icon: BadgeDollarSign,
            }
        ]
    }
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="dark">
        <SidebarHeader class="bg-gray-900 border-b border-gray-800">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="gap-0 p-0 hover:bg-gray-800">
                        <Link :href="route('user.dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-gray-900">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="bg-gray-900 border-t border-gray-800">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
