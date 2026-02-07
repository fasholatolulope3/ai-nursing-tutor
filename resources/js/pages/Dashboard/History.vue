<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
</script>

<template>
    <Head title="History" />
    <DashboardLayout
        title="Session History"
        description="Review your past simulations and performance logs."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            History module coming soon.
        </div>
    </DashboardLayout>
</template>
