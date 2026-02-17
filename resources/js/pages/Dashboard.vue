<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import ChatInterface from '@/components/dashboard/ChatInterface.vue';
import ClinicalLabPanel from '@/components/dashboard/ClinicalLabPanel.vue';
import { useAdaptiveStudy } from '@/composables/useAdaptiveStudy';
import { ref, onMounted, computed } from 'vue';
import { FileSearch } from 'lucide-vue-next';

// --- State Management ---
const isThinking = ref(false);
const isLabOpen = ref(false);
const activeDocument = ref<any>(null); // Type: ActiveDocument
const messages = ref<any[]>([
    {
        id: '1',
        role: 'assistant',
        content:
            'Hello, nurse. I am ready to assist with your clinical reasoning. Please describe the patient case or upload a chart.',
        timestamp: new Date(),
    },
]);

const thoughtTrace = ref<any[]>([]);

// --- Actions ---
const previousThoughtSignature = ref<string | null>(null);
const { setTopics, clearTopics } = useAdaptiveStudy();

const restoreInteraction = (interaction: any) => {
    const aiData = interaction.ai_response;
    // 1. Restore User Prompt
    messages.value.push({
        id: 'restored-user',
        role: 'user',
        content: interaction.prompt,
        timestamp: new Date(interaction.created_at), // Approx
    });

    // 2. Restore AI Response
    messages.value.push({
        id: 'restored-ai',
        role: 'assistant',
        content: aiData.answer,
        timestamp: new Date(interaction.created_at),
    });

    // 3. Restore Context
    if (aiData.new_signature) {
        previousThoughtSignature.value = aiData.new_signature;
    }

    // 4. Restore Thought Trace
    if (aiData.reasoning_trace) {
        thoughtTrace.value = aiData.reasoning_trace.map(
            (step: any, index: number) => ({
                id: `restored-t-${index}`,
                description: step.content,
                status: step.status || 'completed',
                details: step.details,
            }),
        );
    }

    // 5. Restore Extracted Data (Active Document Placeholder)
    if (
        aiData.extracted_data &&
        Object.keys(aiData.extracted_data).length > 0
    ) {
        const facts = Object.entries(aiData.extracted_data).map(
            ([key, value], index) => ({
                id: `restored-f-${index}`,
                text: `${key.toUpperCase()}: ${value}`,
                confidence: 0.95,
                source: 'Restored Context',
            }),
        );

        activeDocument.value = {
            id: 'restored-doc',
            name: 'Restored Clinical Context',
            type: 'pdf', // Generic fallback
            url: null,
            facts: facts,
        };
    }

    // 6. Restore Topics
    if (aiData.related_topics) {
        setTopics(aiData.related_topics);
    }
};

const startNewChat = () => {
    messages.value = [
        {
            id: '1',
            role: 'assistant',
            content:
                'Hello, nurse. I am ready to assist with your clinical reasoning. Please describe the patient case or upload a chart.',
            timestamp: new Date(),
        },
    ];
    thoughtTrace.value = [];
    previousThoughtSignature.value = null;
    activeDocument.value = null;
    clearTopics();

    // Clear URL param without reload
    router.get('/dashboard', {}, { preserveState: true, replace: true });
};

onMounted(async () => {
    try {
        const apiClient = (await import('@/lib/axios')).default;

        // Check for interaction ID in URL
        const urlParams = new URLSearchParams(window.location.search);
        const interactionId = urlParams.get('interaction');

        let response;
        if (interactionId) {
            response = await apiClient.get(
                `/simulations/clinical-query/${interactionId}`,
            );
        } else {
            response = await apiClient.get('/simulations/clinical-query/last');
        }

        if (response.data) {
            restoreInteraction(response.data);
        }
    } catch (error) {
        console.error('Failed to restore session:', error);
    }
});

const handleSendMessage = async (content: string, files: File[]) => {
    // 1. Add User Message
    const userMsgId = Date.now().toString();
    messages.value.push({
        id: userMsgId,
        role: 'user',
        content: content,
        timestamp: new Date(),
        attachments: files,
    });

    // 2. Set Thinking State
    isThinking.value = true;
    thoughtTrace.value = [];

    try {
        // 3. Prepare API Request
        const formData = new FormData();
        formData.append('message', content);
        if (files.length > 0) {
            formData.append('attachment', files[0]); // Handle first file for now
        }
        if (previousThoughtSignature.value) {
            formData.append(
                'previous_thought_signature',
                previousThoughtSignature.value,
            );
        }

        // 4. Call Backend API
        const apiClient = (await import('@/lib/axios')).default;
        const response = await apiClient.post(
            '/simulations/clinical-query',
            formData,
            {
                headers: { 'Content-Type': 'multipart/form-data' },
            },
        );

        const data = response.data;

        // 5. Update State with Response
        messages.value.push({
            id: Date.now().toString(),
            role: 'assistant',
            content: data.answer,
            timestamp: new Date(),
        });

        // Update Thought Trace
        if (data.reasoning_trace) {
            thoughtTrace.value = data.reasoning_trace.map(
                (step: any, index: number) => ({
                    id: `t-${index}`,
                    description: step.content,
                    status: step.status || 'completed',
                    details: step.details,
                }),
            );
        }

        // Update Clinical Lab (Simple Mapping)
        if (
            data.extracted_data &&
            Object.keys(data.extracted_data).length > 0
        ) {
            // Check if it's vitals or generic data
            const facts = Object.entries(data.extracted_data).map(
                ([key, value], index) => ({
                    id: `f-${index}`,
                    text: `${key.toUpperCase()}: ${value}`,
                    confidence: 0.95,
                    source: 'AI Extraction',
                }),
            );

            activeDocument.value = {
                id: 'doc-' + Date.now(),
                name: files.length > 0 ? files[0].name : 'Clinical Analysis',
                type:
                    files.length > 0 && files[0].type.includes('image')
                        ? 'image'
                        : 'pdf',
                url: files.length > 0 ? URL.createObjectURL(files[0]) : null, // Temp URL for preview
                facts: facts,
            };
        }

        // Save Signature
        if (data.new_signature) {
            previousThoughtSignature.value = data.new_signature;
        }

        // Adaptive Recommendations
        if (data.related_topics) {
            setTopics(data.related_topics);
        }
    } catch (error: any) {
        console.error('Clinical Query Failed:', error);

        let errorMsg =
            "I'm having trouble connecting to the clinical reasoning engine. Please try again.";
        if (error.response?.status === 429) {
            errorMsg = 'Usage limit reached. Please wait a moment.';
        } else if (error.response?.data?.error) {
            errorMsg = error.response.data.error;
        }

        messages.value.push({
            id: Date.now().toString(),
            role: 'assistant',
            content: errorMsg,
            timestamp: new Date(),
        });
    } finally {
        isThinking.value = false;
    }
};

// Mock Document Selection (would come from sidebar or upload)
const handleDocumentSelect = (doc: any) => {
    activeDocument.value = doc;
    // In real app, we might also set the document as context for the next query
};
</script>

<template>
    <Head title="Clinical Dashboard" />

    <DashboardLayout
        title="Clinical Workspace"
        description="Real-time Agentic Reasoning"
    >
        <template #actions>
            <div class="flex items-center gap-3">
                <button
                    @click="isLabOpen = !isLabOpen"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md focus:ring-2 focus:ring-emerald-500 xl:hidden dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                >
                    <FileSearch class="h-4 w-4 text-emerald-500" />
                    <span>Clinical Lab</span>
                </button>
                <button
                    @click="startNewChat"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-500 hover:shadow-md focus:ring-2 focus:ring-emerald-500 active:scale-95"
                >
                    <Send class="h-4 w-4" />
                    <span>New Chat</span>
                </button>
            </div>
        </template>

        <!-- Main Layout: 3 Panels (Sidebar handled by Layout) -->
        <div
            class="-m-4 mt-0 flex h-[calc(100vh-8rem)] border-t border-gray-200 sm:-m-6 dark:border-white/10"
        >
            <!-- Center Stage: Chat & Reasoning -->
            <div class="relative min-w-0 flex-1 bg-white dark:bg-black">
                <ChatInterface
                    :messages="messages"
                    :is-thinking="isThinking"
                    :thought-trace="thoughtTrace"
                    @send-message="handleSendMessage"
                />
            </div>

            <!-- Right Panel: Clinical Lab -->
            <ClinicalLabPanel
                :document="activeDocument"
                :is-loading="false"
                :is-open="isLabOpen"
                @close="isLabOpen = false"
            />
        </div>
    </DashboardLayout>
</template>
