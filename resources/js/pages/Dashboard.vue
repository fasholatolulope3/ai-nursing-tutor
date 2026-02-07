<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import ChatInterface from '@/components/dashboard/ChatInterface.vue';
import ClinicalLabPanel from '@/components/dashboard/ClinicalLabPanel.vue';
import { ref } from 'vue';

// --- State Management ---
const isThinking = ref(false);
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
// --- Actions ---
const previousThoughtSignature = ref<string | null>(null);

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
            formData.append('previous_thought_signature', previousThoughtSignature.value);
        }

        // 4. Call Backend API
        const apiClient = (await import('@/lib/axios')).default;
        const response = await apiClient.post('/simulations/clinical-query', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

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
            thoughtTrace.value = data.reasoning_trace.map((step: any, index: number) => ({
                id: `t-${index}`,
                description: step.content,
                status: step.status || 'completed',
                details: step.details,
            }));
        }

        // Update Clinical Lab (Simple Mapping)
        if (data.extracted_data && Object.keys(data.extracted_data).length > 0) {
            // Check if it's vitals or generic data
            const facts = Object.entries(data.extracted_data).map(([key, value], index) => ({
                id: `f-${index}`,
                text: `${key.toUpperCase()}: ${value}`,
                confidence: 0.95,
                source: 'AI Extraction'
            }));

            activeDocument.value = {
                id: 'doc-' + Date.now(),
                name: files.length > 0 ? files[0].name : 'Clinical Analysis',
                type: files.length > 0 && files[0].type.includes('image') ? 'image' : 'pdf',
                url: files.length > 0 ? URL.createObjectURL(files[0]) : null, // Temp URL for preview
                facts: facts
            };
        }

        // Save Signature
        if (data.new_signature) {
            previousThoughtSignature.value = data.new_signature;
        }

    } catch (error) {
        console.error('Clinical Query Failed:', error);
        messages.value.push({
            id: Date.now().toString(),
            role: 'assistant',
            content: "I'm having trouble connecting to the clinical reasoning engine. Please try again.",
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
        <!-- Main Layout: 3 Panels (Sidebar handled by Layout) -->
        <div
            class="-m-6 mt-0 flex h-[calc(100vh-8rem)] border-t border-gray-200 dark:border-white/10"
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
            <ClinicalLabPanel :document="activeDocument" :is-loading="false" />
        </div>
    </DashboardLayout>
</template>
