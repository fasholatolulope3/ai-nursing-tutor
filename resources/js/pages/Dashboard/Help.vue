<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Search, Send, ChevronDown, HelpCircle, MessageCircle, FileText, Sparkles } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const searchQuery = ref('');

const faqs = [
    {
        question: 'How does the AI Nursing Tutor work?',
        answer: 'Clinical Context uses advanced Gemini models to simulate real-world patient scenarios. It analyzes your interactions, provides reasoning traces (Thought Signatures), and gives feedback based on established nursing processes.'
    },
    {
        question: 'What is a "Thought Signature"?',
        answer: 'A Thought Signature is the AIs internal reasoning process. It shows you how the AI arrived at its conclusions, allowing you to learn the underlying clinical logic.'
    },
    {
        question: 'Can I upload my own clinical documents?',
        answer: 'Yes! You can upload PDFs or images in the Clinical Query section. The AI will analyze the document and provide clinical context based on the information provided.'
    },
    {
        question: 'How do I track my progress?',
        answer: 'Your performance in simulation sessions is tracked in the "History" section. You can review past scenarios, scores, and specific AI feedback to identify areas for improvement.'
    },
    {
        question: 'Is the data secure?',
        answer: 'We prioritize privacy and security. No personally identifiable patient data should be uploaded. All interactions are processed according to HIPAA-aware guidelines.'
    }
];

const filteredFaqs = computed(() => {
    if (!searchQuery.value) return faqs;
    const query = searchQuery.value.toLowerCase();
    return faqs.filter(faq => 
        faq.question.toLowerCase().includes(query) || 
        faq.answer.toLowerCase().includes(query)
    );
});

const form = useForm({
    topic: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/api/v1/support/tickets', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Support request submitted successfully!');
            form.reset();
        },
        onError: () => {
            toast.error('Failed to submit support request. Please try again.');
        }
    });
};

const topics = [
    'Technical Issue',
    'Account Billing',
    'Feature Request',
    'Clinical Content Feedback',
    'Other'
];
</script>

<template>
    <Head title="Help & Support" />
    <DashboardLayout
        title="Help & Support"
        description="Find answers to common questions or reach out to our team."
    >
        <div class="max-w-6xl mx-auto space-y-8 pb-12">
            <!-- FAQ Section -->
            <section class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-2xl font-semibold tracking-tight">Frequently Asked Questions</h2>
                        <p class="text-sm text-muted-foreground">Quick answers to common inquiries.</p>
                    </div>
                    <div class="relative w-full md:w-72">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search FAQ..."
                            class="pl-9 bg-background/50 backdrop-blur-sm border-white/10"
                        />
                    </div>
                </div>

                <div class="grid gap-4">
                    <Card v-for="(faq, index) in filteredFaqs" :key="index" class="bg-background/20 backdrop-blur-md border-white/5 overflow-hidden">
                        <Collapsible>
                            <CollapsibleTrigger class="flex items-center justify-between w-full p-4 hover:bg-white/5 transition-colors text-left">
                                <span class="font-medium text-slate-200">{{ faq.question }}</span>
                                <ChevronDown class="h-4 w-4 text-muted-foreground transition-transform duration-200" />
                            </CollapsibleTrigger>
                            <CollapsibleContent class="p-4 pt-0 text-sm text-slate-400 leading-relaxed">
                                {{ faq.answer }}
                            </CollapsibleContent>
                        </Collapsible>
                    </Card>
                    <div v-if="filteredFaqs.length === 0" class="text-center py-10 text-muted-foreground">
                        No results found for "{{ searchQuery }}".
                    </div>
                </div>
            </section>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Contact Form -->
                <Card class="md:col-span-2 bg-background/20 backdrop-blur-xl border-white/10 shadow-2xl">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageCircle class="h-5 w-5 text-indigo-400" />
                            Contact Support
                        </CardTitle>
                        <CardDescription>
                            Cant find what you're looking for? Send us a message and we'll get back to you.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="topic">Topic</Label>
                                    <select
                                        id="topic"
                                        v-model="form.topic"
                                        class="w-full px-3 py-2 bg-background/50 border border-white/10 rounded-md focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all outline-none text-sm text-slate-200"
                                        required
                                    >
                                        <option value="" disabled>Select a topic</option>
                                        <option v-for="t in topics" :key="t" :value="t">{{ t }}</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <Label for="subject">Subject</Label>
                                    <Input
                                        id="subject"
                                        v-model="form.subject"
                                        placeholder="e.g., Cannot load scenarios"
                                        class="bg-background/50 border-white/10"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="message">Message</Label>
                                <Textarea
                                    id="message"
                                    v-model="form.message"
                                    placeholder="Please describe your issue in detail..."
                                    class="min-h-[150px] bg-background/50 border-white/10 resize-none"
                                    required
                                />
                            </div>
                            <Button
                                type="submit"
                                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white shadow-lg shadow-indigo-500/20"
                                :disabled="form.processing"
                            >
                                <Send v-if="!form.processing" class="mr-2 h-4 w-4" />
                                <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                                {{ form.processing ? 'Sending...' : 'Send Message' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <Card class="bg-indigo-500/10 border-indigo-500/20 backdrop-blur-sm">
                        <CardHeader>
                            <CardTitle class="text-lg flex items-center gap-2">
                                <HelpCircle class="h-5 w-5 text-indigo-400" />
                                Resources
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="grid gap-4">
                            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 transition-colors group">
                                <div class="h-8 w-8 rounded bg-white/10 flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors">
                                    <FileText class="h-4 w-4 text-indigo-400" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium">Platform Guide</p>
                                    <p class="text-xs text-muted-foreground">Comprehensive documentation</p>
                                </div>
                            </a>
                            <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 transition-colors group">
                                <div class="h-8 w-8 rounded bg-white/10 flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors">
                                    <MessageCircle class="h-4 w-4 text-indigo-400" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium">Community Forum</p>
                                    <p class="text-xs text-muted-foreground">Connect with other learners</p>
                                </div>
                            </a>
                        </CardContent>
                    </Card>

                    <Card class="bg-background/20 border-white/5">
                        <CardContent class="p-6 text-center space-y-4">
                            <div class="h-16 w-16 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-full mx-auto flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <Send class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-1">
                                <p class="font-medium">Direct Email</p>
                                <p class="text-sm text-muted-foreground">support@clinicalcontext.ai</p>
                            </div>
                            <p class="text-xs text-muted-foreground mt-4">
                                Average response time: <br/> 
                                <span class="text-indigo-400 font-medium">Under 2 hours</span>
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.collapsible-content-enter-active,
.collapsible-content-leave-active {
  transition: all 0.2s ease-out;
}
.collapsible-content-enter-from,
.collapsible-content-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
