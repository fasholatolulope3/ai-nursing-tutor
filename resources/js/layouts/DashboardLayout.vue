<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Bell,
    Sun,
    Moon,
    User,
} from 'lucide-vue-next';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';

const props = defineProps<{
    title?: string;
    description?: string;
}>();

// State
const isDark = ref(false);
const sidebarOpen = ref(true);

// Dark Mode Logic
onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
    } else {
        isDark.value = false;
    }
});

watch(isDark, (val) => {
    if (val) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div :class="['flex min-h-screen w-full font-sans', isDark ? 'dark' : '']">
        <div class="flex w-full bg-gray-50 dark:bg-black text-gray-900 dark:text-gray-100 transition-colors duration-300">
            
            <!-- Sidebar -->
            <DashboardSidebar 
                :open="sidebarOpen" 
                @toggle="toggleSidebar"
            />

            <!-- Content Area -->
            <div class="flex-1 bg-gray-50 dark:bg-black p-6 overflow-auto h-screen transition-all duration-300">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ title }}</h1>
                        <p class="text-gray-600 dark:text-slate-400 mt-1">{{ description }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="relative p-2 rounded-lg bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <Bell class="h-5 w-5" />
                            <span class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full"></span>
                        </button>
                        <button
                            @click="isDark = !isDark"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-white/10 hover:text-gray-900 dark:hover:text-white transition-colors"
                        >
                            <component :is="isDark ? Sun : Moon" class="h-4 w-4" />
                        </button>
                        <Link href="/user/profile" class="p-2 rounded-lg bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <User class="h-5 w-5" />
                        </Link>
                    </div>
                </div>

                <!-- Page Content -->
                <slot />
            </div>
        </div>
    </div>
</template>
