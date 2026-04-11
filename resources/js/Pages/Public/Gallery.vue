<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import GalleryLightbox from '@/Components/Public/GalleryLightbox.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    galleries: { type: Array, required: true }
});
</script>

<template>
    <PublicLayout>
        <Head title="Galeri Dokumentasi - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-7xl mx-auto">
                <SectionTitle 
                    title="Potret Kenangan Sims" 
                    subtitle="Kompilasi momen dan keasrian alam yang diabadikan di kawasan kami." 
                />
                
                <div v-if="galleries.length > 0" class="mt-12">
                    <GalleryLightbox :images="galleries" />
                </div>
                
                <div v-else class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100 mt-12">
                     <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-forest-50 text-forest-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Album Kosong</h3>
                    <p class="text-gray-500 mt-2">Daftar dokumentasi akan segera diunggah oleh pihak admin.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
