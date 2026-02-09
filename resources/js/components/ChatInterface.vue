<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import { marked } from 'marked';
import { simulationService, type ConversationTurn } from '@/services/simulation';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ChevronDown, Brain, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
    sessionId: number;
    initialTurns: ConversationTurn[];
}>();

const turns = ref<ConversationTurn[]>(props.initialTurns);
const newMessage = ref('');
const sending = ref(false);
const errorMessage = ref<string | null>(null);
const messagesContainer = ref<HTMLElement | null>(null);

const renderMarkdown = (content: string) => {
    return marked.parse(content, { breaks: true });
};

const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || sending.value) return;

    const content = newMessage.value;
    newMessage.value = '';
    sending.value = true;

    // Optimistic update
    const tempId = Date.now();
    turns.value.push({
        id: tempId,
        role: 'user',
        content: content,
        created_at: new Date().toISOString()
    });
    scrollToBottom();

    try {
        const response = await simulationService.sendChat(props.sessionId, content);
        // Replace temp turn with real one and add AI response
        const index = turns.value.findIndex((t: ConversationTurn) => t.id === tempId);
        if (index !== -1) {
            turns.value[index] = response.user_turn;
        }
        
        const aiTurn = response.ai_turn;
        if (response.reasoning_trace) {
            aiTurn.reasoning_trace = response.reasoning_trace;
        }
        turns.value.push(aiTurn);
    } catch (error: any) {
        console.error("Failed to send message", error);
        // Remove optimistic turn on error
        turns.value = turns.value.filter((t: ConversationTurn) => t.id !== tempId);
        
        if (error.response?.status === 429) {
            errorMessage.value = "Usage limit reached. Please wait a moment.";
        } else {
            errorMessage.value = "Failed to transmit message. Please try again.";
        }
        
        setTimeout(() => errorMessage.value = null, 5000);
    } finally {
        sending.value = false;
        scrollToBottom();
    }
};

const expandedTraces = ref<Record<number, boolean>>({});
const toggleTrace = (id: number) => {
    expandedTraces.value[id] = !expandedTraces.value[id];
};

</script>

<template>
    <div class="flex flex-col h-full bg-slate-950/50">
        <!-- Messages Area -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-8 scroll-smooth">
            <div 
                v-for="turn in turns" 
                :key="turn.id" 
                class="flex flex-col gap-2 max-w-[85%]"
                :class="turn.role === 'user' ? 'self-end items-end' : 'self-start items-start'"
            >
                <div class="flex items-center gap-2 px-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        {{ turn.role === 'user' ? 'Consulting Nurse' : 'Clinical System' }}
                    </span>
                </div>

                <!-- Reasoning Trace (AI Only) -->
                <div 
                    v-if="turn.role === 'ai' && turn.reasoning_trace?.length"
                    class="w-full mb-1"
                >
                    <button 
                        @click="toggleTrace(turn.id)"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/5 border border-emerald-500/10 text-[10px] font-bold text-emerald-400/80 hover:bg-emerald-500/10 transition-colors group"
                    >
                        <Brain class="w-3 h-3 group-hover:animate-pulse" />
                        CLINICAL REASONING TRACE
                        <ChevronDown :class="['w-3 h-3 transition-transform duration-300', expandedTraces[turn.id] ? 'rotate-180' : '']" />
                    </button>
                    
                    <div 
                        v-if="expandedTraces[turn.id]"
                        class="mt-2 ml-4 p-3 border-l-2 border-emerald-500/20 space-y-2 animate-in slide-in-from-left-2 duration-300"
                    >
                        <div v-for="(step, idx) in turn.reasoning_trace" :key="idx" class="flex gap-3">
                            <span class="text-[10px] font-mono text-emerald-500/50 pt-0.5">0{{ idx + 1 }}</span>
                            <span class="text-xs text-slate-400 leading-relaxed markdown-content" v-html="renderMarkdown(step.content)"></span>
                        </div>
                    </div>
                </div>

                <div 
                    class="p-4 rounded-2xl text-sm leading-relaxed shadow-sm lg:text-base markdown-content"
                    :class="[
                        turn.role === 'user' 
                            ? 'bg-emerald-600 text-white rounded-tr-none' 
                            : 'bg-white/5 text-slate-200 border border-white/10 rounded-tl-none backdrop-blur-sm'
                    ]"
                    v-html="renderMarkdown(turn.content)"
                >
                </div>
            </div>
            
            <div v-if="sending" class="self-start animate-pulse">
                 <div class="bg-white/5 border border-white/5 px-5 py-3 rounded-2xl rounded-bl-none flex gap-1.5 items-center">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-bounce"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-bounce [animation-delay:-0.3s]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-bounce [animation-delay:-0.5s]"></span>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest ml-2">Reasoning...</span>
                 </div>
            </div>
            
            <div v-if="errorMessage" class="w-full flex justify-center sticky bottom-0 pb-4 animate-in slide-in-from-bottom-2">
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 backdrop-blur-md shadow-lg">
                    <AlertCircle class="w-4 h-4" />
                    {{ errorMessage }}
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 bg-black/60 border-t border-white/10 backdrop-blur-md">
            <form @submit.prevent="sendMessage" class="flex gap-3 max-w-4xl mx-auto">
                <Input 
                    v-model="newMessage" 
                    placeholder="Describe your next assessment or intervention..." 
                    class="flex-1 h-12 bg-white/5 border-white/10 focus-visible:ring-emerald-500 text-white placeholder:text-slate-500 rounded-xl"
                    :disabled="sending"
                />
                <Button 
                    type="submit" 
                    variant="default"
                    class="h-12 px-6 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all active:scale-95"
                    :disabled="sending || !newMessage.trim()"
                >
                    {{ sending ? 'Sending...' : 'Transmit' }}
                </Button>
            </form>
            <p class="text-[10px] text-center text-slate-600 mt-3 uppercase tracking-widest">
                Clinical Simulation Mode • Real-time AI Reasoning Enabled
            </p>
        </div>
    </div>
</template>
