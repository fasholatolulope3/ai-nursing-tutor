<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
</script>

<template>
    <Head title="Performance" />
    <DashboardSidebar
        title="Performance Analytics"
        description="Track your clinical reasoning growth over time."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            Performance analytics coming soon.
        </div>
    </DashboardSidebar>
</template>
