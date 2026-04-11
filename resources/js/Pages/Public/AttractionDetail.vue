<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import GalleryLightbox from '@/Components/Public/GalleryLightbox.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    attraction: { type: Object, required: true }
});

const formatedPrice = computed(() => {
    return props.attraction.price ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(props.attraction.price) : 'Gratis / Termasuk Tiket';
});

const isAvailable = computed(() => props.attraction.status === 'active');
</script>

<template>
    <PublicLayout>
        <Head :title="attraction.name + ' - Sims'" />
        
        <div class="pt-4 pb-20 px-4 md:px-0">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="flex text-gray-500 text-sm mb-8 font-medium">
                    <Link :href="route('home')" class="hover:text-forest-emerald transition-colors">Beranda</Link>
                    <span class="mx-2">/</span>
                    <Link :href="route('attractions')" class="hover:text-forest-emerald transition-colors">Wahana</Link>
                    <span class="mx-2">/</span>
                    <span class="text-forest-900">{{ attraction.name }}</span>
                </nav>

                <div class="bg-white rounded-3xl shadow-forest overflow-hidden mb-12">
                    <!-- Main Cover -->
                    <div class="h-[40vh] md:h-[50vh] relative bg-gray-100">
                        <img v-if="attraction.media?.length > 0" :src="attraction.media[0].original_url" class="w-full h-full object-cover" :alt="attraction.name" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 p-8 w-full">
                            <span class="inline-block px-4 py-1.5 bg-forest-emerald text-white rounded-full text-sm font-bold tracking-wide uppercase shadow-lg mb-4">
                                {{ attraction.category }}
                            </span>
                            <h1 class="text-4xl md:text-5xl font-bold font-heading text-white">{{ attraction.name }}</h1>
                        </div>
                    </div>

                    <div class="p-8 md:p-12 grid md:grid-cols-3 gap-12">
                        <!-- Left Details -->
                        <div class="md:col-span-2 space-y-8">
                            <div>
                                <h3 class="text-2xl font-bold font-heading text-forest-900 border-b border-forest-100 pb-3 mb-4">Deskripsi Wahana</h3>
                                <p class="text-gray-700 leading-relaxed font-body whitespace-pre-wrap">{{ attraction.description }}</p>
                            </div>

                            <div v-if="attraction.media?.length > 1">
                                <h3 class="text-2xl font-bold font-heading text-forest-900 border-b border-forest-100 pb-3 mb-6">Galeri Wahana</h3>
                                <GalleryLightbox :images="attraction.media" />
                            </div>
                        </div>

                        <!-- Right Sidebar Card -->
                        <div class="md:col-span-1">
                            <div class="bg-forest-50 p-6 rounded-2xl border border-forest-100 sticky top-28">
                                <div class="mb-6">
                                    <p class="text-sm text-gray-500 font-medium mb-1">Harga Tiket</p>
                                    <p class="text-3xl font-bold text-forest-emerald">{{ formatedPrice }}</p>
                                </div>
                                
                                <div class="space-y-4 mb-8">
                                    <div class="flex justify-between items-center py-3 border-b border-forest-100/50">
                                        <span class="text-gray-600">Status</span>
                                        <span class="font-bold py-1 px-3 rounded-full text-xs uppercase tracking-wide"
                                              :class="{
                                                'bg-emerald-100 text-emerald-800': attraction.status === 'active',
                                                'bg-orange-100 text-orange-800': attraction.status === 'coming_soon',
                                                'bg-red-100 text-red-800': attraction.status === 'maintenance'
                                              }">
                                            {{ attraction.status === 'coming_soon' ? 'Segera Hadir' : attraction.status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <a v-if="isAvailable" href="https://wa.me/6281388819088" target="_blank" class="w-full py-4 bg-forest-900 hover:bg-forest-700 text-white rounded-xl font-bold text-center flex justify-center items-center gap-2 transition-colors shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    Pesan Tiket Sekarang
                                </a>
                                <button v-else disabled class="w-full py-4 bg-gray-200 text-gray-500 rounded-xl font-bold cursor-not-allowed">
                                    Belum Tersedia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
