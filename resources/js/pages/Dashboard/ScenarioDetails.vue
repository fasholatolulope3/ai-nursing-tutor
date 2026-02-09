<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Button } from '@/components/ui/button';
import { 
    Clock, 
    Target, 
    BookOpen, 
    Users, 
    ArrowLeft, 
    Activity, 
    PlayCircle
} from 'lucide-vue-next';
import { simulationService } from '@/services/simulation';
import { ref } from 'vue';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps<{
    scenario: {
        id: number;
        title: string;
        description: string;
        type: string;
        complexity: string;
        objective: string[];
        initial_patient_state: Record<string, any>;
        medical_history: Record<string, any>;
        created_at: string;
    };
}>();

const isStarting = ref(false);

const startScenario = async () => {
    isStarting.value = true;
    try {
        const session = await simulationService.startSimulation(props.scenario.id);
        router.visit(`/simulation/${session.id}`);
    } catch (error) {
        console.error("Failed to start simulation", error);
        isStarting.value = false;
    }
};

const getComplexityColor = (complexity: string) => {
    if (!complexity) return 'bg-gray-100 text-gray-700 border-gray-200';
    switch (complexity.toLowerCase()) {
        case 'beginner': return 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30';
        case 'intermediate': return 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-300 dark:border-yellow-500/30';
        case 'advanced': 
        case 'expert': return 'bg-red-100 text-red-700 border-red-200 dark:bg-red-500/20 dark:text-red-300 dark:border-red-500/30';
        default: return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-500/20 dark:text-gray-400 dark:border-gray-600';
    }
};

</script>

<template>
    <Head :title="scenario.title" />

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <Link 
            href="/dashboard/scenarios" 
            class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400 transition-colors"
        >
            <ArrowLeft class="w-4 h-4 mr-1" />
            Back to Scenarios
        </Link>

        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <span 
                        class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border"
                        :class="getComplexityColor(scenario.complexity)"
                    >
                        {{ scenario.complexity }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                        <Clock class="w-3.5 h-3.5 mr-1" />
                        Est. 15-20 min
                    </span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ scenario.title }}
                </h1>

                <p class="text-lg text-gray-600 dark:text-slate-300 leading-relaxed mb-6">
                    {{ scenario.description }}
                </p>

                <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <Button 
                        @click="startScenario" 
                        :disabled="isStarting"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white min-w-[200px] h-12 text-base font-medium shadow-lg shadow-emerald-500/20"
                    >
                        <PlayCircle class="w-5 h-5 mr-2" />
                        {{ isStarting ? 'Initializing...' : 'Start Simulation' }}
                    </Button>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Learning Objectives -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <Target class="w-5 h-5 text-emerald-500 mr-2" />
                    Learning Objectives
                </h3>
                <ul class="space-y-3">
                    <li 
                        v-for="(objective, index) in scenario.objective" 
                        :key="index"
                        class="flex items-start text-gray-600 dark:text-slate-300 text-sm"
                    >
                        <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 mr-3"></span>
                        {{ objective }}
                    </li>
                    <li v-if="!scenario.objective?.length" class="text-gray-500 italic text-sm">
                        No specific objectives listed.
                    </li>
                </ul>
            </div>

            <!-- Patient Profile -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <Users class="w-5 h-5 text-blue-500 mr-2" />
                    Initial Patient Profile
                </h3>
                <div class="space-y-4">
                    <div v-if="scenario.initial_patient_state" class="space-y-3">
                         <div v-for="(value, key) in scenario.initial_patient_state" :key="key" class="border-b border-gray-100 dark:border-gray-700/50 pb-2 last:border-0 last:pb-0">
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">
                                {{ key.replace(/_/g, ' ') }}
                            </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ value }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 italic text-sm">
                        Patient data will be revealed upon simulation start.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
