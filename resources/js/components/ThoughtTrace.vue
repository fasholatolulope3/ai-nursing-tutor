<template>
  <div
    class="relative w-full max-w-lg overflow-hidden rounded-xl border border-white/10 bg-black/40 p-4 font-mono text-xs text-sky-400 shadow-2xl backdrop-blur-md"
    v-motion
    :initial="{ opacity: 0, y: 20 }"
    :enter="{ opacity: 1, y: 0, transition: { duration: 800 } }"
  >
    <!-- Header -->
    <div class="mb-3 flex items-center justify-between border-b border-white/5 pb-2">
      <div class="flex items-center gap-2">
        <div class="relative flex h-2 w-2">
          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
          <span class="relative inline-flex h-2 w-2 rounded-full bg-sky-500"></span>
        </div>
        <span class="font-bold tracking-wider text-sky-100">GEMINI 3 REASONING</span>
      </div>
      <span class="text-[10px] text-sky-500/80">LATENCY: 12ms</span>
    </div>

    <!-- Trace Logs -->
    <div class="space-y-2">
      <div
        v-for="(log, index) in steps"
        :key="index"
        class="flex items-start gap-3"
        v-motion
        :initial="{ opacity: 0, x: -10 }"
        :enter="{ opacity: 1, x: 0, transition: { delay: index * 600, duration: 400 } }"
      >
        <span class="mt-0.5 text-[10px] text-gray-500">{{ log.time }}</span>
        <div class="flex-1">
          <span :class="log.color ?? 'text-sky-300'">{{ log.text }}</span>
          <div v-if="log.sub" class="mt-1 pl-2 text-[10px] text-gray-500 border-l border-gray-700">
            {{ log.sub }}
          </div>
        </div>
      </div>
    </div>

    <!-- Processing Indicator -->
    <div class="mt-4 flex animate-pulse gap-1">
      <div class="h-1.5 w-1.5 rounded-full bg-sky-500/50"></div>
      <div class="h-1.5 w-1.5 rounded-full bg-sky-500/50 animation-delay-200"></div>
      <div class="h-1.5 w-1.5 rounded-full bg-sky-500/50 animation-delay-400"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const steps = ref([
  { time: '00:00.02', text: 'Scanning vital signs monitor...', sub: 'HR: 110, BP: 85/55, SpO2: 92%', color: 'text-amber-300' },
  { time: '00:00.15', text: 'Detected abnormality: Hypotension + Tachycardia', color: 'text-red-300' },
  { time: '00:00.42', text: 'Retrieving context: Patient history (Sepsis risk)', sub: 'Checking previous antibiotic administration...' },
  { time: '00:00.89', text: 'Formulating intervention recommendation...', color: 'text-sky-200' },
  { time: '00:01.20', text: 'Cross-referencing Interpreted Guidelines (SSC 2024)', color: 'text-purple-300' },
]);
</script>

<style scoped>
.animation-delay-200 {
  animation-delay: 200ms;
}
.animation-delay-400 {
  animation-delay: 400ms;
}
</style>
