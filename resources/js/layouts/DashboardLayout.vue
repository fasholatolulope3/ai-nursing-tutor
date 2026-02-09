<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, Sun, Moon, User as UserIcon, X, Check, Menu } from 'lucide-vue-next';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps<{
    title?: string;
    description?: string;
}>();

const { notifications, unreadCount, fetchNotifications, markAsRead, markAllAsRead } = useNotifications();
const isDark = ref(false);
const sidebarOpen = ref(true);
const showNotifications = ref(false);
const page = usePage();

// Initialize theme from local storage or system preference
onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
    
    // Check screen size for initial sidebar state
    if (window.innerWidth < 1024) {
        sidebarOpen.value = false;
    }

    fetchNotifications();
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div :class="['flex min-h-screen w-full font-sans bg-gray-50 dark:bg-black text-gray-900 dark:text-gray-100 transition-colors duration-300']">
        
        <!-- Sidebar -->
        <DashboardSidebar 
            :open="sidebarOpen" 
            @toggle="toggleSidebar"
        />

        <!-- Mobile Overlay -->
        <div 
            v-if="sidebarOpen" 
            class="fixed inset-0 bg-black/20 dark:bg-black/40 backdrop-blur-sm z-40 lg:hidden"
            @click="toggleSidebar"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden transition-all duration-300">
            
            <!-- Top Header / Navigation -->
            <header class="h-16 flex items-center justify-between px-4 sm:px-6 bg-white dark:bg-black border-b border-gray-200 dark:border-white/10 shrink-0 z-10">
                
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button 
                        @click="toggleSidebar"
                        class="p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-white/10 dark:text-gray-400 transition-colors lg:hidden"
                    >
                        <Menu class="w-6 h-6" />
                    </button>

                    <!-- Page Title / Breadcrumbs -->
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 truncate">
                            {{ title || 'Dashboard' }}
                        </h1>
                        <p v-if="description" class="hidden sm:block text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">

                    <!-- Page Actions Slot -->
                    <div v-if="$slots.actions" class="mr-2">
                        <slot name="actions" />
                    </div>
                    
                    <!-- Notifications -->
                    <div class="relative">
                        <button 
                            @click="showNotifications = !showNotifications"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-white/10 dark:text-gray-400 transition-colors relative"
                        >
                            <Bell class="w-5 h-5" />
                            <span v-if="unreadCount > 0" class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-black"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-xl z-50 overflow-hidden">
                            <div class="p-3 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gray-50 dark:bg-white/5">
                                <h3 class="text-sm font-semibold">Notifications</h3>
                                <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-emerald-600 hover:text-emerald-500 font-medium">
                                    Mark all read
                                </button>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500 text-sm">
                                    No notifications yet.
                                </div>
                                <div v-else>
                                    <div 
                                        v-for="notification in notifications" 
                                        :key="notification.id"
                                        class="p-3 border-b border-gray-100 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer group"
                                        :class="{'bg-emerald-50/30 dark:bg-emerald-900/10': !notification.read_at}"
                                        @click="markAsRead(notification.id)"
                                    >
                                        <div class="flex justify-between items-start gap-3">
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-medium truncate">
                                                    {{ notification.data.title || 'Notification' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                                    {{ notification.data.message || 'You have a new update.' }}
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1.5">{{ notification.created_at }}</p>
                                            </div>
                                            <button 
                                                v-if="!notification.read_at"
                                                @click.stop="markAsRead(notification.id)"
                                                class="text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                                title="Mark as read"
                                            >
                                                <Check class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Overlay to close -->
                        <div v-if="showNotifications" @click="showNotifications = false" class="fixed inset-0 z-40 bg-transparent cursor-default"></div>
                    </div>

                    <!-- Theme Toggle -->
                    <button 
                        @click="toggleTheme"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-white/10 dark:text-gray-400 transition-colors"
                    >
                        <Sun v-if="isDark" class="w-5 h-5" />
                        <Moon v-else class="w-5 h-5" />
                    </button>

                    <!-- User Profile -->
                    <Link href="/dashboard/settings" class="pl-2 flex items-center gap-2 group">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 p-[1px]">
                             <div class="w-full h-full rounded-full overflow-hidden bg-white dark:bg-black">
                                <img 
                                    v-if="$page.props.auth.user.avatar_path" 
                                    :src="'/storage/' + $page.props.auth.user.avatar_path" 
                                    alt="User" 
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-white/10 text-gray-500">
                                    <UserIcon class="w-4 h-4" />
                                </div>
                             </div>
                        </div>
                    </Link>

                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
                <slot />
            </main>

        </div>
    </div>
</template>
