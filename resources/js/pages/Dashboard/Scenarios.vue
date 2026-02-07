<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { simulationService, type Scenario } from '@/services/simulation';
import { Button } from '@/components/ui/button';
import { Loader2, Sparkles, Plus } from 'lucide-vue-next';

const scenarios = ref<Scenario[]>([]);
const loading = ref(true);
const isGenerating = ref(false);

// Generation State
const showGenerateModal = ref(false);
const genParams = ref({
    type: 'Clinical Simulation Command Center',
    difficulty: 'Intermediate',
    role: 'Charge Nurse',
});

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

const generateNewScenario = async () => {
    isGenerating.value = true;
    showGenerateModal.value = false; // Close modal start generation
    try {
        const newScenario = await simulationService.generateScenario(genParams.value);
        scenarios.value.unshift(newScenario); // Add to top of list
    } catch (error: any) {
        console.error("Failed to generate scenario", error);
        const msg = error.response?.data?.message || "Failed to generate scenario. Please try again.";
        alert(msg);
    } finally {
        isGenerating.value = false;
    }
};
</script>

<template>
    <Head title="Scenarios" />

    <DashboardLayout 
        title="Scenarios" 
        description="Select a clinical simulation to begin practice."
    >
        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Available Scenarios</h2>
                
                <div class="flex gap-2">
                    <Button 
                        v-if="!isGenerating"
                        @click="showGenerateModal = !showGenerateModal" 
                        variant="outline" 
                        class="gap-2 border-emerald-200 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-400 dark:hover:bg-emerald-500/10"
                    >
                        <Sparkles class="w-4 h-4" />
                        AI Generate
                    </Button>
                </div>
            </div>
            
            <!-- Simple Generation Form (Inline for now, could be modal) -->
            <div v-if="showGenerateModal" class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-200 dark:border-white/10 mb-4 animate-in fade-in slide-in-from-top-2">
                <h3 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Generate New Clinical Case</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <select v-model="genParams.difficulty" class="rounded-md border-gray-300 dark:border-white/10 dark:bg-black/20 text-sm">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                    <input v-model="genParams.role" placeholder="Role (e.g. Charge Nurse)" class="rounded-md border-gray-300 dark:border-white/10 dark:bg-black/20 text-sm" />
                    <Button @click="generateNewScenario" :disabled="isGenerating" class="bg-emerald-600 hover:bg-emerald-500 text-white">
                        <Sparkles class="w-4 h-4 mr-2" />
                        Generate
                    </Button>
                </div>
            </div>

            <!-- Loading State for Generation -->
            <div v-if="isGenerating" class="flex flex-col items-center justify-center p-12 border border-dashed border-emerald-500/30 rounded-xl bg-emerald-50/50 dark:bg-emerald-500/5 animate-pulse">
                <Loader2 class="w-8 h-8 text-emerald-500 animate-spin mb-3" />
                <p class="text-emerald-700 dark:text-emerald-400 font-medium">Gemini 3 is designing a new clinical simulation...</p>
                <p class="text-xs text-emerald-600/70 dark:text-emerald-500/70 mt-1">Analyzing vitals, setting objectives, and creating complications.</p>
            </div>

            <div v-if="loading" class="text-slate-400">Loading modules...</div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="scenario in scenarios" 
                    :key="scenario.id"
                    class="group relative overflow-hidden bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 p-6 rounded-2xl hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all duration-300 flex flex-col gap-4 shadow-sm hover:shadow-md"
                >
                    <!-- Background Gradient Hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-emerald-500/0 group-hover:from-emerald-500/5 group-hover:to-emerald-500/10 transition-colors duration-500"></div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">
                                {{ scenario.title }}
                            </h3>
                            <span 
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full border"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30': scenario.complexity === 'beginner',
                                    'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-300 dark:border-yellow-500/30': scenario.complexity === 'intermediate',
                                    'bg-red-100 text-red-700 border-red-200 dark:bg-red-500/20 dark:text-red-300 dark:border-red-500/30': scenario.complexity === 'advanced' || scenario.complexity === 'expert',
                                }"
                            >
                                {{ scenario.complexity }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-slate-400 line-clamp-3 leading-relaxed mb-4">
                            {{ scenario.description }}
                        </p>

                        <!-- Quick Stats / Objectives Hint -->
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-slate-500 mb-2">
                             <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ scenario.objective?.length || 0 }} Objectives
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Vitals Monitoring
                            </div>
                        </div>
                    </div>
                    
                    <Button 
                        @click="startScenario(scenario.id)" 
                        class="mt-auto w-full relative z-10 bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-emerald-600 dark:hover:bg-emerald-400 transition-all duration-300"
                    >
                        Start Simulation
                    </Button>
                </div>
                
                <!-- Zero State -->
                <div v-if="scenarios.length === 0 && !isGenerating" class="col-span-full text-slate-500 text-center py-10 border border-dashed border-gray-200 dark:border-white/10 rounded-xl">
                    No scenarios available. Use the "AI Generate" button to create one.
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
