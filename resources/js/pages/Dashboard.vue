<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import {
  BookOpen,
  Activity,
  History,
  Award,
  TrendingUp,
} from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Stat type definition
interface Stat {
    label: string;
    value: string;
    trend: string;
    icon: any;
    color: string;
    bg: string;
}

const stats = ref<Stat[]>([]);
const loading = ref(true);

const fetchStats = async () => {
    try {
        const response = await axios.get('/api/v1/dashboard/stats');
        const data = response.data;

        stats.value = [
            { label: 'Scenarios Completed', value: data.scenarios_completed.value, trend: data.scenarios_completed.trend, icon: BookOpen, color: 'text-emerald-600', bg: 'bg-emerald-50' },
            { label: 'Clinical Accuracy', value: data.clinical_accuracy.value, trend: data.clinical_accuracy.trend, icon: Activity, color: 'text-emerald-600', bg: 'bg-emerald-50' },
            { label: 'Time Spent', value: data.time_spent.value, trend: data.time_spent.trend, icon: History, color: 'text-emerald-600', bg: 'bg-emerald-50' },
            { label: 'Experience Points', value: data.experience_points.value, trend: data.experience_points.trend, icon: Award, color: 'text-emerald-600', bg: 'bg-emerald-50' },
        ];
    } catch (error) {
        console.error('Failed to fetch dashboard stats', error);
        // Fallback or error state could be handled here
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchStats();
});
</script>

<template>
    <Head title="Dashboard" />
    
    <DashboardLayout title="Dashboard" description="Welcome back to your clinical workspace">
        <!-- Stats Grid -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
             <div v-for="i in 4" :key="i" class="p-6 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 h-32 animate-pulse">
                <div class="h-8 w-8 bg-gray-200 dark:bg-white/10 rounded-lg mb-4"></div>
                <div class="h-6 w-3/4 bg-gray-200 dark:bg-white/10 rounded mb-2"></div>
                <div class="h-4 w-1/2 bg-gray-200 dark:bg-white/10 rounded"></div>
             </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div v-for="(stat, idx) in stats" :key="idx" class="p-6 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div :class="['p-2 rounded-lg', stat.bg, 'dark:bg-emerald-500/10']">
                        <component :is="stat.icon" :class="['h-5 w-5', stat.color, 'dark:text-emerald-400']" />
                    </div>
                    <TrendingUp class="h-4 w-4 text-emerald-500" />
                </div>
                <h3 class="font-medium text-gray-600 dark:text-slate-400 mb-1">{{ stat.label }}</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</p>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">{{ stat.trend }} from last month</p>
            </div>
        </div>
    </DashboardLayout>
</template>
