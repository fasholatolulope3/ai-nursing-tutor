<template>
  <Head title="Clinical Context - AI Nursing Tutor" />
  
  <div class="min-h-screen bg-black font-sans selection:bg-emerald-500/30 overflow-hidden">
    <!-- Background Patterns -->
    <div class="fixed inset-0 z-0 opacity-20 pointer-events-none">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-950/80 to-slate-950"></div>
    </div>
    
    <!-- Ambient Glows (New) -->
    <div class="fixed top-1/2 left-0 -translate-y-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-emerald-900/20 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-0 translate-y-1/3 translate-x-1/3 w-[600px] h-[600px] bg-teal-900/20 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <!-- Navigation -->
    <Navbar />

    <!-- Hero Section -->
    <SyntheticHero 
      title="Master Clinical Reasoning, Not Just the Textbook."
      description="Your 24/7 Agentic Nursing Mentor. Bridge the gap between clinical theory and digital literacy with real-time, multimodal feedback."
      badgeLabel="Powered by"
      badgeText="Gemini 3"
      :ctaButtons="[
        { text: 'Start Learning (Free)', href: '/register', primary: true },
        { text: 'Watch Demo', href: '/dashboard' }
      ]"
      :microDetails="[
        'Deep Reasoning', 
        'Multimodal Vision', 
        '1M Token Support'
      ]"
    />

    <!-- The Agentic Advantage Section -->
    <section class="relative z-10 bg-black py-24">
      <div class="mx-auto max-w-7xl px-6">
        <div class="mb-16 text-center"
             v-motion-slide-visible-once-bottom
        >
          <h2 class="text-3xl font-light tracking-tight text-white sm:text-4xl">The Agentic Advantage</h2>
          <p class="mt-4 text-lg text-slate-400 font-light">Why studying with Gemini 3 changes the game.</p>
        </div>

        <div class="grid gap-8 md:grid-cols-3">
            <!-- Feature 1 -->
            <div 
                class="group relative rounded-2xl border border-white/5 bg-white/[0.02] p-8 shadow-sm transition-all hover:-translate-y-1 hover:bg-white/[0.04] hover:border-emerald-500/30 hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] backdrop-blur-md"
                 v-motion-slide-visible-once-bottom
            >
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-900/10 text-emerald-900 group-hover:bg-emerald-900 group-hover:text-white transition-colors">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M16 15.5v.01"/><path d="M12 12v.01"/><path d="M11 17a2 2 0 0 1 2 2"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white">Deep Reasoning</h3>
                <p class="mt-2 text-slate-400">Uses Chain-of-Thought processing to explain *why* an intervention is needed, not just *what* to do.</p>
            </div>

            <!-- Feature 2 -->
             <div 
                class="group relative rounded-2xl border border-white/5 bg-white/[0.02] p-8 shadow-sm transition-all hover:-translate-y-1 hover:bg-white/[0.04] hover:border-emerald-500/30 hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] backdrop-blur-md"
                 v-motion-slide-visible-once-bottom
                 :delay="100"
            >
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#0d9488]/10 text-[#0d9488] group-hover:bg-[#0d9488] group-hover:text-white transition-colors">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white">Multimodal Vision</h3>
                <p class="mt-2 text-slate-400">Upload EKGs, wound images, or lab reports. The AI analyzes visual data just like a senior nurse.</p>
            </div>

            <!-- Feature 3 -->
             <div 
                class="group relative rounded-2xl border border-white/5 bg-white/[0.02] p-8 shadow-sm transition-all hover:-translate-y-1 hover:bg-white/[0.04] hover:border-emerald-500/30 hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] backdrop-blur-md"
                 v-motion-slide-visible-once-bottom
                 :delay="200"
            >
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72Z"/><path d="m14 7 3 3"/><path d="M5 6v4"/><path d="M19 14v4"/><path d="M10 2v2"/><path d="M7 8H3"/><path d="M21 16h-4"/><path d="M11 3H9"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white">1M Token Library</h3>
                <p class="mt-2 text-slate-400">Instantly cross-references your specific patient case against 1,000+ textbooks and guidelines.</p>
            </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Footer -->
    <section class="bg-gradient-to-t from-emerald-950 to-black py-20 text-center text-white border-t border-white/5">
        <div class="mx-auto max-w-4xl px-6">
            <h2 class="text-3xl font-light tracking-tight md:text-5xl">Ready to elevate your practice?</h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-emerald-100/80 font-light">Join thousands of nursing students and professionals using Agentic AI to master clinical judgment.</p>
            <Link 
                href="/register" 
                class="inline-block mt-10 rounded-full bg-white px-8 py-4 text-lg font-bold text-emerald-900 hover:bg-emerald-50 transition-colors"
            >
              Get Started for Free
            </Link>
        </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SyntheticHero from '../components/SyntheticHero.vue';
import Navbar from '../components/Navbar.vue';
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}
</style>
