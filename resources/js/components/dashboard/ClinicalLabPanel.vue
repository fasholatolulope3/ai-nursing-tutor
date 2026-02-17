<script setup lang="ts">
import { ref } from 'vue';
import { FileText, Eye, CheckCircle, AlertCircle, X } from 'lucide-vue-next';

interface GroundedFact {
    id: string;
    text: string;
    confidence: number; // 0-1
    source: string; // "Page 1", "Image Header", etc.
}

interface ActiveDocument {
    id: string;
    name: string;
    type: 'pdf' | 'image';
    url: string;
    facts: GroundedFact[];
}

const props = defineProps<{
    document?: ActiveDocument | null;
    isLoading?: boolean;
    isOpen?: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const activeTab = ref<'preview' | 'facts'>('preview');
</script>

<template>
    <!-- Overlay for mobile/tablet -->
    <div
        v-if="isOpen"
        class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm xl:hidden"
        @click="emit('close')"
    ></div>

    <div
        :class="[
            'fixed inset-y-0 right-0 z-50 flex h-full w-full flex-col border-l border-gray-200 bg-white transition-all duration-300 sm:w-80 xl:sticky xl:top-0 xl:z-0 xl:w-96 dark:border-white/10 dark:bg-black',
            isOpen ? 'translate-x-0' : 'translate-x-full xl:translate-x-0',
        ]"
    >
        <!-- Header -->
        <div
            class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10"
        >
            <div class="flex items-center gap-2">
                <FileText class="h-4 w-4 text-emerald-500" />
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    Clinical Lab
                </h3>
            </div>

            <div class="flex items-center gap-2">
                <span
                    v-if="document"
                    class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-white/5 dark:text-gray-400"
                >
                    {{ document.type === 'pdf' ? 'PDF' : 'IMG' }}
                </span>
                <button
                    @click="emit('close')"
                    class="rounded-md p-1 text-gray-400 hover:text-gray-600 lg:hidden dark:hover:text-gray-200"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="!document && !isLoading"
            class="flex flex-1 flex-col items-center justify-center p-6 text-center text-gray-500"
        >
            <div
                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 dark:bg-white/5"
            >
                <Eye class="h-8 w-8 text-gray-300 dark:text-gray-600" />
            </div>
            <p class="font-medium text-gray-900 dark:text-gray-200">
                No Document Selected
            </p>
            <p class="mt-1 max-w-[200px] text-sm">
                Select a file from the library or upload a new clinical document
                to analyze.
            </p>
        </div>

        <!-- Content -->
        <div v-else class="flex flex-1 flex-col overflow-hidden">
            <!-- Document Meta -->
            <div
                class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5"
            >
                <h4
                    class="truncate text-sm font-medium text-gray-900 dark:text-white"
                    :title="document?.name"
                >
                    {{ document?.name || 'Processing...' }}
                </h4>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 dark:border-white/10">
                <button
                    @click="activeTab = 'preview'"
                    class="flex-1 border-b-2 py-2 text-sm font-medium transition-colors"
                    :class="
                        activeTab === 'preview'
                            ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    "
                >
                    Preview
                </button>
                <button
                    @click="activeTab = 'facts'"
                    class="flex-1 border-b-2 py-2 text-sm font-medium transition-colors"
                    :class="
                        activeTab === 'facts'
                            ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    "
                >
                    Grounded Facts
                </button>
            </div>

            <!-- Tab Content -->
            <div class="custom-scrollbar flex-1 overflow-y-auto p-4">
                <!-- Preview Tab -->
                <div v-if="activeTab === 'preview'">
                    <div
                        v-if="isLoading"
                        class="flex h-48 items-center justify-center"
                    >
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-b-2 border-emerald-500"
                        ></div>
                    </div>
                    <div
                        v-else
                        class="group relative overflow-hidden rounded-lg border border-gray-200 bg-gray-100 shadow-sm dark:border-white/10 dark:bg-gray-900"
                    >
                        <!-- Valid Mock Preview -->
                        <div
                            class="flex aspect-[3/4] items-center justify-center text-gray-400"
                        >
                            <img
                                v-if="document?.type === 'image'"
                                :src="document.url"
                                class="h-full w-full object-cover"
                                alt="Document Preview"
                            />
                            <div v-else class="p-4 text-center">
                                <FileText
                                    class="mx-auto mb-2 h-12 w-12 opacity-50"
                                />
                                <span class="text-xs"
                                    >PDF Preview Placeholder</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facts Tab -->
                <div v-else class="space-y-4">
                    <div
                        v-if="document?.facts.length === 0"
                        class="py-8 text-center text-gray-500"
                    >
                        <AlertCircle
                            class="mx-auto mb-2 h-8 w-8 text-amber-500/50"
                        />
                        <p class="text-sm">No facts extracted yet.</p>
                    </div>

                    <div
                        v-for="fact in document?.facts"
                        :key="fact.id"
                        class="group cursor-help rounded-lg border border-emerald-100 bg-emerald-50/50 p-3 transition-colors hover:border-emerald-300 dark:border-white/5 dark:bg-emerald-900/10 dark:hover:border-emerald-700"
                    >
                        <div class="flex items-start gap-2">
                            <CheckCircle
                                class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500"
                            />
                            <div>
                                <p
                                    class="text-sm text-gray-800 dark:text-gray-200"
                                >
                                    {{ fact.text }}
                                </p>
                                <div
                                    class="mt-1.5 flex items-center gap-2 opacity-60 transition-opacity group-hover:opacity-100"
                                >
                                    <span
                                        class="text-[10px] font-bold tracking-wider text-emerald-700 uppercase dark:text-emerald-400"
                                    >
                                        {{ fact.source }}
                                    </span>
                                    <span class="text-[10px] text-gray-500">
                                        {{ Math.round(fact.confidence * 100) }}%
                                        Confidence
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.5);
}
</style>
