<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { Activity, BadgeDollarSign, Calendar, ChevronDown, TrendingUp, ShoppingCart } from 'lucide-vue-next';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const selectedPeriod = ref('monthly');
const periodOptions = [
    { value: 'daily', label: 'Harian' },
    { value: 'weekly', label: 'Mingguan' },
    { value: 'monthly', label: 'Bulanan' },
    { value: 'yearly', label: 'Tahunan' }
];

const dashboardData = ref({
    daily: { total: 0, loading: true },
    weekly: { total: 0, loading: true },
    monthly: { total: 0, loading: true },
    yearly: { total: 0, loading: true }
});

const categoryData = ref({
    categories: [],
    loading: true
});

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const formatCurrencyShort = (amount: number) => {
    if (amount >= 1000000) {
        return `Rp ${(amount / 1000000).toFixed(1)}M`;
    } else if (amount >= 1000) {
        return `Rp ${(amount / 1000).toFixed(1)}K`;
    } else {
        return `Rp ${amount.toLocaleString('id-ID')}`;
    }
};

const fetchDashboardData = async () => {
    try {
        const [daily, weekly, monthly, yearly] = await Promise.all([
            axios.get('/user/dashboard-analytics/daily'),
            axios.get('/user/dashboard-analytics/weekly'),
            axios.get('/user/dashboard-analytics/monthly'),
            axios.get('/user/dashboard-analytics/yearly')
        ]);

        dashboardData.value = {
            daily: { total: daily.data.total, loading: false },
            weekly: { total: weekly.data.total, loading: false },
            monthly: { total: monthly.data.total, loading: false },
            yearly: { total: yearly.data.total, loading: false }
        };
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        dashboardData.value = {
            daily: { total: 0, loading: false },
            weekly: { total: 0, loading: false },
            monthly: { total: 0, loading: false },
            yearly: { total: 0, loading: false }
        };
    }
};

const chartData = ref({
    labels: [],
    data: []
});
const chartLoading = ref(false);

const fetchChartData = async (period: string) => {
    chartLoading.value = true;
    try {
        const response = await axios.get(`/user/dashboard-analytics/chart/${period}`);
        chartData.value = {
            labels: response.data.labels,
            data: response.data.data
        };
    } catch (error) {
        console.error('Error fetching chart data:', error);
        chartData.value = {
            labels: [],
            data: []
        };
    }
    chartLoading.value = false;
};

const fetchCategoryAnalytics = async () => {
    categoryData.value.loading = true;
    try {
        const response = await axios.get('/user/dashboard-analytics/categories');
        categoryData.value = {
            categories: response.data.categories,
            loading: false
        };
    } catch (error) {
        console.error('Error fetching category analytics:', error);
        categoryData.value = {
            categories: [],
            loading: false
        };
    }
};

watch(selectedPeriod, (newPeriod) => {
    fetchChartData(newPeriod);
}, { immediate: true });

onMounted(() => {
    fetchDashboardData();
    fetchCategoryAnalytics();
});

const maxChartValue = computed(() => {
    return Math.max(...chartData.value.data, 1);
});

const chartTotal = computed(() => {
    if (!chartData.value.data || chartData.value.data.length === 0) {
        return 0;
    }

    // Ensure all values are treated as numbers
    const validNumbers = chartData.value.data.map(val => {
        const num = Number(val);
        return isNaN(num) ? 0 : num;
    });

    const total = validNumbers.reduce((sum, val) => sum + val, 0);

    return total;
});

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
    };
}>();
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col text-center gap-2 rounded-t-lg bg-gray-800 p-4">
            <h1 class="text-2xl font-bold">Selamat Datang, {{ props.user.name }}!</h1>
            <p class="mt-2 text-gray-400">Di sini anda dapat menemukan gambaran umum tentang aktivitas dan statistik
                anda.</p>
        </div>

        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 overflow-x-auto">
            <!-- Chart Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-4 md:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-6">
                    <h2 class="text-lg md:text-xl font-semibold">Grafik Pengeluaran</h2>
                    <div class="relative">
                        <select
                            v-model="selectedPeriod"
                            class="appearance-none bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 pr-8 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto"
                        >
                            <option v-for="option in periodOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <ChevronDown class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" />
                    </div>
                </div>

                <!-- Mobile Chart Visualization -->
                <div class="block md:hidden">
                    <div v-if="chartLoading" class="flex items-center justify-center h-48">
                        <p class="text-gray-500">Loading chart...</p>
                    </div>
                    <div v-else-if="chartData.data.length === 0" class="flex items-center justify-center h-48">
                        <p class="text-gray-500">No data available</p>
                    </div>
                    <div v-else class="space-y-3">
                        <!-- Mobile Chart as Horizontal Bars -->
                        <div
                            v-for="(value, index) in chartData.data"
                            :key="index"
                            class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3"
                        >
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ chartData.labels[index] }}
                                </span>
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                    {{ formatCurrencyShort(value) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                <div
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500 ease-out"
                                    :style="{ width: `${value > 0 ? (value / maxChartValue) * 100 : 2}%` }"
                                ></div>
                            </div>
                        </div>

                        <!-- Mobile Summary Card -->
                        <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total {{ periodOptions.find(p => p.value === selectedPeriod)?.label }}</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ formatCurrency(chartTotal) }}
                                </p>
                                <!-- Debug info - remove after fixing -->
                                <!-- <p class="text-xs text-gray-500 mt-1">
                                    Debug: {{ chartData.data }} | Total: {{ chartTotal }}
                                </p> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Chart Visualization -->
                <div class="hidden md:block">
                    <div class="h-64 flex items-end justify-center gap-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div v-if="chartLoading" class="flex items-center justify-center w-full h-full">
                            <p class="text-gray-500">Loading chart...</p>
                        </div>
                        <div v-else-if="chartData.data.length === 0" class="flex items-center justify-center w-full h-full">
                            <p class="text-gray-500">No data available</p>
                        </div>
                        <div
                            v-else
                            v-for="(value, index) in chartData.data"
                            :key="index"
                            class="flex flex-col items-center gap-2 group"
                        >
                            <div class="flex flex-col items-center relative">
                                <!-- Tooltip on hover -->
                                <div class="absolute -top-8 bg-gray-800 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                    {{ formatCurrency(value) }}
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    {{ formatCurrency(value) }}
                                </span>
                                <div
                                    class="bg-gradient-to-t from-blue-600 to-blue-400 rounded-t transition-all duration-500 min-w-[40px] flex items-end justify-center hover:from-blue-700 hover:to-blue-500 cursor-pointer"
                                    :style="{ height: `${value > 0 ? (value / maxChartValue) * 180 + 20 : 20}px` }"
                                >
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-400 text-center">{{ chartData.labels[index] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Analytics Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-4 md:p-6">
                <div class="flex items-center gap-3 mb-6">
                    <TrendingUp class="w-6 h-6 text-purple-500" />
                    <h2 class="text-lg md:text-xl font-semibold">Kategori Pengeluaran Terbesar</h2>
                </div>

                <!-- Mobile Category View -->
                <div class="block md:hidden">
                    <div v-if="categoryData.loading" class="flex items-center justify-center h-32">
                        <p class="text-gray-500">Loading categories...</p>
                    </div>
                    <div v-else-if="categoryData.categories.length === 0" class="flex items-center justify-center h-32">
                        <p class="text-gray-500">No category data available</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="(category, index) in categoryData.categories"
                            :key="index"
                            class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4"
                        >
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                        #{{ index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ category.name_category }}
                                    </span>
                                </div>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                    {{ formatCurrencyShort(category.total_amount) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ category.transaction_count }} transaksi</span>
                                <span>{{ formatCurrency(category.total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Category View -->
                <div class="hidden md:block">
                    <div v-if="categoryData.loading" class="flex items-center justify-center h-40">
                        <p class="text-gray-500">Loading categories...</p>
                    </div>
                    <div v-else-if="categoryData.categories.length === 0" class="flex items-center justify-center h-40">
                        <p class="text-gray-500">No category data available</p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="(category, index) in categoryData.categories"
                            :key="index"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                        >
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <span class="text-purple-600 dark:text-purple-400 font-bold text-sm">
                                        {{ index + 1 }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ category.name_category }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ category.transaction_count }} transaksi
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg text-green-600 dark:text-green-400">
                                    {{ formatCurrency(category.total_amount) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ ((category.total_amount / categoryData.categories[0]?.total_amount) * 100).toFixed(1) }}% dari tertinggi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-6 flex flex-col items-center text-center gap-3">
                    <Activity class="w-8 h-8 text-blue-500" />
                    <h3 class="font-semibold">Pengeluaran Harian</h3>
                    <p v-if="dashboardData.daily.loading" class="text-lg text-gray-500">Loading...</p>
                    <p v-else class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(dashboardData.daily.total) }}
                    </p>
                </div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-6 flex flex-col items-center text-center gap-3">
                    <Activity class="w-8 h-8 text-green-500" />
                    <h3 class="font-semibold">Pengeluaran Mingguan</h3>
                    <p v-if="dashboardData.weekly.loading" class="text-lg text-gray-500">Loading...</p>
                    <p v-else class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(dashboardData.weekly.total) }}
                    </p>
                </div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-6 flex flex-col items-center text-center gap-3">
                    <Calendar class="w-8 h-8 text-yellow-500" />
                    <h3 class="font-semibold">Pengeluaran Bulanan</h3>
                    <p v-if="dashboardData.monthly.loading" class="text-lg text-gray-500">Loading...</p>
                    <p v-else class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(dashboardData.monthly.total) }}
                    </p>
                </div>
                <div class="relative bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 p-6 flex flex-col items-center text-center gap-3">
                    <BadgeDollarSign class="w-8 h-8 text-red-500" />
                    <h3 class="font-semibold">Pengeluaran Tahunan</h3>
                    <p v-if="dashboardData.yearly.loading" class="text-lg text-gray-500">Loading...</p>
                    <p v-else class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(dashboardData.yearly.total) }}
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
