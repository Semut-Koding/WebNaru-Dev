<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import GalleryLightbox from '@/Components/Public/GalleryLightbox.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    villa: { type: Object, required: true }
});

const page = usePage();
const settings = computed(() => page.props.settings || {});

const formatedPrice = computed(() => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(props.villa.base_price_weekday);
});

// Assuming 'available' units are those that can be booked
const availableUnits = computed(() => {
    if(!props.villa.units) return 0;
    return props.villa.units.filter(u => u.status === 'available').length;
});
</script>

<template>
    <PublicLayout>
        <Head :title="villa.name + ' - Sims'" />
        
        <div class="pt-4 pb-20 px-4 md:px-0">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="flex text-gray-500 text-sm mb-8 font-medium">
                    <Link :href="route('home')" class="hover:text-forest-emerald transition-colors">Beranda</Link>
                    <span class="mx-2">/</span>
                    <Link :href="route('villas')" class="hover:text-forest-emerald transition-colors">Villa</Link>
                    <span class="mx-2">/</span>
                    <span class="text-forest-900">{{ villa.name }}</span>
                </nav>

                <div class="bg-white rounded-3xl shadow-forest overflow-hidden mb-12">
                    <!-- Main Cover -->
                    <div class="h-[40vh] md:h-[50vh] relative bg-gray-100">
                        <img v-if="villa.media?.length > 0" :src="villa.media[0].original_url" class="w-full h-full object-cover" :alt="villa.name" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 p-8 w-full">
                            <span class="inline-block px-4 py-1.5 bg-forest-emerald text-white rounded-full text-sm font-bold tracking-wide uppercase shadow-lg mb-4">
                                Kapasitas: {{ villa.capacity }} Orang
                            </span>
                            <h1 class="text-4xl md:text-5xl font-bold font-heading text-white">{{ villa.name }}</h1>
                        </div>
                    </div>

                    <div class="p-8 md:p-12 grid md:grid-cols-3 gap-12">
                        <!-- Left Details -->
                        <div class="md:col-span-2 space-y-12">
                            <div>
                                <h3 class="text-2xl font-bold font-heading text-forest-900 border-b border-forest-100 pb-3 mb-6">Tentang Akomodasi Ini</h3>
                                <p class="text-gray-700 leading-relaxed font-body whitespace-pre-wrap">{{ villa.description }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-2xl font-bold font-heading text-forest-900 border-b border-forest-100 pb-3 mb-6">Fasilitas Termasuk</h3>
                                <div class="bg-forest-50/50 p-6 rounded-2xl">
                                    <p class="text-gray-700 whitespace-pre-wrap leading-loose">{{ villa.amenities }}</p>
                                </div>
                            </div>

                            <div v-if="villa.media?.length > 1">
                                <h3 class="text-2xl font-bold font-heading text-forest-900 border-b border-forest-100 pb-3 mb-6">Potret Menginap</h3>
                                <GalleryLightbox :images="villa.media" />
                            </div>
                        </div>

                        <!-- Right Sidebar Card -->
                        <div class="md:col-span-1">
                            <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.06)] p-6 md:p-8 rounded-3xl border border-gray-100 sticky top-28">
                                <div class="mb-6 pb-6 border-b border-gray-100">
                                    <p class="text-sm text-gray-500 font-medium mb-1">Mulai Dari</p>
                                    <p class="text-3xl font-bold text-forest-900">{{ formatedPrice }}<span class="text-base text-gray-400 font-normal">/malam</span></p>
                                </div>
                                
                                <div class="space-y-4 mb-8">
                                    <div class="flex items-center text-gray-600 gap-3">
                                        <div class="w-10 h-10 rounded-full bg-forest-50 text-forest-emerald flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Kapasitas Tamu</p>
                                            <p class="text-sm">Ideal untuk {{ villa.capacity }} orang</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600 gap-3">
                                        <div class="w-10 h-10 rounded-full bg-forest-50 text-forest-emerald flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Ketersediaan Unit</p>
                                            <p class="text-sm">{{ availableUnits > 0 ? availableUnits + ' Unit Tersedia' : 'Penuh' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <a :href="`https://wa.me/${settings.kontak_wa?.replace(/\D/g,'')}?text=Halo,%20saya%20tertarik%20untuk%20reservasi%20${villa.name}`" target="_blank" class="w-full py-4 bg-forest-900 hover:bg-forest-700 text-white rounded-xl font-bold text-center flex justify-center items-center gap-2 transition-transform transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    Booking via WhatsApp
                                </a>
                                <p class="text-center text-xs text-gray-400 mt-4">Pemesanan online instan segera hadir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
