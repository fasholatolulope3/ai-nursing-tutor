<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { 
  Clock, 
  ChevronRight, 
  Award,
  Activity,
  MessageSquare,
  PlayCircle,
  Trash2,
  History as HistoryIcon
} from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

defineOptions({
  layout: DashboardLayout,
});

const props = defineProps<{
  history: {
    data: Array<{
      id: number;
      type: 'simulation' | 'ai_chat';
      title: string;
      subtitle: string;
      difficulty: string;
      outcome: string;
      duration: string;
      date: string;
      time_ago: string;
      link: string | null;
    }>;
    links: Array<any>;
    current_page: number;
    last_page: number;
    total: number;
  };
}>();

const getDifficultyColor = (difficulty: string) => {
  switch (difficulty.toLowerCase()) {
    case 'beginner': return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400';
    case 'intermediate': return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400';
    case 'advanced': return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400';
    default: return 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400';
  }
};

const deleteItem = (item: any) => {
  if (confirm(`Are you sure you want to delete this ${item.type === 'simulation' ? 'simulation' : 'chat'}?`)) {
    router.delete(`/dashboard/history/${item.id}`, {
      data: { type: item.type },
      onSuccess: () => {
        // Optional: Show toast or feedback
      }
    });
  }
};
</script>

<template>
  <Head title="History" />

  <div class="max-w-7xl mx-auto space-y-8 px-4 sm:px-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 py-2">
      <div>
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500 dark:from-white dark:via-gray-300 dark:to-gray-500 tracking-tight">
          Activity History
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Review your clinical trajectory and past simulations.
        </p>
      </div>
      <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 shadow-sm">
        <Activity class="w-4 h-4 text-emerald-500" />
        <span class="text-sm font-bold">{{ history.total }} Total Records</span>
      </div>
    </div>

    <!-- History Grid -->
    <div v-if="history.total === 0" class="py-20 text-center border-2 border-dashed border-gray-200 dark:border-white/5 rounded-[40px] bg-gray-50/50 dark:bg-white/[0.02]">
        <div class="w-20 h-20 bg-white dark:bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl border border-gray-100 dark:border-white/10">
          <HistoryIcon class="w-10 h-10 text-gray-300 dark:text-slate-600" />
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">Quiet Shift</h3>
        <p class="text-gray-500 dark:text-slate-500 max-w-sm mx-auto mb-8 text-sm">You haven't recorded any clinical activities yet. Start a simulation to begin your journey.</p>
        <Link 
          href="/dashboard/scenarios" 
          class="inline-flex items-center px-8 h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl transition-all text-xs font-bold uppercase tracking-widest shadow-lg shadow-emerald-500/20"
        >
          View Scenarios
        </Link>
    </div>

    <!-- Cards Layout -->
    <div v-else class="grid gap-4">
      <div 
        v-for="item in history.data" 
        :key="item.id + item.type"
        class="group relative bg-white dark:bg-slate-900/50 border border-gray-200 dark:border-white/10 p-5 rounded-3xl hover:border-emerald-500/40 transition-all duration-300 flex flex-col sm:flex-row sm:items-center gap-6 shadow-sm hover:shadow-xl"
      >
        <!-- Icon & Type -->
        <div class="flex items-center gap-4 shrink-0">
          <div class="p-4 rounded-2xl transition-all duration-300" :class="item.type === 'simulation' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white' : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white'">
            <PlayCircle v-if="item.type === 'simulation'" class="w-6 h-6" />
            <MessageSquare v-else class="w-6 h-6" />
          </div>
          <div class="sm:hidden">
            <h3 class="font-bold text-gray-900 dark:text-white leading-tight underline-offset-4 decoration-emerald-500/30 group-hover:underline">{{ item.title }}</h3>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ item.type === 'simulation' ? 'Clinical Simulation' : 'AI Assistant Query' }}</span>
          </div>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0 hidden sm:block">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ item.title }}</h3>
            <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
               <span class="flex items-center gap-1.5">
                  <Clock class="w-3.5 h-3.5 opacity-50" /> {{ item.time_ago }}
               </span>
               <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/10"></span>
               <span class="truncate max-w-[300px] italic">{{ item.subtitle }}</span>
            </div>
        </div>

        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-4 sm:gap-8 shrink-0">
            <div v-if="item.type === 'simulation'" class="space-y-1">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Outcome</p>
              <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
                <Award v-if="item.outcome.includes('%')" class="w-4 h-4 text-emerald-500" />
                {{ item.outcome }}
              </div>
            </div>
            
            <div class="space-y-1">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Duration</p>
              <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                {{ item.duration }}
              </div>
            </div>

            <div class="flex items-center gap-2">
              <Link 
                v-if="item.link"
                :href="item.link"
                class="h-10 px-6 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-emerald-600 dark:hover:bg-emerald-500 dark:hover:text-white transition-all font-bold text-[10px] uppercase tracking-widest flex items-center justify-center shadow-lg shadow-gray-900/10"
              >
                Restore
              </Link>
              <button 
                @click="deleteItem(item)"
                class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="history.total > 0 && history.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100 dark:border-white/5">
      <div class="text-sm text-gray-500 dark:text-gray-400">
        Showing <span class="font-bold text-gray-900 dark:text-white">{{ history.current_page * history.data.length }}</span> of <span class="font-bold text-gray-900 dark:text-white">{{ history.total }}</span> clinical activities
      </div>
      <div class="flex items-center gap-1">
        <Link
          v-for="(link, i) in history.links"
          :key="i"
          :href="link.url || '#'"
          :class="[
            'min-w-[40px] h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all',
            link.active
              ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20'
              : !link.url
                ? 'text-gray-300 dark:text-white/10 cursor-not-allowed'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5'
          ]"
          v-html="link.label"
        />
      </div>
    </div>
  </div>
</template>
