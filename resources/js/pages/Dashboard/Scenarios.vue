<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { simulationService, type Scenario } from '@/services/simulation';
import { Button } from '@/components/ui/button';
import {
    Loader2,
    Sparkles,
    Plus,
    Activity,
    Brain,
    Stethoscope,
    Trophy,
    ChevronDown,
} from 'lucide-vue-next';

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
        const response = await simulationService.getScenarios(
            currentPage.value,
        );
        if (reset) {
            scenarios.value = response.data;
        } else {
            scenarios.value = [...scenarios.value, ...response.data];
        }
        lastPage.value = response.last_page;
    } catch (error) {
        console.error('Failed to load scenarios', error);
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
        console.error('Failed to start simulation', error);
        isStartingMap.value[id] = false;
    }
};

const generateNewScenario = async () => {
    isGenerating.value = true;
    try {
        generationError.value = null;
        const newScenario = await simulationService.generateScenario(
            genParams.value,
        );
        scenarios.value.unshift(newScenario); // Add to top of list
        showGenerateModal.value = false; // Close modal on success
    } catch (error: any) {
        console.error('Failed to generate scenario', error);

        let errorMessage =
            'Generation failed. The AI engine might be busy. Please try again.';
        if (error.response?.status === 429) {
            errorMessage = 'Usage limit reached. Please wait a moment.';
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
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 pb-12 sm:px-6">
            <!-- Header Section -->
            <div
                class="flex flex-col justify-between gap-4 py-4 sm:flex-row sm:items-center"
            >
                <div>
                    <h2
                        class="bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 bg-clip-text text-3xl font-bold tracking-tight text-transparent dark:from-white dark:via-gray-300 dark:to-gray-500"
                    >
                        Available Scenarios
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Master your clinical reasoning with high-fidelity
                        simulations.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Button
                        @click="showGenerateModal = !showGenerateModal"
                        variant="outline"
                        class="h-10 gap-2 rounded-xl border-emerald-500/30 text-emerald-700 shadow-sm transition-all hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-500/10"
                    >
                        <Sparkles class="h-4 w-4" />
                        AI Generate
                    </Button>
                </div>
            </div>

            <!-- Inline Generation Card (Premium Refined) -->
            <div
                v-if="showGenerateModal"
                class="group relative animate-in duration-700 fade-in slide-in-from-top-4"
            >
                <!-- Glowing Backdrop -->
                <div
                    class="absolute -inset-1 rounded-[2.5rem] bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-teal-500/10 opacity-50 blur-2xl transition duration-1000 group-hover:opacity-100"
                ></div>

                <div
                    class="relative overflow-hidden rounded-[2rem] border border-emerald-500/10 bg-white/80 p-8 shadow-2xl backdrop-blur-2xl dark:border-emerald-500/20 dark:bg-black/40"
                >
                    <!-- Subtle Mesh Gradient -->
                    <div
                        class="pointer-events-none absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 rounded-full bg-emerald-500/5 blur-3xl"
                    ></div>

                    <div class="relative flex flex-col gap-8">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10"
                                >
                                    <Sparkles
                                        class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                                    />
                                </div>
                                <div>
                                    <h3
                                        class="text-xs font-bold tracking-[0.2em] text-gray-400 uppercase"
                                    >
                                        Clinical Intelligence
                                    </h3>
                                    <h2
                                        class="mt-0.5 text-lg font-bold text-gray-900 dark:text-white"
                                    >
                                        Generate New Clinical Case
                                    </h2>
                                </div>
                            </div>
                            <button
                                @click="showGenerateModal = false"
                                class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl text-gray-400 transition-all hover:bg-gray-100 dark:hover:bg-white/5"
                            >
                                <Plus class="h-5 w-5 rotate-45" />
                            </button>
                        </div>

                        <!-- Main Inputs -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Custom Select Area -->
                            <div class="space-y-2.5">
                                <label
                                    class="ml-0.5 px-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                    >Complexity Level</label
                                >
                                <div class="group/select relative">
                                    <select
                                        v-model="genParams.difficulty"
                                        class="h-14 w-full cursor-pointer appearance-none rounded-2xl border border-gray-200 bg-gray-50/50 pr-12 pl-5 text-sm font-medium text-gray-900 transition-all hover:border-emerald-500/30 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-white/[0.03] dark:text-white"
                                    >
                                        <option
                                            value="Beginner"
                                            class="bg-white text-gray-900 dark:bg-slate-900 dark:text-white"
                                        >
                                            Beginner (Stable Patient)
                                        </option>
                                        <option
                                            value="Intermediate"
                                            class="bg-white text-gray-900 dark:bg-slate-900 dark:text-white"
                                        >
                                            Intermediate (Acute Crisis)
                                        </option>
                                        <option
                                            value="Advanced"
                                            class="bg-white text-gray-900 dark:bg-slate-900 dark:text-white"
                                        >
                                            Advanced (Critical Crisis)
                                        </option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-400 transition-colors group-hover/select:text-emerald-500"
                                    >
                                        <ChevronDown class="h-4 w-4" />
                                    </div>
                                </div>
                            </div>

                            <!-- Role Input -->
                            <div class="space-y-2.5">
                                <label
                                    class="ml-0.5 px-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                    >Nursing Role / Focus</label
                                >
                                <div class="group/input relative">
                                    <input
                                        v-model="genParams.role"
                                        placeholder="e.g. Charge Nurse, ICU Specialist"
                                        class="h-14 w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-5 text-sm font-medium text-gray-900 transition-all hover:border-emerald-500/30 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-white/[0.03] dark:text-white"
                                    />
                                    <div
                                        class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 opacity-0 transition-opacity group-hover/input:opacity-100"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label
                                class="ml-0.5 px-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                >Specific Clinical Requirements
                                (Optional)</label
                            >
                            <textarea
                                v-model="genParams.description"
                                rows="5"
                                placeholder="Describe specific medical history, comorbidities, or nursing challenges you wish to practice..."
                                class="w-full resize-none rounded-[2rem] border border-gray-200 bg-gray-50/50 p-6 text-sm font-medium text-gray-900 shadow-inner transition-all focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-white/[0.03] dark:text-white"
                            ></textarea>
                        </div>

                        <!-- Error Message -->
                        <div
                            v-if="generationError"
                            class="flex animate-in items-center gap-3 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm font-medium text-red-500 slide-in-from-top-2"
                        >
                            <Activity class="h-5 w-5 shrink-0" />
                            {{ generationError }}
                        </div>

                        <!-- Generate Button at Bottom -->
                        <div class="flex justify-end pt-2">
                            <Button
                                @click="generateNewScenario"
                                :disabled="isGenerating"
                                class="h-14 w-full min-w-[240px] rounded-2xl border-0 bg-emerald-600 px-8 font-bold text-white shadow-xl shadow-emerald-500/10 transition-all hover:scale-[1.02] hover:bg-emerald-500 active:scale-[0.98] md:w-auto"
                            >
                                <Loader2
                                    v-if="isGenerating"
                                    class="mr-3 h-5 w-5 animate-spin"
                                />
                                <Sparkles v-else class="mr-3 h-5 w-5" />
                                <span class="tracking-widest uppercase"
                                    >SYNTHESIZE CASE</span
                                >
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div
                v-if="isGenerating"
                class="animate-pulse rounded-3xl border-2 border-dashed border-emerald-500/20 bg-emerald-500/5 py-12 text-center"
            >
                <Loader2
                    class="mx-auto mb-4 h-10 w-10 animate-spin text-emerald-500"
                />
                <p
                    class="text-sm font-medium text-emerald-700 dark:text-emerald-400"
                >
                    Our Clinical AI is synthesizing your custom scenario...
                </p>
            </div>

            <!-- Loading Initial List -->
            <div
                v-if="loading && currentPage === 1"
                class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="i in 3"
                    :key="i"
                    class="h-64 animate-pulse rounded-2xl border border-gray-200 bg-gray-100 dark:border-white/10 dark:bg-white/5"
                ></div>
            </div>

            <!-- Scenarios Grid -->
            <div v-else class="mb-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="scenario in scenarios"
                    :key="scenario.id"
                    class="group relative flex flex-col gap-6 rounded-3xl border border-gray-200 bg-white p-7 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 dark:border-white/10 dark:bg-slate-900/50"
                >
                    <div class="flex-1 space-y-4">
                        <div class="flex items-start justify-between">
                            <div
                                class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 transition-all duration-300 group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400"
                            >
                                <Activity class="h-6 w-6" />
                            </div>
                            <span
                                class="rounded-full border px-3 py-1 text-[10px] font-bold tracking-widest uppercase shadow-sm"
                                :class="{
                                    'border-emerald-200/50 bg-emerald-50 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400':
                                        scenario.complexity === 'beginner',
                                    'border-amber-200/50 bg-amber-50 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400':
                                        scenario.complexity === 'intermediate',
                                    'border-rose-200/50 bg-rose-50 text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400':
                                        scenario.complexity === 'advanced' ||
                                        scenario.complexity === 'expert',
                                }"
                            >
                                {{ scenario.complexity }}
                            </span>
                        </div>

                        <div>
                            <Link
                                :href="`/dashboard/scenarios/${scenario.id}`"
                                class="cursor-pointer transition-colors group-hover:text-emerald-500"
                            >
                                <h3
                                    class="mb-2 text-xl leading-tight font-bold text-gray-900 dark:text-white"
                                >
                                    {{ scenario.title }}
                                </h3>
                            </Link>
                            <p
                                class="line-clamp-3 text-sm leading-relaxed text-gray-500 dark:text-slate-400"
                            >
                                {{ scenario.description }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div
                                class="flex items-center gap-1.5 rounded-lg bg-gray-50 px-3 py-1.5 text-[10px] font-bold tracking-tight text-gray-500 uppercase dark:bg-white/5 dark:text-slate-500"
                            >
                                <Plus class="h-3 w-3 text-emerald-500" />
                                {{ scenario.objective?.length || 0 }} Objectives
                            </div>
                        </div>
                    </div>

                    <button
                        @click="startScenario(scenario.id)"
                        :disabled="isStartingMap[scenario.id]"
                        class="flex w-full items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-xs font-bold tracking-widest text-white uppercase shadow-lg shadow-slate-900/10 transition-all duration-300 hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-slate-900 dark:hover:bg-emerald-500 dark:hover:text-white"
                    >
                        <Loader2
                            v-if="isStartingMap[scenario.id]"
                            class="mr-2 h-3 w-3 animate-spin"
                        />
                        {{
                            isStartingMap[scenario.id]
                                ? 'INITIALIZING...'
                                : 'START SIMULATION'
                        }}
                    </button>
                </div>
            </div>

            <!-- Refined Zero State -->
            <div
                v-if="scenarios.length === 0 && !loading && !isGenerating"
                class="col-span-full rounded-[40px] border-2 border-dashed border-gray-200 bg-gray-50/50 py-20 text-center dark:border-white/5 dark:bg-white/[0.02]"
            >
                <div
                    class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50 dark:border-white/10 dark:bg-white/5 dark:shadow-none"
                >
                    <Sparkles
                        class="h-10 w-10 text-gray-300 dark:text-slate-600"
                    />
                </div>
                <h3
                    class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                >
                    Empty Simulation Deck
                </h3>
                <p
                    class="mx-auto mb-8 max-w-sm text-sm text-gray-500 dark:text-slate-500"
                >
                    You haven't generated any custom scenarios yet. Let our
                    Clinical AI design a practice case for you.
                </p>
                <Button
                    @click="showGenerateModal = true"
                    variant="outline"
                    class="h-12 gap-2 rounded-2xl border-emerald-200 px-8 text-xs font-bold tracking-widest text-emerald-700 uppercase hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-400 dark:hover:bg-emerald-500/10"
                >
                    <Sparkles class="h-4 w-4" />
                    Create Your First Case
                </Button>
            </div>

            <!-- Load More Button -->
            <div v-if="currentPage < lastPage" class="mt-8 flex justify-center">
                <Button
                    @click="loadMore"
                    :disabled="loading"
                    variant="outline"
                    class="h-12 rounded-2xl border-gray-200 px-8 text-xs font-bold tracking-widest text-gray-500 uppercase transition-all hover:bg-gray-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    Load More Scenarios
                </Button>
            </div>
        </div>
    </DashboardLayout>
</template>
