<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils'; // Ensure utility is available

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navLinks = [
    { name: 'Features', href: '#' },
    { name: 'Pricing', href: '#' },
    { name: 'Enterprise', href: '#' },
];
</script>

<template>
    <nav
        :class="
            cn(
                'fixed top-0 right-0 left-0 z-50 border-b border-transparent transition-all duration-300',
                isScrolled
                    ? 'border-white/5 bg-black/70 py-4 shadow-2xl backdrop-blur-xl'
                    : 'bg-transparent py-6',
            )
        "
    >
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div
                    class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-[0_0_15px_rgba(16,185,129,0.4)]"
                ></div>
                <span
                    :class="
                        cn(
                            'text-xl font-semibold tracking-tighter transition-colors',
                            isScrolled ? 'text-white' : 'text-white',
                        )
                    "
                >
                    Clinical Context
                </span>
            </div>

            <!-- Desktop Links -->
            <div class="hidden gap-8 md:flex">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    class="text-sm font-medium text-slate-300 transition-colors hover:text-white"
                >
                    {{ link.name }}
                </a>
            </div>

            <!-- CTA -->
            <div>
                <Button
                    v-if="$page.props.auth.user"
                    :as="Link"
                    href="/dashboard"
                    variant="secondary"
                    class="rounded-full bg-white font-semibold text-emerald-900 transition-transform hover:scale-105 hover:bg-emerald-50 active:scale-95"
                >
                    Dashboard
                </Button>
                <Button
                    v-else
                    :as="Link"
                    href="/login"
                    variant="secondary"
                    class="rounded-full bg-white font-semibold text-emerald-900 transition-transform hover:scale-105 hover:bg-emerald-50 active:scale-95"
                >
                    Sign In
                </Button>
            </div>
        </div>
    </nav>
</template>
