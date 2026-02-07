<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
</script>

<template>
    <Head title="Settings" />
    <DashboardSidebar
        title="Settings"
        description="Manage your account preferences and application settings."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            Settings module coming soon.
        </div>
    </DashboardSidebar>
</template>
