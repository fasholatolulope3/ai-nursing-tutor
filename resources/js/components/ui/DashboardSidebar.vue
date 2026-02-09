<script setup lang="ts">
import {
  Home,
  BookOpen,
  History,
  Activity,
  Award,
  Book,
  FileText,
  Settings,
  HelpCircle,
  ChevronsRight,
  ChevronDown,
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { useAdaptiveStudy } from '@/composables/useAdaptiveStudy';
import axios from 'axios';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'toggle'): void;
}>();

const recentCases = ref<any[]>([]);
const references = ref<any[]>([]);
const recommendedScenarios = ref<any[]>([]);
const recommendedReferences = ref<any[]>([]);
const { currentTopics, isRecommendationsActive } = useAdaptiveStudy();

const isLibraryOpen = ref(true);

const fetchRecommendations = async () => {
    if (!isRecommendationsActive.value) return;
    
    try {
        const response = await axios.post('/api/v1/recommendations', {
            topics: currentTopics.value
        });
        
        recommendedScenarios.value = response.data.scenarios || [];
        recommendedReferences.value = response.data.references || [];
    } catch (error) {
        console.error('Failed to fetch recommendations', error);
    }
};

watch(currentTopics, () => {
    fetchRecommendations();
});

onMounted(async () => {
    // 1. Fetch References (Public/Robust)
    try {
        const response = await axios.get('/api/v1/references');
        if (Array.isArray(response.data)) {
            references.value = response.data;
        } else if (response.data && Array.isArray(response.data.data)) {
            // Handle Laravel Resource wrap
            references.value = response.data.data;
        } else {
            console.error('References response is not an array:', response.data);
            references.value = [];
        }
    } catch (error) {
        console.error('Failed to load references', error);
    }

    // 2. Fetch Simulations (Requires Auth)
    try {
        const response = await axios.get('/api/v1/simulations');
        if (Array.isArray(response.data)) {
             recentCases.value = response.data;
        } else if (response.data && Array.isArray(response.data.data)) {
             recentCases.value = response.data.data;
        } else {
             recentCases.value = [];
        }
    } catch (error) {
        console.warn('Failed to load simulations', error);
    }
});
</script>

<template>
    <nav
        :class="[
            'fixed inset-y-0 left-0 z-50 flex flex-col h-screen border-r bg-white dark:bg-black transition-all duration-300 ease-in-out border-gray-200 dark:border-white/10 p-2 shadow-xl lg:sticky lg:top-0 lg:shadow-sm lg:z-20',
            open ? 'translate-x-0 w-64' : '-translate-x-full w-64 lg:translate-x-0 lg:w-20'
        ]"
    >
        <!-- Title Section -->
        <div class="mb-6 border-b border-gray-200 dark:border-white/10 pb-4 shrink-0">
            <div class="flex cursor-pointer items-center justify-between rounded-md p-2 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                <div class="flex items-center gap-3 overflow-hidden">
                    <!-- Logo -->
                    <div class="grid size-10 shrink-0 place-content-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-[0_0_15px_rgba(16,185,129,0.4)]">
                        <svg width="20" height="auto" viewBox="0 0 50 39" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-white">
                            <path d="M16.4992 2H37.5808L22.0816 24.9729H1L16.4992 2Z" />
                            <path d="M17.4224 27.102L11.4192 36H33.5008L49 13.0271H32.7024L23.2064 27.102H17.4224Z" />
                        </svg>
                    </div>
                    
                    <div :class="['transition-opacity duration-200 min-w-max', open ? 'opacity-100' : 'opacity-0 w-0 hidden']">
                        <div class="flex flex-col">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Clinical Context
                            </span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">
                                AI Nursing Tutor
                            </span>
                        </div>
                    </div>
                </div>
                <ChevronDown v-if="open" class="h-4 w-4 text-gray-400 dark:text-gray-500 shrink-0" />
            </div>
        </div>

        <!-- Navigation Options - Scrollable Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar space-y-1 min-h-0 pb-4">
            <Link
                v-for="item in [
                    { icon: Home, title: 'Dashboard', href: '/dashboard' },
                    { icon: BookOpen, title: 'Scenarios', href: '/dashboard/scenarios' },
                    { icon: History, title: 'History', href: '/dashboard/history' },
                ]"
                :key="item.title"
                :href="item.href"
                :class="[
                    'relative flex h-11 w-full items-center rounded-md transition-all duration-200 group overflow-hidden',
                    $page.url === item.href
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 shadow-sm border-l-2 border-emerald-500'
                        : 'text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-slate-200'
                ]"
            >
                <div class="grid h-full w-12 place-content-center shrink-0">
                    <component :is="item.icon" class="h-4 w-4" />
                </div>
                    
                <span :class="['text-sm font-medium transition-opacity duration-200 whitespace-nowrap', open ? 'opacity-100' : 'opacity-0 w-0']">
                    {{ item.title }}
                </span>
            </Link>

            <!-- Previous Cases (from screenshot) -->
            <div v-if="recentCases.length > 0 && open" class="mt-6 mb-2 px-4 animate-fadeIn">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">
                    Previous Cases
                </h3>
            </div>
            <div v-if="recentCases.length > 0" class="space-y-0.5">
                <Link 
                    v-for="session in recentCases.slice(0, 5)"
                    :key="'recent-'+session.id"
                    :href="`/simulation/${session.id}`"
                    class="w-full flex items-center h-9 px-2 rounded-md hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 text-gray-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 group transition-colors"
                    :title="session.scenario?.title"
                >
                    <div class="grid h-full w-12 place-content-center shrink-0">
                        <History class="h-4 w-4 opacity-40 group-hover:opacity-100" />
                    </div>
                    <span :class="['text-xs font-medium truncate transition-opacity duration-200', open ? 'opacity-100' : 'opacity-0 w-0']">
                        {{ session.scenario?.title || 'Sim Session' }}
                    </span>
                </Link>
            </div>

            <!-- Recommended Study -->
            <div v-if="isRecommendationsActive && open" class="mt-6 mb-2 px-4 animate-fadeIn">
                <h3 class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                    <Sparkles class="w-3 h-3" /> Recommended
                </h3>
            </div>
            <div v-if="isRecommendationsActive" class="space-y-1 animate-fadeIn">
                <Link 
                    v-for="scenario in recommendedScenarios"
                    :key="'rec-s-'+scenario.id"
                    :href="`/scenarios/${scenario.id}`"
                    class="w-full flex items-center h-9 px-2 rounded-md bg-emerald-50/50 dark:bg-emerald-900/10 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/20 group transition-colors"
                >
                    <div class="grid h-full w-12 place-content-center shrink-0">
                        <Activity class="h-4 w-4" />
                    </div>
                    <span :class="['text-xs font-medium truncate transition-opacity duration-200', open ? 'opacity-100' : 'opacity-0 w-0']">
                        {{ scenario.title }}
                    </span>
                </Link>
            </div>

            <!-- Library -->
            <div class="mt-8 mb-2 px-4" v-if="open">
                <button 
                    @click="isLibraryOpen = !isLibraryOpen"
                    class="w-full flex items-center justify-between text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group"
                >
                    <span class="flex items-center gap-2">
                        <BookOpen class="w-3 h-3 text-emerald-500/50 group-hover:text-emerald-500" /> Reference Library
                    </span>
                    <ChevronDown :class="['w-3 h-3 transition-transform duration-300', isLibraryOpen ? '' : '-rotate-90']" />
                </button>
            </div>
            <div class="space-y-0.5 mt-1" v-if="isLibraryOpen || !open">
                <a 
                    v-for="doc in references"
                    :key="doc.id"
                    :href="doc.file_path"
                    target="_blank"
                    class="w-full flex items-center h-10 px-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5 hover:text-emerald-700 dark:hover:text-emerald-300 group transition-all duration-200"
                >
                    <div class="grid h-full w-12 place-content-center shrink-0">
                        <FileText class="h-4 w-4 opacity-40 group-hover:opacity-100 group-hover:scale-110 transition-transform" />
                    </div>
                    <span :class="['text-xs font-medium truncate transition-opacity duration-200', open ? 'opacity-100' : 'opacity-0 w-0']">
                        {{ doc.title }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Sticky Bottom Section -->
        <div class="shrink-0 border-t border-gray-200 dark:border-white/10 pt-4 pb-2 space-y-1 bg-white dark:bg-black">
            <div v-if="open" class="px-3 py-1 text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">
                Account
            </div>
            <Link
                v-for="item in [
                    { icon: Settings, title: 'Settings', href: '/dashboard/settings' },
                    { icon: HelpCircle, title: 'Help & Support', href: '/dashboard/help' },
                ]"
                :key="item.title"
                :href="item.href"
                :class="[
                    'relative flex h-11 w-full items-center rounded-md transition-all duration-200 overflow-hidden',
                    $page.url === item.href
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 shadow-sm border-l-2 border-emerald-500'
                        : 'text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-slate-200'
                ]"
            >
                <div class="grid h-full w-12 place-content-center shrink-0">
                    <component :is="item.icon" class="h-4 w-4" />
                </div>
                <span :class="['text-sm font-medium transition-opacity duration-200 whitespace-nowrap', open ? 'opacity-100' : 'opacity-0 w-0']">
                    {{ item.title }}
                </span>
            </Link>

            <!-- Toggle Button - No longer absolute -->
            <button
                @click="emit('toggle')"
                class="w-full mt-2 border-t border-gray-100 dark:border-white/5 transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
            >
                <div class="flex items-center p-3 overflow-hidden">
                    <div class="grid size-10 place-content-center shrink-0">
                        <ChevronsRight :class="['h-4 w-4 transition-transform duration-300 text-gray-500 dark:text-slate-400', open ? 'rotate-180' : '']" />
                    </div>
                    <span :class="['text-sm font-medium text-gray-600 dark:text-slate-300 transition-opacity duration-200 whitespace-nowrap', open ? 'opacity-100' : 'opacity-0 w-0']">
                        Hide Sidebar
                    </span>
                </div>
            </button>
        </div>
    </nav>
</template>
