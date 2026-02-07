<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import { Send, Paperclip, Mic, Image as ImageIcon, X } from 'lucide-vue-next';
import NursingThoughtTrace from '@/components/NursingThoughtTrace.vue';

interface Message {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    timestamp: Date;
    attachments?: File[];
}

const props = defineProps<{
    messages: Message[];
    isThinking: boolean;
    thoughtTrace: any[]; // Using specific type in real impl
}>();

const emit = defineEmits<{
    (e: 'sendMessage', content: string, files: File[]): void;
}>();

const inputContent = ref('');
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFiles = ref<File[]>([]);
const chatContainer = ref<HTMLElement | null>(null);

const triggerFileUpload = () => {
    fileInput.value?.click();
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        selectedFiles.value = [
            ...selectedFiles.value,
            ...Array.from(target.files),
        ];
    }
    // Reset input so same file can be selected again
    if (target.value) target.value = '';
};

const removeFile = (index: number) => {
    selectedFiles.value.splice(index, 1);
};

const sendMessage = () => {
    if (
        (!inputContent.value.trim() && selectedFiles.value.length === 0) ||
        props.isThinking
    )
        return;

    emit('sendMessage', inputContent.value, selectedFiles.value);

    inputContent.value = '';
    selectedFiles.value = [];
};

// Auto-scroll to bottom
watch(
    () => props.messages.length,
    () => {
        nextTick(() => {
            if (chatContainer.value) {
                chatContainer.value.scrollTop =
                    chatContainer.value.scrollHeight;
            }
        });
    },
);
</script>

<template>
    <div
        class="relative flex h-full flex-col overflow-hidden bg-gray-50 dark:bg-black/50"
    >
        <!-- Chat Area -->
        <div
            ref="chatContainer"
            class="flex-1 space-y-6 overflow-y-auto scroll-smooth p-4"
        >
            <!-- Thought Trace (Sticky or inline?) - Placing it at the bottom of the last assistant message or top of current turn -->
            <NursingThoughtTrace
                :is-thinking="isThinking"
                :trace="thoughtTrace"
                class="mb-4"
            />

            <div
                v-if="messages.length === 0"
                class="flex h-full flex-col items-center justify-center text-center text-gray-500 opacity-60"
            >
                <div
                    class="mb-4 flex h-16 w-16 rotate-3 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/20"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        class="h-8 w-8 text-emerald-600 dark:text-emerald-400"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                        />
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-900 dark:text-white">
                    Start a clinical case
                </p>
                <p class="max-w-xs text-sm">
                    Describe the patient's symptoms or upload a chart to begin
                    the simulation.
                </p>
            </div>

            <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex w-full"
                :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
            >
                <div
                    class="max-w-[85%] rounded-2xl p-4 shadow-sm lg:max-w-[75%]"
                    :class="
                        msg.role === 'user'
                            ? 'rounded-br-none bg-emerald-600 text-white'
                            : 'rounded-bl-none border border-gray-100 bg-white text-gray-800 dark:border-white/5 dark:bg-white/10 dark:text-gray-100'
                    "
                >
                    <!-- Attachments -->
                    <div
                        v-if="msg.attachments?.length"
                        class="mb-3 flex flex-wrap gap-2"
                    >
                        <div
                            v-for="(file, idx) in msg.attachments"
                            :key="idx"
                            class="flex items-center gap-2 rounded bg-black/10 px-2 py-1 text-xs dark:bg-white/10"
                        >
                            <paperclip class="h-3 w-3" />
                            <span class="max-w-[150px] truncate">{{
                                file.name
                            }}</span>
                        </div>
                    </div>

                    <div class="leading-relaxed whitespace-pre-wrap">
                        {{ msg.content }}
                    </div>

                    <div class="mt-2 text-right text-[10px] opacity-70">
                        {{
                            msg.timestamp.toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                            })
                        }}
                    </div>
                </div>
            </div>

            <!-- Thinking Indicator (Alternative placement if thought trace is hidden/collapsed) -->
            <div
                v-if="isThinking && thoughtTrace.length === 0"
                class="ml-2 flex items-center gap-2 text-sm text-gray-400"
            >
                <div class="flex space-x-1">
                    <div
                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.3s]"
                    ></div>
                    <div
                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.15s]"
                    ></div>
                    <div
                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400"
                    ></div>
                </div>
                <span>Agent is thinking...</span>
            </div>
        </div>

        <!-- Input Area -->
        <div
            class="border-t border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-black"
        >
            <!-- Selected Files Preview -->
            <div
                v-if="selectedFiles.length > 0"
                class="mb-3 flex flex-wrap gap-2 px-2"
            >
                <div
                    v-for="(file, index) in selectedFiles"
                    :key="index"
                    class="group relative flex items-center gap-2 rounded-lg border border-emerald-100 bg-emerald-50 py-1.5 pr-8 pl-2 text-sm transition-all dark:border-emerald-500/30 dark:bg-emerald-900/20"
                >
                    <span
                        class="max-w-[200px] truncate text-emerald-700 dark:text-emerald-300"
                        >{{ file.name }}</span
                    >
                    <button
                        @click="removeFile(index)"
                        class="absolute top-1/2 right-1 -translate-y-1/2 rounded-full p-1 text-emerald-400 transition-colors hover:bg-emerald-100 hover:text-emerald-600 dark:hover:bg-emerald-500/20 dark:hover:text-emerald-200"
                    >
                        <X class="h-3 w-3" />
                    </button>
                </div>
            </div>

            <div
                class="relative flex items-end gap-2 rounded-xl border border-gray-200 bg-gray-50 p-2 transition-all focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5"
            >
                <!-- File Upload -->
                <button
                    @click="triggerFileUpload"
                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10 dark:hover:text-gray-200"
                    title="Upload Clinical Documents"
                >
                    <Paperclip class="h-5 w-5" />
                    <input
                        ref="fileInput"
                        type="file"
                        multiple
                        class="hidden"
                        accept=".pdf,.jpg,.jpeg,.png"
                        @change="handleFileSelect"
                    />
                </button>

                <button
                    class="block rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 md:hidden dark:hover:bg-white/10 dark:hover:text-gray-200"
                >
                    <ImageIcon class="h-5 w-5" />
                </button>

                <!-- Text Input -->
                <textarea
                    v-model="inputContent"
                    rows="1"
                    class="max-h-32 flex-1 resize-none border-none bg-transparent px-2 py-2.5 text-sm leading-relaxed text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-white"
                    placeholder="Describe symptoms, vital signs, or ask about the patient..."
                    @keydown.enter.prevent="sendMessage"
                ></textarea>

                <!-- Voice Input (Mock) -->
                <button
                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10 dark:hover:text-gray-200"
                >
                    <Mic class="h-5 w-5" />
                </button>

                <!-- Send Button -->
                <button
                    @click="sendMessage"
                    :disabled="
                        (!inputContent.trim() && selectedFiles.length === 0) ||
                        isThinking
                    "
                    class="rounded-lg bg-emerald-600 p-2 text-white shadow-sm transition-all hover:scale-105 hover:bg-emerald-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Send class="h-5 w-5" />
                </button>
            </div>
            <p
                class="mt-2 text-center text-[10px] text-gray-400 dark:text-gray-600"
            >
                Gemini 3 can make mistakes. Verify clinical outputs with
                standard guidelines.
            </p>
        </div>
    </div>
</template>
