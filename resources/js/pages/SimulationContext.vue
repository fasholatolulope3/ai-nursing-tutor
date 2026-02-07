<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import { simulationService, type SimulationSession } from '@/services/simulation';
import ChatInterface from '@/components/ChatInterface.vue';
import Navbar from '@/components/Navbar.vue';

const props = defineProps<{
    sessionId: number;
}>();

const session = ref<SimulationSession | null>(null);
const loading = ref(true);

onMounted(async () => {
    try {
        session.value = await simulationService.getSimulation(props.sessionId);
    } catch (error) {
        console.error("Failed to load session", error);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head title="Simulation" />
    <div class="min-h-screen bg-black text-white font-sans selection:bg-emerald-500/30">
        <Navbar />

        <main class="pt-24 px-6 max-w-7xl mx-auto h-[calc(100vh-6rem)] flex gap-6">
            <!-- Left Panel: Patient State / Context -->
            <div class="w-1/3 bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md flex flex-col gap-4">
                <div v-if="loading" class="text-slate-400">Loading Context...</div>
                <template v-else-if="session">
                    <div>
                        <h2 class="text-xl font-light text-emerald-400 mb-1">{{ session.scenario?.title }}</h2>
                        <p class="text-sm text-slate-400">{{ session.scenario?.description }}</p>
                    </div>

                    <div class="h-px bg-white/10 my-2"></div>

                    <div>
                        <h3 class="text-sm font-medium text-slate-300 uppercase tracking-wider mb-3">Patient Vitals (Initial)</h3>
                        <div class="grid grid-cols-2 gap-4" v-if="session.scenario?.initial_patient_state?.vitals">
                            <div class="p-3 bg-black/40 rounded-lg border border-white/5">
                                <div class="text-xs text-slate-500">HR</div>
                                <div class="text-xl font-mono text-emerald-300">{{ session.scenario.initial_patient_state.vitals.HR }} <span class="text-xs text-slate-600">bpm</span></div>
                            </div>
                            <div class="p-3 bg-black/40 rounded-lg border border-white/5">
                                <div class="text-xs text-slate-500">BP</div>
                                <div class="text-xl font-mono text-emerald-300">{{ session.scenario.initial_patient_state.vitals.BP }}</div>
                            </div>
                            <div class="p-3 bg-black/40 rounded-lg border border-white/5">
                                <div class="text-xs text-slate-500">SpO2</div>
                                <div class="text-xl font-mono text-emerald-300">{{ session.scenario.initial_patient_state.vitals.SpO2 }}%</div>
                            </div>
                            <div class="p-3 bg-black/40 rounded-lg border border-white/5">
                                <div class="text-xs text-slate-500">RR</div>
                                <div class="text-xl font-mono text-emerald-300">{{ session.scenario.initial_patient_state.vitals.RR }}</div>
                            </div>
                             <div class="p-3 bg-black/40 rounded-lg border border-white/5 col-span-2">
                                <div class="text-xs text-slate-500">Temp</div>
                                <div class="text-xl font-mono text-emerald-300">{{ session.scenario.initial_patient_state.vitals.Temp }}°C</div>
                            </div>
                        </div>
                         <div v-else class="text-slate-500 italic">No vitals data available.</div>
                    </div>
                </template>
            </div>

            <!-- Right Panel: Chat Interface -->
            <div class="flex-1 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md overflow-hidden flex flex-col">
                <ChatInterface 
                    v-if="session" 
                    :session-id="session.id" 
                    :initial-turns="session.turns || []"
                />
            </div>
        </main>
    </div>
</template>
