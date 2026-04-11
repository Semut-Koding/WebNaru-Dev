<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import ItemCard from '@/Components/Public/ItemCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    villas: { type: Array, required: true }
});
</script>

<template>
    <PublicLayout>
        <Head title="Akomodasi & Villa - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-7xl mx-auto">
                <SectionTitle 
                    title="Akomodasi Pilihan" 
                    subtitle="Pilihan villa, glamping, dan cabin untuk menyempurnakan istirahat Anda." 
                />
                
                <div v-if="villas.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ItemCard 
                        v-for="villa in villas" 
                        :key="villa.id"
                        :title="villa.name"
                        :description="'Kapasitas: ' + villa.capacity + ' | ' + villa.amenities"
                        :imageUrl="villa.media?.length > 0 ? villa.media[0].original_url : null"
                        :price="Number(villa.price_per_night)"
                        badgeText="Staycation"
                        :link="route('villas.detail', villa.id)"
                    />
                </div>
                
                <div v-else class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-forest-50 text-forest-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Belum ada Villa Tersedia</h3>
                    <p class="text-gray-500 mt-2">Daftar villa sedang dalam penambahan sistem kami.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
