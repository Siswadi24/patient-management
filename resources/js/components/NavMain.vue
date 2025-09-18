<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { computed } from 'vue'

const props = defineProps<{
    items: NavItem[]
}>()

const page = usePage()

// Get current URL path without query parameters
const currentPath = computed(() => {
    if (typeof window !== 'undefined') {
        return window.location.pathname;
    }
    const url = new URL(page.url, 'http://localhost')
    return url.pathname
})

// Check if item is active based on current path
const isItemActive = (item: NavItem) => {
    if (item.href) {
        // Use startsWith to match base route regardless of query parameters
        return currentPath.value.startsWith(item.href)
    }
    return false
}

// Check if any sub-item is active
const hasActiveSubItem = (items: NavItem[]) => {
    return items.some(item => isItemActive(item))
}
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <SidebarMenuItem v-if="!item.items">
                    <SidebarMenuButton :as-child="true" :isActive="isItemActive(item)">
                        <Link :href="item.href!">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <Collapsible
                    v-else
                    :key="item.title"
                    as-child
                    :default-open="hasActiveSubItem(item.items)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :tooltip="item.title"
                                :isActive="hasActiveSubItem(item.items)"
                            >
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton
                                        :as-child="true"
                                        :isActive="isItemActive(subItem)"
                                    >
                                        <Link :href="subItem.href!">
                                            <component :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
