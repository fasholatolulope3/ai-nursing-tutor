<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
    BrainCircuit,
    CheckCircle2,
    CircleDashed,
    ChevronDown,
    ChevronUp,
} from 'lucide-vue-next';

interface ThoughtStep {
    id: string;
    description: string;
    details?: string;
    status: 'pending' | 'active' | 'completed';
}

const props = defineProps<{
    isThinking: boolean;
    trace: ThoughtStep[];
}>();

const isExpanded = ref(true);

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};
</script>

<template>
    <div
        v-if="isThinking || trace.length > 0"
        class="mx-auto mb-6 w-full max-w-3xl"
    >
        <div
            class="overflow-hidden rounded-xl border border-emerald-500/20 bg-emerald-50/50 backdrop-blur-sm transition-all duration-300 dark:bg-emerald-900/10"
            :class="{ 'shadow-lg shadow-emerald-500/5': isThinking }"
        >
            <!-- Header -->
            <div
                @click="toggleExpand"
                class="flex cursor-pointer items-center justify-between bg-emerald-100/50 px-4 py-3 transition-colors hover:bg-emerald-100/80 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/30"
            >
                <div class="flex items-center gap-2.5">
                    <div class="relative">
                        <BrainCircuit
                            class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                        />
                        <span
                            v-if="isThinking"
                            class="absolute -top-0.5 -right-0.5 h-2 w-2 animate-ping rounded-full bg-emerald-500"
                        ></span>
                    </div>
                    <span
                        class="text-sm font-medium text-emerald-900 dark:text-emerald-100"
                    >
                        {{
                            isThinking
                                ? 'Analyzing Clinical Context...'
                                : 'Reasoning Complete'
                        }}
                    </span>
                </div>

                <component
                    :is="isExpanded ? ChevronUp : ChevronDown"
                    class="h-4 w-4 text-emerald-600 dark:text-emerald-400"
                />
            </div>

            <!-- Trace Steps -->
            <div
                v-if="isExpanded"
                class="space-y-3 px-4 py-3"
                v-motion-slide-top
            >
                <div
                    v-for="(step, index) in trace"
                    :key="step.id"
                    class="flex items-start gap-3 text-sm"
                    v-motion
                    :initial="{ opacity: 0, x: -10 }"
                    :enter="{
                        opacity: 1,
                        x: 0,
                        transition: { delay: index * 100 },
                    }"
                >
                    <!-- Status Icon -->
                    <div class="mt-0.5 shrink-0">
                        <CircleDashed
                            v-if="step.status === 'pending'"
                            class="h-4 w-4 text-slate-400"
                        />
                        <div
                            v-else-if="step.status === 'active'"
                            class="h-4 w-4 animate-spin text-emerald-500"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-dasharray="4 4"
                                />
                            </svg>
                        </div>
                        <CheckCircle2
                            v-else
                            class="h-4 w-4 text-emerald-600 dark:text-emerald-400"
                        />
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <p
                            class="font-medium"
                            :class="{
                                'text-slate-500 dark:text-slate-500':
                                    step.status === 'pending',
                                'text-emerald-700 dark:text-emerald-300':
                                    step.status === 'active',
                                'text-slate-700 dark:text-slate-200':
                                    step.status === 'completed',
                            }"
                        >
                            {{ step.description }}
                        </p>
                        <p
                            v-if="step.details"
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            {{ step.details }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer/Progress Bar -->
            <div
                v-if="isThinking && isExpanded"
                class="h-1 w-full overflow-hidden bg-emerald-100 dark:bg-emerald-900/30"
            >
                <div
                    class="animate-progress-indeterminate h-full w-1/3 bg-emerald-500/50"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes progress-indeterminate {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(300%);
    }
}
.animate-progress-indeterminate {
    animation: progress-indeterminate 1.5s infinite linear;
}
</style>
