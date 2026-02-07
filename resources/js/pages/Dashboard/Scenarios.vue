<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
import { simulationService, type Scenario } from '@/services/simulation';
import { Button } from '@/components/ui/button';

const scenarios = ref<Scenario[]>([]);
const loading = ref(true);

onMounted(async () => {
    try {
        scenarios.value = await simulationService.getScenarios();
    } catch (error) {
        console.error("Failed to load scenarios", error);
    } finally {
        loading.value = false;
    }
});

const startScenario = async (id: number) => {
    try {
        const session = await simulationService.startSimulation(id);
        router.visit(`/simulation/${session.id}`);
    } catch (error) {
        console.error("Failed to start simulation", error);
    }
};
</script>

<template>
    <Head title="Scenarios" />

    <DashboardSidebar 
        title="Scenarios" 
        description="Select a clinical simulation to begin practice."
    >
        <div class="flex flex-col gap-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Available Scenarios</h2>
            
            <div v-if="loading" class="text-slate-400">Loading modules...</div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="scenario in scenarios" 
                    :key="scenario.id"
                    class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 p-6 rounded-xl hover:bg-gray-50 dark:hover:bg-white/10 transition-colors flex flex-col gap-4"
                >
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-emerald-600 dark:text-emerald-400">{{ scenario.title }}</h3>
                            <span class="text-xs uppercase tracking-wider bg-gray-100 dark:bg-white/10 px-2 py-1 rounded text-gray-600 dark:text-slate-300">{{ scenario.complexity }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-3">{{ scenario.description }}</p>
                    </div>
                    
                    <Button @click="startScenario(scenario.id)" class="mt-auto w-full">
                        Start Simulation
                    </Button>
                </div>
                
                <!-- Zero State -->
                <div v-if="scenarios.length === 0" class="col-span-full text-slate-500 text-center py-10 border border-dashed border-gray-200 dark:border-white/10 rounded-xl">
                    No scenarios available. Please run seeders.
                </div>
            </div>
        </div>
    </DashboardSidebar>
</template>
