<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import { simulationService, type SimulationSession } from '@/services/simulation';
import ChatInterface from '@/components/ChatInterface.vue';
import Navbar from '@/components/Navbar.vue';
import { Activity, Heart, Thermometer, Wind, Droplets } from 'lucide-vue-next';

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

// Note: Future improvement would be to listen for vitals updates from ChatInterface
</script>

<template>
    <Head title="Simulation" />
    <div class="min-h-screen bg-slate-950 text-white font-sans selection:bg-emerald-500/30 overflow-hidden flex flex-col">
        <Navbar />

        <main class="flex-1 pt-20 flex overflow-hidden">
            <!-- Left Panel: Patient State / Context -->
            <div class="w-80 lg:w-96 bg-black/40 border-r border-white/10 p-6 flex flex-col gap-6 overflow-y-auto">
                <div v-if="loading" class="flex items-center gap-3 text-slate-400 animate-pulse">
                    <Activity class="w-5 h-5 animate-spin" />
                    <span>Initializing Context...</span>
                </div>
                
                <template v-else-if="session">
                    <!-- Scenario Info -->
                    <div>
                        <div class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/10 text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2 border border-emerald-500/20">
                            Active Case
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2 leading-tight">{{ session.scenario?.title }}</h2>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ session.scenario?.description }}</p>
                    </div>

                    <div class="h-px bg-white/10"></div>

                    <!-- Vitals Section -->
                    <div>
                        <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Real-time Biometrics</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <div 
                                v-for="(val, key) in Object.fromEntries(Object.entries(session.scenario?.initial_patient_state?.vitals || session.scenario?.initial_patient_state || {}).filter(([k]) => k !== 'profile' && k !== 'id' && k !== 'patient_profile'))" 
                                :key="key"
                                class="p-4 bg-white/5 rounded-xl border border-white/5 hover:border-white/10 transition-colors group"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ key }}</span>
                                    <component 
                                        :is="String(key).toLowerCase().includes('hr') || String(key).toLowerCase().includes('heart') ? Heart : 
                                            String(key).toLowerCase().includes('temp') ? Thermometer : 
                                            String(key).toLowerCase().includes('rr') || String(key).toLowerCase().includes('resp') ? Wind : 
                                            String(key).toLowerCase().includes('spo2') || String(key).toLowerCase().includes('ox') ? Droplets : Activity" 
                                        class="w-3.5 h-3.5 text-slate-600 group-hover:text-emerald-500/70 transition-colors"
                                    />
                                </div>
                                <div class="text-2xl font-mono text-emerald-400 tracking-tighter">
                                    {{ val }}
                                    <span class="text-[10px] text-slate-600 ml-1 font-sans tracking-normal">
                                        {{ String(key).toLowerCase().includes('hr') ? 'BPM' : 
                                           String(key).toLowerCase().includes('temp') ? '°C' : 
                                           String(key).toLowerCase().includes('spo2') ? '%' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                         <div v-if="!session.scenario?.initial_patient_state" class="text-slate-500 text-xs italic bg-white/5 p-4 rounded-xl border border-white/5">
                            No telemetry data available for this case.
                        </div>
                    </div>

                    <!-- Objectives -->
                    <div class="mt-auto">
                        <div class="bg-emerald-500/5 rounded-2xl p-4 border border-emerald-500/10">
                            <h4 class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2">Primary Objective</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                {{ session.scenario?.objective?.[0] || 'Stabilize patient and perform initial assessment.' }}
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Right Panel: Chat Interface -->
            <div class="flex-1 bg-black/20 overflow-hidden flex flex-col relative">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>
                <ChatInterface 
                    v-if="session" 
                    :session-id="session.id" 
                    :initial-turns="session.turns || []"
                />
            </div>
        </main>
    </div>
</template>
