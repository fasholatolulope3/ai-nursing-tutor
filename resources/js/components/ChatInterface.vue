<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import { simulationService, type ConversationTurn } from '@/services/simulation';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    sessionId: number;
    initialTurns: ConversationTurn[];
}>();

const turns = ref<ConversationTurn[]>(props.initialTurns);
const newMessage = ref('');
const sending = ref(false);
const messagesContainer = ref<HTMLElement | null>(null);

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
        const index = turns.value.findIndex(t => t.id === tempId);
        if (index !== -1) {
            turns.value[index] = response.user_turn;
        }
        turns.value.push(response.ai_turn);
    } catch (error) {
        console.error("Failed to send message", error);
        // Remove optimistic turn on error
        turns.value = turns.value.filter(t => t.id !== tempId);
    } finally {
        sending.value = false;
        scrollToBottom();
    }
};
</script>

<template>
    <div class="flex flex-col h-full">
        <!-- Messages Area -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-6">
            <div 
                v-for="turn in turns" 
                :key="turn.id" 
                class="flex flex-col gap-1 max-w-[80%]"
                :class="turn.role === 'user' ? 'self-end items-end' : 'self-start items-start'"
            >
                <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">
                    {{ turn.role === 'user' ? 'You' : 'AI Tutor' }}
                </div>
                <div 
                    class="p-4 rounded-2xl text-sm leading-relaxed"
                    :class="[
                        turn.role === 'user' 
                            ? 'bg-emerald-500/20 text-emerald-100 border border-emerald-500/30 rounded-br-none' 
                            : 'bg-white/10 text-slate-200 border border-white/10 rounded-bl-none'
                    ]"
                >
                    {{ turn.content }}
                </div>
            </div>
            
            <div v-if="sending" class="self-start">
                 <div class="bg-white/5 border border-white/5 px-4 py-3 rounded-2xl rounded-bl-none flex gap-2 items-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce delay-100"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce delay-200"></span>
                 </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-black/40 border-t border-white/10">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <Input 
                    v-model="newMessage" 
                    placeholder="Type your assessment or action..." 
                    class="bg-white/5 border-white/10 focus-visible:ring-emerald-500 text-white placeholder:text-slate-500"
                    :disabled="sending"
                />
                <Button 
                    type="submit" 
                    variant="default"
                    :disabled="sending || !newMessage.trim()"
                >
                    Send
                </Button>
            </form>
        </div>
    </div>
</template>
