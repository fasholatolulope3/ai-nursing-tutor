<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import * as THREE from 'three';
import gsap from 'gsap';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

// --- Props ---
interface HeroProps {
    title?: string;
    description?: string;
    badgeText?: string;
    badgeLabel?: string;
    ctaButtons?: Array<{ text: string; href?: string; primary?: boolean }>;
    microDetails?: Array<string>;
}

const props = withDefaults(defineProps<HeroProps>(), {
    title: 'An experiment in light, motion, and the quiet chaos between.',
    description:
        'Experience a new dimension of interaction — fluid, tactile, and alive. Designed for creators who see beauty in motion.',
    badgeText: 'Gemini 3',
    badgeLabel: 'Powered By',
    ctaButtons: () => [
        { text: 'Start Learning (Free)', href: '#', primary: true },
        { text: 'Watch Demo', href: '#' },
    ],
    microDetails: () => [
        'Deep Reasoning',
        'Multimodal Vision',
        '1M Token Support',
    ],
});

// --- Refs for UI Elements ---
const sectionRef = ref<HTMLElement | null>(null);
const canvasContainerRef = ref<HTMLElement | null>(null);
const badgeWrapperRef = ref<HTMLElement | null>(null);
const headingRef = ref<HTMLElement | null>(null);
const paragraphRef = ref<HTMLElement | null>(null);
const ctaRef = ref<HTMLElement | null>(null);
const microRef = ref<HTMLElement | null>(null);

// --- Three.js State ---
let renderer: THREE.WebGLRenderer | null = null;
let scene: THREE.Scene | null = null;
let camera: THREE.OrthographicCamera | null = null;
let material: THREE.ShaderMaterial | null = null;
let animationFrameId: number;
const clock = new THREE.Clock();

// --- Shaders ---
const vertexShader = `
  varying vec2 vUv;
  void main() {
    vUv = uv;
    gl_Position = vec4(position, 1.0);
  }
`;

const fragmentShader = `
  precision highp float;

  varying vec2 vUv;
  uniform float u_time;
  uniform vec3 u_resolution;
  // uniform sampler2D u_channel0; // Unused in original React code provided

  vec2 toPolar(vec2 p) {
      float r = length(p);
      float a = atan(p.y, p.x);
      return vec2(r, a);
  }

  // Unused but kept for completeness
  vec2 fromPolar(vec2 polar) {
      return vec2(cos(polar.y), sin(polar.y)) * polar.x;
  }

  void mainImage(out vec4 fragColor, in vec2 fragCoord) {
      // Normalize coords - center (0,0)
      vec2 p = 6.0 * ((fragCoord.xy - 0.5 * u_resolution.xy) / u_resolution.y);

      vec2 polar = toPolar(p);
      float r = polar.x;
      // float a = polar.y; // Unused

      vec2 i = p;
      float c = 0.0;
      float rot = r + u_time + p.x * 0.100;
      
      for (float n = 0.0; n < 4.0; n++) {
          float rr = r + 0.15 * sin(u_time*0.7 + float(n) + r*2.0);
          
          // Rotation matrix
          p *= mat2(
              cos(rot - sin(u_time / 10.0)), sin(rot),
              -sin(cos(rot) - u_time / 10.0), cos(rot)
          ) * -0.25;

          float t = r - u_time / (n + 30.0);
          i -= p + sin(t - i.y) + rr;

          c += 2.2 / length(vec2(
              (sin(i.x + t) / 0.15),
              (cos(i.y + t) / 0.15)
          ));
      }

      c /= 8.0;

      // Color Palette: Clinical Blue/Teal/Green
      // React code was: vec3(0.2, 0.7, 0.5) (Emeraldish)
      // Adapting slightly to Nursing Tutor Brand (Blue/Teal) if needed, 
      // but keeping original emerald for now or tweaking to 0.1, 0.4, 0.6 for Blue?
      // Let's stick to the React Code's emerald for now as requested, 
      // maybe slight shift to blue: vec3(0.2, 0.6, 0.8)
      
      vec3 baseColor = vec3(0.2, 0.7, 0.5); // Original Emerald
      // vec3 baseColor = vec3(0.1, 0.5, 0.8); // More Blue/Teal

      vec3 finalColor = baseColor * smoothstep(0.0, 1.0, c * 0.6);

      fragColor = vec4(finalColor, 1.0);
  }

  void main() {
      vec4 fragColor;
      // vUv is 0..1, resolution is px. fragCoord needs to be in pixels
      vec2 fragCoord = vUv * u_resolution.xy;
      mainImage(fragColor, fragCoord);
      gl_FragColor = fragColor;
  }
`;

// --- Lifecycle ---
onMounted(() => {
    initThree();
    initGSAP();
    window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    if (animationFrameId) cancelAnimationFrame(animationFrameId);
    if (renderer) {
        renderer.dispose();
        renderer.domElement.remove();
    }
    window.removeEventListener('resize', handleResize);
});

// --- Methods ---
const initThree = () => {
    if (!canvasContainerRef.value) return;

    const width = canvasContainerRef.value.clientWidth;
    const height = canvasContainerRef.value.clientHeight;

    // Scene
    scene = new THREE.Scene();

    // Camera - doesn't matter much for full screen quad, but needed for render
    camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

    // Geometry - Full screen quad
    const geometry = new THREE.PlaneGeometry(2, 2);

    // Material
    material = new THREE.ShaderMaterial({
        vertexShader,
        fragmentShader,
        uniforms: {
            u_time: { value: 0 },
            u_resolution: { value: new THREE.Vector3(width, height, 1) },
        },
        depthTest: false,
        depthWrite: false,
    });

    const mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);

    // Renderer
    renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    canvasContainerRef.value.appendChild(renderer.domElement);

    animate();
};

const animate = () => {
    if (!renderer || !scene || !camera || !material) return;

    material.uniforms.u_time.value = clock.getElapsedTime() * 0.5;

    renderer.render(scene, camera);
    animationFrameId = requestAnimationFrame(animate);
};

const handleResize = () => {
    if (!canvasContainerRef.value || !renderer || !material) return;

    const width = canvasContainerRef.value.clientWidth;
    const height = canvasContainerRef.value.clientHeight;

    renderer.setSize(width, height);
    material.uniforms.u_resolution.value.set(width, height, 1);
};

const initGSAP = () => {
    // Ensure DOM is ready
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    // 1. Badge Reveal
    if (badgeWrapperRef.value) {
        // Set initial
        gsap.set(badgeWrapperRef.value, { autoAlpha: 0, y: -8 });
        // Animate
        tl.to(badgeWrapperRef.value, { autoAlpha: 1, y: 0, duration: 0.5 }, 0);
    }

    // 2. Heading Reveal (Staggered words/lines simulation without SplitText plugin)
    // We'll just fade up the whole heading or use a simple split if implemented manually.
    // For simplicity and avoiding paid plugins, we'll fade up the heading container slightly slower.
    if (headingRef.value) {
        gsap.set(headingRef.value, {
            autoAlpha: 0,
            y: 24,
            filter: 'blur(16px)',
            scale: 1.04,
        });
        tl.to(
            headingRef.value,
            {
                autoAlpha: 1,
                y: 0,
                filter: 'blur(0px)',
                scale: 1,
                duration: 0.9,
            },
            0.1,
        );
    }

    // 3. Paragraph
    if (paragraphRef.value) {
        gsap.set(paragraphRef.value, { autoAlpha: 0, y: 8 });
        tl.to(
            paragraphRef.value,
            { autoAlpha: 1, y: 0, duration: 0.5 },
            '-=0.55',
        );
    }

    // 4. Buttons
    if (ctaRef.value) {
        gsap.set(ctaRef.value, { autoAlpha: 0, y: 8 });
        tl.to(ctaRef.value, { autoAlpha: 1, y: 0, duration: 0.5 }, '-=0.35');
    }

    // 5. Micro Details
    if (microRef.value) {
        // Select list items if possible, or just animate the container
        // In Vue, ref points to the UL. We can querySelector children.
        const items = microRef.value.querySelectorAll('li');
        if (items.length) {
            gsap.set(items, { autoAlpha: 0, y: 6 });
            tl.to(
                items,
                { autoAlpha: 1, y: 0, duration: 0.5, stagger: 0.1 },
                '-=0.25',
            );
        }
    }
};
</script>

<template>
    <section
        ref="sectionRef"
        class="relative flex min-h-[90vh] items-center justify-center overflow-hidden bg-black"
    >
        <!-- Canvas Background -->
        <div ref="canvasContainerRef" class="absolute inset-0 z-0"></div>

        <!-- Content -->
        <div
            class="relative z-10 mx-auto flex max-w-7xl flex-col items-center px-6 text-center"
        >
            <!-- Badge -->
            <div ref="badgeWrapperRef" class="mb-6">
                <Badge
                    class="flex items-center gap-2 border border-white/20 bg-white/10 px-4 py-1.5 font-medium tracking-wider text-emerald-300 uppercase backdrop-blur-md hover:bg-white/15"
                >
                    <span
                        class="text-[10px] font-light tracking-[0.18em] text-emerald-100/80"
                    >
                        {{ badgeLabel }}
                    </span>
                    <span class="h-1 w-1 rounded-full bg-emerald-200/60" />
                    <span
                        class="text-xs font-light tracking-tight text-emerald-200"
                    >
                        {{ badgeText }}
                    </span>
                </Badge>
            </div>

            <!-- Heading -->
            <h1
                ref="headingRef"
                class="mb-6 max-w-4xl text-5xl leading-tight font-light tracking-tight text-white md:text-7xl"
                style="will-change: transform, opacity, filter"
            >
                {{ title }}
            </h1>

            <!-- Description -->
            <p
                ref="paragraphRef"
                class="mx-auto mb-10 max-w-2xl text-lg font-light text-emerald-50/80"
            >
                {{ description }}
            </p>

            <!-- CTA Buttons -->
            <div
                ref="ctaRef"
                class="flex flex-wrap items-center justify-center gap-4"
            >
                <Button
                    v-for="(btn, idx) in ctaButtons"
                    :key="idx"
                    :as="btn.href && !btn.href.startsWith('#') ? Link : 'a'"
                    :href="btn.href"
                    :variant="btn.primary ? 'default' : 'outline'"
                    :class="
                        cn(
                            'rounded-xl px-8 py-6 text-base font-medium transition-all duration-300',
                            btn.primary
                                ? 'border-transparent bg-emerald-500/80 text-white shadow-[0_0_20px_rgba(16,185,129,0.3)] backdrop-blur-lg hover:bg-emerald-400/80'
                                : 'border-white/30 text-white backdrop-blur-lg hover:bg-white/10',
                        )
                    "
                >
                    {{ btn.text }}
                </Button>
            </div>

            <!-- Micro Details -->
            <ul
                ref="microRef"
                v-if="microDetails && microDetails.length > 0"
                class="mt-12 flex flex-wrap justify-center gap-8 text-xs font-light tracking-wide text-emerald-100/70"
            >
                <li
                    v-for="(detail, index) in microDetails"
                    :key="index"
                    class="flex items-center gap-2"
                >
                    <span
                        class="box-shadow-glow h-1.5 w-1.5 rounded-full bg-emerald-400"
                    />
                    {{ detail }}
                </li>
            </ul>
        </div>
    </section>
</template>

<style scoped>
.box-shadow-glow {
    box-shadow: 0 0 8px currentColor;
}
</style>
