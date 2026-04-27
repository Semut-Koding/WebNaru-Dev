<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    stats: {
        type: Array,
        default: () => [
            { label: 'Wahana Aktif', value: 0, suffix: '+', icon: '🎢' },
            { label: 'Villa Tersedia', value: 0, suffix: '+', icon: '🏡' },
            { label: 'Pengunjung Bulan Ini', value: 0, suffix: '+', icon: '👥' },
            { label: 'Rating', value: 0, suffix: '/5', icon: '⭐' },
        ]
    }
});

const counters = ref(props.stats.map(() => 0));
const isVisible = ref(false);
const sectionRef = ref(null);

function animateCounters() {
    props.stats.forEach((stat, index) => {
        const target = stat.value;
        const duration = 2000;
        const steps = 60;
        const stepValue = target / steps;
        let current = 0;
        let step = 0;

        const interval = setInterval(() => {
            step++;
            current = Math.min(Math.round(stepValue * step), target);
            counters.value[index] = current;
            if (step >= steps) {
                clearInterval(interval);
                counters.value[index] = target;
            }
        }, duration / steps);
    });
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !isVisible.value) {
                    isVisible.value = true;
                    animateCounters();
                }
            });
        },
        { threshold: 0.3 }
    );

    if (sectionRef.value) {
        observer.observe(sectionRef.value);
    }

    onUnmounted(() => observer.disconnect());
});

function formatNumber(num) {
    if (num >= 1000) {
        return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
    }
    return num.toLocaleString('id-ID');
}
</script>

<template>
    <section ref="sectionRef" class="py-16 sm:py-20 relative overflow-hidden">
        <!-- Subtle bg pattern -->
        <div class="absolute inset-0 bg-gradient-to-b from-forest-50/50 to-white"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <div 
                    v-for="(stat, index) in stats" 
                    :key="index"
                    class="text-center p-6 sm:p-8 rounded-2xl bg-white/80 backdrop-blur-sm border border-forest-100/50 shadow-forest transition-all duration-500 hover:-translate-y-1 hover:shadow-forest-lg"
                    :class="{ 'opacity-100 translate-y-0': isVisible, 'opacity-0 translate-y-8': !isVisible }"
                    :style="{ transitionDelay: `${index * 0.15}s` }"
                >
                    <div class="text-3xl sm:text-4xl mb-3">{{ stat.icon }}</div>
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold font-heading text-forest-deep mb-2">
                        {{ formatNumber(counters[index]) }}<span class="text-forest-emerald">{{ stat.suffix }}</span>
                    </div>
                    <p class="text-sm sm:text-base text-gray-500 font-medium">{{ stat.label }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
