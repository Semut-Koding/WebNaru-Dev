<template>
    <div>
        <!-- Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="(image, index) in limitedImages" :key="index" 
                 class="group relative rounded-xl overflow-hidden cursor-pointer aspect-square bg-gray-100 shadow-sm hover:shadow-forest transition-all"
                 @click="openLightbox(index)">
                <img :src="(image.original_url || image.image_path).startsWith('http') ? (image.original_url || image.image_path) : '/' + (image.original_url || image.image_path)" 
                     :alt="image.name || image.title || 'Gallery Image'" 
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                
                <!-- Overlay Hover -->
                <div class="absolute inset-0 bg-forest-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transform scale-50 group-hover:scale-100 transition-transform duration-300"><path d="M15 3h6v6"/><path d="M9 21H3v-6"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 animate-[fade-in_0.3s_ease-out]">
                <!-- Close Button -->
                <button aria-label="Tutup Galeri" @click="closeLightbox" class="absolute top-6 right-6 text-white/50 hover:text-white p-2 rounded-full hover:bg-white/10 transition-colors z-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <!-- Navigation Controls -->
                <button aria-label="Gambar Sebelumnya" @click.stop="prev" v-if="images.length > 1" class="absolute left-4 md:left-10 text-white/50 hover:text-white p-4 rounded-full hover:bg-white/10 transition-colors z-50 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button aria-label="Gambar Selanjutnya" @click.stop="next" v-if="images.length > 1" class="absolute right-4 md:right-10 text-white/50 hover:text-white p-4 rounded-full hover:bg-white/10 transition-colors z-50 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <!-- Image Main Container -->
                <div class="relative max-w-6xl w-full h-full flex flex-col items-center justify-center" @click.self="closeLightbox">
                    <img :src="(currentImage.original_url || currentImage.image_path).startsWith('http') ? (currentImage.original_url || currentImage.image_path) : '/' + (currentImage.original_url || currentImage.image_path)" 
                         :alt="currentImage.name || currentImage.title || 'Gallery Image'" 
                         class="max-h-[85vh] max-w-full object-contain shadow-2xl rounded-sm animate-[zoom-in_0.3s_ease-out]" />
                         
                    <div v-if="currentImage.title" class="absolute bottom-10 px-6 py-3 bg-black/60 backdrop-blur-md text-white rounded-full font-medium tracking-wide">
                        {{ currentImage.title }}
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    images: { type: Array, required: true },
    limit: { type: Number, default: 999 }
});

const isOpen = ref(false);
const currentIndex = ref(0);

const limitedImages = computed(() => {
    return props.images.slice(0, props.limit);
});

const currentImage = computed(() => {
    return props.images[currentIndex.value];
});

const openLightbox = (index) => {
    currentIndex.value = index;
    isOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    isOpen.value = false;
    document.body.style.overflow = 'auto';
};

const next = () => {
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
};

const prev = () => {
    currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length;
};

// Keyboard listener for ESC, Left/Right arrow
const handleKeyup = (e) => {
    if (!isOpen.value) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
};

onMounted(() => {
    window.addEventListener('keyup', handleKeyup);
});

onUnmounted(() => {
    window.removeEventListener('keyup', handleKeyup);
    document.body.style.overflow = 'auto'; // safety
});
</script>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes zoom-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
