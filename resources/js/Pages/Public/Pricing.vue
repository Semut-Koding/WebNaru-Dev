<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const tiketUmum = computed(() => {
    try {
        return JSON.parse(settings.value.tiket_masuk_umum || '{}');
    } catch (e) {
        return { harga: 35000, keterangan: 'Termasuk kolam renang' };
    }
});

const hargaString = computed(() => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(tiketUmum.value.harga || 0);
});
</script>

<template>
    <PublicLayout>
        <Head title="Harga Tiket - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-4xl mx-auto">
                <SectionTitle 
                    title="Harga Tiket & Paket" 
                    subtitle="Informasi lengkap tiket masuk reguler dan tambahan fasilitas yang kami tawarkan." 
                />
                
                <!-- Main Ticket -->
                <div class="relative bg-gradient-to-br from-forest-900 to-forest-700 rounded-3xl p-8 md:p-12 text-white shadow-forest-lg overflow-hidden my-12">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                        <div>
                            <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold tracking-wide uppercase mb-4">
                                Tiket Reguler
                            </span>
                            <h3 class="text-3xl md:text-4xl font-bold font-heading mb-2">Tiket Masuk Umum</h3>
                            <p class="text-forest-100 max-w-sm">{{ tiketUmum.keterangan || 'Sudah termasuk akses area utama' }}</p>
                        </div>
                        <div class="text-center md:text-right shrink-0">
                            <p class="text-xs text-forest-200 uppercase tracking-widest font-bold mb-1">Harga Per Orang</p>
                            <p class="text-5xl font-bold text-forest-emerald bg-white px-6 py-4 rounded-2xl shadow-inner text-center">{{ hargaString }}</p>
                        </div>
                    </div>
                </div>
                
                <h3 class="text-2xl font-bold font-heading text-forest-900 mb-8 border-b-2 border-forest-100 pb-2 inline-block">Kenapa Memilih Sims?</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex gap-4 items-start p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Akses Penuh Area Hijau</h4>
                            <p class="text-sm text-gray-600 mt-1">Bebas berkeliling menikmati lahan seluas ratusan hektar.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 items-start p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20"/><path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/><path d="M4 12V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Wahana Tersedia</h4>
                            <p class="text-sm text-gray-600 mt-1">Pilihan Wahana edukasi yang tersebar di area taman bermain.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
