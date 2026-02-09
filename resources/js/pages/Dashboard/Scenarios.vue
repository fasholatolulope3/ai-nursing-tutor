<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { simulationService, type Scenario } from '@/services/simulation';
import { Button } from '@/components/ui/button';
import { Loader2, Sparkles, Plus, Activity, Brain, Stethoscope, Trophy, ChevronDown } from 'lucide-vue-next';

const scenarios = ref<Scenario[]>([]);
const loading = ref(true);
const isGenerating = ref(false);
const generationError = ref<string | null>(null);

// Generation State
const showGenerateModal = ref(false);
const genParams = ref({
    type: 'Clinical Simulation Command Center',
    difficulty: 'Intermediate',
    role: 'Charge Nurse',
    description: '',
});

const isStartingMap = ref<Record<number, boolean>>({});

const currentPage = ref(1);
const lastPage = ref(1);

onMounted(async () => {
    await loadScenarios();
});

const loadScenarios = async (reset = false) => {
    if (reset) {
        currentPage.value = 1;
        scenarios.value = [];
        loading.value = true;
    }
    
    try {
        const response = await simulationService.getScenarios(currentPage.value);
        if (reset) {
             scenarios.value = response.data;
        } else {
             scenarios.value = [...scenarios.value, ...response.data];
        }
        lastPage.value = response.last_page;
    } catch (error) {
        console.error("Failed to load scenarios", error);
    } finally {
        loading.value = false;
    }
};

const loadMore = async () => {
    if (currentPage.value < lastPage.value) {
        currentPage.value++;
        await loadScenarios();
    }
};

const startScenario = async (id: number) => {
    if (isStartingMap.value[id]) return;
    isStartingMap.value[id] = true;
    try {
        const session = await simulationService.startSimulation(id);
        router.visit(`/simulation/${session.id}`);
    } catch (error) {
        console.error("Failed to start simulation", error);
        isStartingMap.value[id] = false;
    }
};

const generateNewScenario = async () => {
    isGenerating.value = true;
    try {
        generationError.value = null;
        const newScenario = await simulationService.generateScenario(genParams.value);
        scenarios.value.unshift(newScenario); // Add to top of list
        showGenerateModal.value = false; // Close modal on success
    } catch (error: any) {
        console.error("Failed to generate scenario", error);
        
        let errorMessage = "Generation failed. The AI engine might be busy. Please try again.";
        if (error.response?.status === 429) {
            errorMessage = "Usage limit reached. Please wait a moment.";
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }
        
        generationError.value = errorMessage;
    } finally {
        isGenerating.value = false;
    }
};
</script>

<template>
    <Head title="Scenarios" />

    <DashboardLayout 
        title="Scenarios" 
        description="Bridge the gap between theory and practice with AI-driven clinical reasoning simulations."
    >
        <div class="flex flex-col gap-6 max-w-7xl mx-auto px-4 sm:px-6 pb-12">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4">
                <div>
                    <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 dark:from-white dark:via-gray-300 dark:to-gray-500 tracking-tight">
                        Available Scenarios
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Master your clinical reasoning with high-fidelity simulations.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <Button 
                        @click="showGenerateModal = !showGenerateModal" 
                        variant="outline"
                        class="gap-2 border-emerald-500/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl h-10 transition-all shadow-sm"
                    >
                        <Sparkles class="w-4 h-4" />
                        AI Generate
                    </Button>
                </div>
            </div>

            <!-- Inline Generation Card (Premium Refined) -->
            <div v-if="showGenerateModal" class="relative group animate-in fade-in slide-in-from-top-4 duration-700">
                <!-- Glowing Backdrop -->
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-teal-500/10 rounded-[2.5rem] blur-2xl opacity-50 group-hover:opacity-100 transition duration-1000"></div>
                
                <div class="relative bg-white/80 dark:bg-black/40 backdrop-blur-2xl border border-emerald-500/10 dark:border-emerald-500/20 p-8 rounded-[2rem] shadow-2xl overflow-hidden">
                    <!-- Subtle Mesh Gradient -->
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="relative flex flex-col gap-8">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                                    <Sparkles class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">Clinical Intelligence</h3>
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">Generate New Clinical Case</h2>
                                </div>
                            </div>
                            <button @click="showGenerateModal = false" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-white/5 text-gray-400 transition-all cursor-pointer">
                                <Plus class="w-5 h-5 rotate-45" />
                            </button>
                        </div>

                        <!-- Main Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Custom Select Area -->
                            <div class="space-y-2.5">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1 ml-0.5">Complexity Level</label>
                                <div class="relative group/select">
                                    <select 
                                        v-model="genParams.difficulty" 
                                        class="appearance-none w-full bg-gray-50/50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-2xl text-sm font-medium h-14 pl-5 pr-12 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all cursor-pointer hover:border-emerald-500/30"
                                    >
                                        <option value="Beginner" class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white">Beginner (Stable Patient)</option>
                                        <option value="Intermediate" class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white">Intermediate (Acute Crisis)</option>
                                        <option value="Advanced" class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white">Advanced (Critical Crisis)</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 group-hover/select:text-emerald-500 transition-colors">
                                        <ChevronDown class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>

                            <!-- Role Input -->
                            <div class="space-y-2.5">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1 ml-0.5">Nursing Role / Focus</label>
                                <div class="relative group/input">
                                    <input 
                                        v-model="genParams.role" 
                                        placeholder="e.g. Charge Nurse, ICU Specialist" 
                                        class="w-full bg-gray-50/50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-2xl text-sm font-medium h-14 px-5 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all hover:border-emerald-500/30" 
                                    />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-0 group-hover/input:opacity-100 transition-opacity">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Optional Context -->
                        <div class="space-y-2.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1 ml-0.5">Specific Clinical Requirements (Optional)</label>
                            <textarea 
                                v-model="genParams.description" 
                                rows="3" 
                                placeholder="Describe specific medical history, comorbidities, or nursing challenges you wish to practice..." 
                                class="w-full bg-gray-50/50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-[2rem] text-sm font-medium p-6 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all resize-none shadow-inner"
                            ></textarea>
                        </div>


                        <!-- Error Message -->
                        <div v-if="generationError" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-medium flex items-center gap-3 animate-in slide-in-from-top-2">
                            <Activity class="w-5 h-5 shrink-0" />
                            {{ generationError }}
                        </div>

                        <!-- Generate Button at Bottom -->
                        <div class="flex justify-end pt-2">
                            <Button 
                                @click="generateNewScenario" 
                                :disabled="isGenerating" 
                                class="w-full md:w-auto min-w-[240px] h-14 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl shadow-xl shadow-emerald-500/10 transition-all hover:scale-[1.02] active:scale-[0.98] border-0 px-8"
                            >
                                <Loader2 v-if="isGenerating" class="w-5 h-5 mr-3 animate-spin" />
                                <Sparkles v-else class="w-5 h-5 mr-3" />
                                <span class="tracking-widest uppercase">SYNTESIZE CASE</span>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isGenerating" class="py-12 text-center border-2 border-dashed border-emerald-500/20 rounded-3xl bg-emerald-500/5 animate-pulse">
                <Loader2 class="w-10 h-10 text-emerald-500 animate-spin mx-auto mb-4" />
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Our Clinical AI is synthesizing your custom scenario...</p>
            </div>

            <!-- Loading Initial List -->
            <div v-if="loading && currentPage === 1" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="i in 3" :key="i" class="h-64 rounded-2xl bg-gray-100 dark:bg-white/5 animate-pulse border border-gray-200 dark:border-white/10"></div>
            </div>

            <!-- Scenarios Grid -->
            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-8">
                <div 
                    v-for="scenario in scenarios" 
                    :key="scenario.id"
                    class="group relative bg-white dark:bg-slate-900/50 border border-gray-200 dark:border-white/10 p-7 rounded-3xl hover:border-emerald-500/40 transition-all duration-500 flex flex-col gap-6 shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-1"
                >
                    <div class="space-y-4 flex-1">
                        <div class="flex justify-between items-start">
                            <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                <Activity class="w-6 h-6" />
                            </div>
                            <span 
                                class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border shadow-sm"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 border-emerald-200/50 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20': scenario.complexity === 'beginner',
                                    'bg-amber-50 text-amber-700 border-amber-200/50 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20': scenario.complexity === 'intermediate',
                                    'bg-rose-50 text-rose-700 border-rose-200/50 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20': scenario.complexity === 'advanced' || scenario.complexity === 'expert',
                                }"
                            >
                                {{ scenario.complexity }}
                            </span>
                        </div>
                        
                        <div>
                            <Link :href="`/dashboard/scenarios/${scenario.id}`" class="group-hover:text-emerald-500 transition-colors cursor-pointer">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 leading-tight">
                                    {{ scenario.title }}
                                </h3>
                            </Link>
                            <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                {{ scenario.description }}
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                             <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-white/5 text-[10px] font-bold text-gray-500 dark:text-slate-500 uppercase tracking-tight">
                                <Plus class="w-3 h-3 text-emerald-500" />
                                {{ scenario.objective?.length || 0 }} Objectives
                            </div>
                        </div>
                    </div>
                    
                    <button 
                        @click="startScenario(scenario.id)"
                        :disabled="isStartingMap[scenario.id]"
                        class="w-full flex items-center justify-center py-3 px-6 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-emerald-600 dark:hover:bg-emerald-500 dark:hover:text-white transition-all duration-300 font-bold text-xs uppercase tracking-widest shadow-lg shadow-slate-900/10 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Loader2 v-if="isStartingMap[scenario.id]" class="w-3 h-3 mr-2 animate-spin" />
                        {{ isStartingMap[scenario.id] ? 'INITIALIZING...' : 'START SIMULATION' }}
                    </button>
                </div>
            </div>

            <!-- Refined Zero State -->
            <div v-if="scenarios.length === 0 && !loading && !isGenerating" class="col-span-full py-20 text-center border-2 border-dashed border-gray-200 dark:border-white/5 rounded-[40px] bg-gray-50/50 dark:bg-white/[0.02]">
                <div class="w-20 h-20 bg-white dark:bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/10">
                    <Sparkles class="w-10 h-10 text-gray-300 dark:text-slate-600" />
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">Empty Simulation Deck</h3>
                <p class="text-gray-500 dark:text-slate-500 max-w-sm mx-auto mb-8 text-sm">You haven't generated any custom scenarios yet. Let our Clinical AI design a practice case for you.</p>
                <Button 
                    @click="showGenerateModal = true" 
                    variant="outline" 
                    class="gap-2 border-emerald-200 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-400 dark:hover:bg-emerald-500/10 px-8 h-12 rounded-2xl font-bold text-xs uppercase tracking-widest"
                >
                    <Sparkles class="w-4 h-4" />
                    Create Your First Case
                </Button>
            </div>

            <!-- Load More Button -->
            <div v-if="currentPage < lastPage" class="flex justify-center mt-8">
                 <Button 
                    @click="loadMore" 
                    :disabled="loading"
                    variant="outline" 
                    class="h-12 px-8 rounded-2xl border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-500 dark:text-slate-400 font-bold text-xs uppercase tracking-widest transition-all"
                >
                    <Loader2 v-if="loading" class="w-4 h-4 mr-2 animate-spin" />
                    Load More Scenarios
                 </Button>
            </div>
        </div>
    </DashboardLayout>
</template>
