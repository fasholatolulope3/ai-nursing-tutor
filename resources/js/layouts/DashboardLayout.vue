<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Sun,
    Moon,
    User as UserIcon,
    X,
    Check,
    Menu,
} from 'lucide-vue-next';
import { Toaster } from 'vue-sonner';
import DashboardSidebar from '@/components/ui/DashboardSidebar.vue';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps<{
    title?: string;
    description?: string;
}>();

const {
    notifications,
    unreadCount,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
} = useNotifications();
const isDark = ref(false);
const sidebarOpen = ref(true);
const showNotifications = ref(false);
const page = usePage();

// Initialize theme from local storage or system preference
onMounted(() => {
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
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
    <div
        :class="[
            'flex min-h-screen w-full bg-gray-50 font-sans text-gray-900 transition-colors duration-300 dark:bg-black dark:text-gray-100',
        ]"
    >
        <Toaster position="top-right" rich-colors />
        <!-- Sidebar -->
        <DashboardSidebar :open="sidebarOpen" @toggle="toggleSidebar" />

        <!-- Mobile Overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm lg:hidden dark:bg-black/40"
            @click="toggleSidebar"
        ></div>

        <!-- Main Content -->
        <div
            class="flex min-w-0 flex-1 flex-col overflow-hidden transition-all duration-300"
        >
            <!-- Top Header / Navigation -->
            <header
                class="z-10 flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 dark:border-white/10 dark:bg-black"
            >
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button
                        @click="toggleSidebar"
                        class="-ml-2 rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-white/10"
                    >
                        <Menu class="h-6 w-6" />
                    </button>

                    <!-- Page Title / Breadcrumbs -->
                    <div class="min-w-0">
                        <h1
                            class="truncate bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-lg font-bold text-transparent sm:text-xl dark:from-white dark:to-gray-400"
                        >
                            {{ title || 'Dashboard' }}
                        </h1>
                        <p
                            v-if="description"
                            class="mt-0.5 hidden truncate text-xs text-gray-500 sm:block dark:text-gray-400"
                        >
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
                            class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/10"
                        >
                            <Bell class="h-5 w-5" />
                            <span
                                v-if="unreadCount > 0"
                                class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full border border-white bg-red-500 dark:border-black"
                            ></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div
                            v-if="showNotifications"
                            class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl dark:border-white/10 dark:bg-gray-900"
                        >
                            <div
                                class="flex items-center justify-between border-b border-gray-200 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5"
                            >
                                <h3 class="text-sm font-semibold">
                                    Notifications
                                </h3>
                                <button
                                    v-if="unreadCount > 0"
                                    @click="markAllAsRead"
                                    class="text-xs font-medium text-emerald-600 hover:text-emerald-500"
                                >
                                    Mark all read
                                </button>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <div
                                    v-if="notifications.length === 0"
                                    class="p-8 text-center text-sm text-gray-500"
                                >
                                    No notifications yet.
                                </div>
                                <div v-else>
                                    <div
                                        v-for="notification in notifications"
                                        :key="notification.id"
                                        class="group cursor-pointer border-b border-gray-100 p-3 transition-colors hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5"
                                        :class="{
                                            'bg-emerald-50/30 dark:bg-emerald-900/10':
                                                !notification.read_at,
                                        }"
                                        @click="markAsRead(notification.id)"
                                    >
                                        <div
                                            class="flex items-start justify-between gap-3"
                                        >
                                            <div class="flex-1">
                                                <p
                                                    class="truncate text-sm font-medium text-gray-800 dark:text-gray-200"
                                                >
                                                    {{
                                                        notification.data
                                                            .title ||
                                                        'Notification'
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    {{
                                                        notification.data
                                                            .message ||
                                                        'You have a new update.'
                                                    }}
                                                </p>
                                                <p
                                                    class="mt-1.5 text-[10px] text-gray-400"
                                                >
                                                    {{
                                                        notification.created_at
                                                    }}
                                                </p>
                                            </div>
                                            <button
                                                v-if="!notification.read_at"
                                                @click.stop="
                                                    markAsRead(notification.id)
                                                "
                                                class="text-emerald-500 opacity-0 transition-opacity group-hover:opacity-100"
                                                title="Mark as read"
                                            >
                                                <Check class="h-3 w-3" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overlay to close -->
                        <div
                            v-if="showNotifications"
                            @click="showNotifications = false"
                            class="fixed inset-0 z-40 cursor-default bg-transparent"
                        ></div>
                    </div>

                    <!-- Theme Toggle -->
                    <button
                        @click="toggleTheme"
                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/10"
                    >
                        <Sun v-if="isDark" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>

                    <!-- User Profile -->
                    <Link
                        href="/dashboard/settings"
                        class="group flex items-center gap-2 pl-2"
                    >
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 p-[1px]"
                        >
                            <div
                                class="h-full w-full overflow-hidden rounded-full bg-white dark:bg-black"
                            >
                                <img
                                    v-if="$page.props.auth.user.avatar_path"
                                    :src="
                                        '/storage/' +
                                        $page.props.auth.user.avatar_path
                                    "
                                    alt="User"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-500 dark:bg-white/10"
                                >
                                    <UserIcon class="h-4 w-4" />
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto scroll-smooth p-4 sm:p-6"
            >
                <slot />
            </main>
        </div>
    </div>
</template>
