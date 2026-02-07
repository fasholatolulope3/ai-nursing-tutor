<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
</script>

<template>
    <Head title="Achievements" />
    <DashboardSidebar
        title="Achievements"
        description="View your badges, milestones, and progress."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            Gamification and achievements coming soon.
        </div>
    </DashboardSidebar>
</template>
