<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const embedUrl = computed(() => {
    // If the admin saved an actual iframe src, return it. Otherwise, return null to show a friendly notice or use standard embed strategy.
    if(settings.value.google_map_embed) {
        return settings.value.google_map_embed;
    }
    return 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15852.126442654308!2d107.0984814!3d-6.6430338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69b5966eb8ad2d%3A0xe54e81cb8d956557!2sKaryamekar%2C%20Kec.%20Cariu%2C%20Kabupaten%20Bogor%2C%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid'; 
});

const waUrl = computed(() => {
    return settings.value.kontak_wa ? `https://wa.me/${settings.value.kontak_wa.replace(/\D/g,'')}` : '#';
});
</script>

<template>
    <PublicLayout>
        <Head title="Kontak Kami - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-7xl mx-auto">
                <SectionTitle 
                    title="Hubungi Sims" 
                    subtitle="Tanyakan apapun mengenai resort, reservasi, maupun event yang ingin Anda langsungkan di lokasi kami." 
                />
                
                <div class="grid lg:grid-cols-2 gap-12 mt-12 bg-white rounded-3xl overflow-hidden shadow-forest border border-forest-50 p-6 md:p-10">
                    <!-- Text Contact -->
                    <div class="space-y-12">
                        <div>
                            <h3 class="text-2xl font-bold font-heading text-forest-900 mb-6">Informasi Kontak</h3>
                            <div class="space-y-6">
                                <div class="flex gap-4 items-start">
                                    <div class="w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 mb-1">WhatsApp Reservasi</p>
                                        <a :href="waUrl" target="_blank" class="text-lg text-forest-emerald hover:text-forest-700 font-medium transition-colors">
                                            {{ settings.kontak_wa || 'Belum diisi' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="flex gap-4 items-start">
                                    <div class="w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 mb-1">Alamat Lokasi</p>
                                        <p class="text-gray-600 leading-relaxed">{{ settings.lokasi || 'Belum diisi' }}</p>
                                        <a v-if="settings.google_map_url" :href="settings.google_map_url" target="_blank" class="inline-flex items-center text-sm font-semibold text-forest-emerald mt-2 hover:underline">
                                            Buka di Google Maps <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Open Hours -->
                        <div>
                            <h3 class="text-2xl font-bold font-heading text-forest-900 mb-6">Jam Operasional</h3>
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="font-bold text-gray-700">Senin - Jumat</span>
                                    <span class="text-forest-600 font-semibold">{{ settings.operational_hour_weekday_open || '08:00' }} - {{ settings.operational_hour_weekday_close || '17:00' }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="font-bold text-gray-700">Sabtu - Minggu</span>
                                    <span class="text-forest-600 font-semibold">{{ settings.operational_hour_weekend_open || '07:00' }} - {{ settings.operational_hour_weekend_close || '18:00' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Maps Embed -->
                    <div class="h-[400px] lg:h-full min-h-[400px] rounded-2xl overflow-hidden border border-gray-100 bg-gray-50 relative">
                        <iframe 
                            :src="embedUrl" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="absolute inset-0"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
