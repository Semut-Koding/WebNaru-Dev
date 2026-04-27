<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    banners: {
        type: Array,
        default: () => [
            {
                image: 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070',
                title: 'Promo Spesial Akhir Pekan',
                subtitle: 'Diskon hingga 30% untuk villa eksklusif kami',
            },
            {
                image: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070',
                title: 'Paket Keluarga Hemat',
                subtitle: 'Nikmati wahana seru bersama keluarga tercinta',
            },
            {
                image: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=2070',
                title: 'Glamping Romantis',
                subtitle: 'Pengalaman bermalam di alam terbuka yang tak terlupakan',
            },
        ]
    }
});

const currentSlide = ref(0);
const totalSlides = computed(() => props.banners.length);
let slideInterval = null;
let touchStartX = 0;
let touchEndX = 0;

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
}

function prevSlide() {
    currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value;
}

function goToSlide(index) {
    currentSlide.value = index;
    resetInterval();
}

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 5000);
}

function handleTouchStart(e) {
    touchStartX = e.changedTouches[0].screenX;
}

function handleTouchEnd(e) {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchStartX - touchEndX;
    if (Math.abs(diff) > 50) {
        if (diff > 0) nextSlide();
        else prevSlide();
        resetInterval();
    }
}

onMounted(() => {
    slideInterval = setInterval(nextSlide, 5000);
});

onUnmounted(() => {
    clearInterval(slideInterval);
});
</script>

<template>
    <section class="py-12 sm:py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Banner Carousel -->
            <div 
                class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-forest-lg group"
                @touchstart="handleTouchStart"
                @touchend="handleTouchEnd"
            >
                <!-- Slides -->
                <div class="relative aspect-[16/7] sm:aspect-[21/9] overflow-hidden">
                    <TransitionGroup name="slide">
                        <div 
                            v-for="(banner, index) in banners"
                            :key="index"
                            v-show="currentSlide === index"
                            class="absolute inset-0"
                        >
                            <img 
                                :src="banner.image" 
                                :alt="banner.title" 
                                class="w-full h-full object-cover"
                            />
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
                            
                            <!-- Text Content -->
                            <div class="absolute inset-0 flex items-center px-8 sm:px-12 lg:px-16">
                                <div class="max-w-lg">
                                    <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider text-white bg-forest-emerald/80 rounded-full mb-4">
                                        Promo
                                    </span>
                                    <h3 class="text-xl sm:text-2xl lg:text-4xl font-bold text-white font-heading mb-2 sm:mb-3 leading-tight">
                                        {{ banner.title }}
                                    </h3>
                                    <p class="text-sm sm:text-base text-white/80 line-clamp-2">
                                        {{ banner.subtitle }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>

                <!-- Navigation Arrows (Desktop) -->
                <button 
                    @click="prevSlide(); resetInterval()" 
                    class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full glass items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-white/30"
                    aria-label="Banner sebelumnya"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button 
                    @click="nextSlide(); resetInterval()" 
                    class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full glass items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-white/30"
                    aria-label="Banner selanjutnya"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <!-- Dot Indicators -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <button 
                        v-for="(_, index) in banners" 
                        :key="index"
                        @click="goToSlide(index)"
                        class="w-2 h-2 rounded-full transition-all duration-300"
                        :class="currentSlide === index ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/70'"
                        :aria-label="`Ke banner ${index + 1}`"
                    ></button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.6s ease;
}
.slide-enter-from {
    opacity: 0;
}
.slide-leave-to {
    opacity: 0;
}
</style>
