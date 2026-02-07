<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
</script>

<template>
    <Head title="Guidelines" />
    <DashboardSidebar
        title="Clinical Guidelines"
        description="Access standard nursing protocols and best practices."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            Clinical guidelines coming soon.
        </div>
    </DashboardSidebar>
</template>
