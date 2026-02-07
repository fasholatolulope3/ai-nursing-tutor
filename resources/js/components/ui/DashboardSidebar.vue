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

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'toggle'): void;
}>();
</script>

<template>
    <nav
        :class="[
            'sticky top-0 h-screen shrink-0 border-r transition-all duration-300 ease-in-out border-gray-200 dark:border-white/10 bg-white dark:bg-black p-2 shadow-sm z-20',
            open ? 'w-64' : 'w-20'
        ]"
    >
        <!-- Title Section -->
        <div class="mb-6 border-b border-gray-200 dark:border-white/10 pb-4">
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

        <!-- Navigation Options -->
        <div class="space-y-1 mb-8">
            <Link
                v-for="item in [
                    { icon: Home, title: 'Dashboard', href: '/dashboard' },
                    { icon: BookOpen, title: 'Scenarios', href: '/dashboard/scenarios', notifs: 3 },
                    { icon: History, title: 'History', href: '/dashboard/history' },
                    { icon: Activity, title: 'Performance', href: '/dashboard/performance' },
                    { icon: Book, title: 'Guidelines', href: '/dashboard/guidelines' },
                    { icon: FileText, title: 'Exam Prep', href: '/dashboard/exam-prep' },
                    { icon: Award, title: 'Achievements', href: '/dashboard/achievements', notifs: 8 },
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

                <span v-if="item.notifs && open" class="absolute right-3 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-xs text-white font-medium">
                    {{ item.notifs }}
                </span>
            </Link>
        </div>

        <!-- Bottom Config -->
            <div v-if="open" class="border-t border-gray-200 dark:border-white/10 pt-4 space-y-1">
            <div class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-slate-500 uppercase tracking-wide">
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
                <span class="text-sm font-medium transition-opacity duration-200 whitespace-nowrap">
                    {{ item.title }}
                </span>
            </Link>
        </div>

        <!-- Toggle -->
        <button
            @click="emit('toggle')"
            class="absolute bottom-0 left-0 right-0 border-t border-gray-200 dark:border-white/10 transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
        >
            <div class="flex items-center p-3 overflow-hidden">
                <div class="grid size-10 place-content-center shrink-0">
                    <ChevronsRight :class="['h-4 w-4 transition-transform duration-300 text-gray-500 dark:text-slate-400', open ? 'rotate-180' : '']" />
                </div>
                <span :class="['text-sm font-medium text-gray-600 dark:text-slate-300 transition-opacity duration-200 whitespace-nowrap', open ? 'opacity-100' : 'opacity-0 w-0']">
                    Hide
                </span>
            </div>
        </button>
    </nav>
</template>
