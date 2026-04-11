<template>
    <section class="relative w-full h-[85vh] min-h-[600px] flex items-center justify-center overflow-hidden">
        <!-- Background Image/Video -->
        <div class="absolute inset-0 w-full h-full">
            <img v-if="imageUrl" :src="imageUrl" alt="Hero Background" class="w-full h-full object-cover scale-105 animate-[slow-zoom_20s_ease-in-out_infinite_alternate]" />
            <div v-else class="w-full h-full bg-forest-900 object-cover"></div>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-forest-900/60 via-forest-900/40 to-forest-900/80"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white mb-6 animate-[fade-in-down_1s_ease-out]">
                <span class="w-2 h-2 rounded-full bg-forest-300 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wide uppercase">Selamat Datang di Sims</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold text-white font-heading leading-tight mb-6 animate-[fade-in-up_1s_ease-out_0.2s_both]">
                {{ title }}
            </h1>
            
            <p class="text-lg md:text-2xl text-forest-50 mb-10 max-w-3xl font-body leading-relaxed animate-[fade-in-up_1s_ease-out_0.4s_both]">
                {{ subtitle }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 animate-[fade-in-up_1s_ease-out_0.6s_both]">
                <a :href="ctaLink" target="_blank" class="px-8 py-4 bg-forest-emerald hover:bg-forest-400 text-white rounded-full font-semibold text-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_8px_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-2 group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:scale-110"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    {{ ctaText }}
                </a>
                <a href="#explore" class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white rounded-full font-semibold text-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    Eksplorasi Layanan
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <a href="#explore" class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-white/70 hover:text-white transition-colors animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
        </a>
    </section>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, required: true },
    imageUrl: { type: String, default: null },
    ctaLink: { type: String, required: true },
    ctaText: { type: String, default: 'Hubungi Kami' }
});
</script>

<style scoped>
@keyframes slow-zoom {
    from { transform: scale(1.05); }
    to { transform: scale(1.15); }
}
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in-down {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
