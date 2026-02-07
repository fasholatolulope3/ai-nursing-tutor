<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
</script>

<template>
    <Head title="Exam Prep" />
    <DashboardSidebar
        title="NCLEX Prep"
        description="Practice with exam-style questions and rationales."
    >
        <div class="p-10 text-center border border-dashed border-gray-200 dark:border-white/10 rounded-xl text-slate-500">
            NCLEX preparation module coming soon.
        </div>
    </DashboardSidebar>
</template>
