<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
    centered: { type: Boolean, default: true }
});

const isVisible = ref(false);
const sectionRef = ref(null);

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    isVisible.value = true;
                }
            });
        },
        { threshold: 0.2 }
    );

    if (sectionRef.value) {
        observer.observe(sectionRef.value);
    }
});
</script>

<template>
    <div 
        ref="sectionRef"
        :class="['mb-12 transition-all duration-700', centered ? 'text-center' : 'text-left', isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
    >
        <h2 class="text-3xl md:text-5xl font-bold font-heading text-forest-deep mb-4 relative inline-block">
            {{ title }}
            <!-- Decorative underline -->
            <div 
              :class="['h-1 bg-gradient-to-r from-forest-emerald to-forest-400 rounded-full mt-3 transition-all duration-700 delay-300', centered ? 'mx-auto' : '', isVisible ? 'w-20 opacity-100' : 'w-0 opacity-0']"
            ></div>
        </h2>
        <p v-if="subtitle" class="text-base sm:text-lg text-gray-500 font-body max-w-2xl leading-relaxed" :class="centered ? 'mx-auto' : ''">
            {{ subtitle }}
        </p>
    </div>
</template>
