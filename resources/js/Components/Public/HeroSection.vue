<template>
    <section class="relative w-full h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 w-full h-full">
            <img 
              v-if="imageUrl" 
              :src="imageUrl" 
              alt="Hero Background" 
              class="w-full h-full object-cover" 
              :style="{ animation: 'slow-zoom 25s ease-in-out infinite alternate' }"
            />
            <div v-else class="w-full h-full bg-forest-900"></div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-forest-900/30 via-transparent to-forest-900/20"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto flex flex-col items-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full glass border border-white/20 text-white mb-8" style="animation: fade-in-down 1s ease-out">
                <span class="w-2 h-2 rounded-full bg-forest-300 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wider uppercase font-heading">Selamat Datang</span>
            </div>
            
            <!-- Heading with mixed typography -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white font-heading leading-tight mb-6" style="animation: fade-in-up 1s ease-out 0.2s both">
                {{ titleBefore }} 
                <span class="font-accent italic font-normal text-forest-200">{{ accentWord }}</span>
                {{ titleAfter }}
            </h1>
            
            <!-- Subtitle -->
            <p class="text-base sm:text-lg md:text-xl text-white/80 mb-10 max-w-2xl font-body leading-relaxed" style="animation: fade-in-up 1s ease-out 0.4s both">
                {{ subtitle }}
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4" style="animation: fade-in-up 1s ease-out 0.6s both">
                <a 
                  :href="ctaLink" 
                  target="_blank"
                  class="px-8 py-4 bg-forest-emerald hover:bg-forest-400 text-white rounded-full font-semibold text-base sm:text-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_8px_30px_rgba(16,185,129,0.4)] flex items-center justify-center gap-2 group"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:scale-110"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    {{ ctaText }}
                </a>
                <a 
                  href="#explore" 
                  class="px-8 py-4 glass hover:bg-white/20 text-white rounded-full font-semibold text-base sm:text-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2"
                >
                    Eksplorasi Layanan
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <a href="#explore" class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/50 hover:text-white transition-colors" style="animation: float 3s ease-in-out infinite">
            <div class="flex flex-col items-center gap-2">
                <span class="text-xs font-heading uppercase tracking-widest">Scroll</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
            </div>
        </a>
    </section>
</template>

<script setup>
defineProps({
    titleBefore: { type: String, default: 'Jelajahi Keindahan' },
    accentWord: { type: String, default: 'Alam' },
    titleAfter: { type: String, default: 'yang Memukau' },
    subtitle: { type: String, required: true },
    imageUrl: { type: String, default: null },
    ctaLink: { type: String, required: true },
    ctaText: { type: String, default: 'Hubungi Kami' }
});
</script>

<style scoped>
@keyframes slow-zoom {
    from { transform: scale(1.0); }
    to { transform: scale(1.1); }
}
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in-down {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes float {
    0%, 100% { transform: translate(-50%, 0); }
    50% { transform: translate(-50%, -8px); }
}
</style>
