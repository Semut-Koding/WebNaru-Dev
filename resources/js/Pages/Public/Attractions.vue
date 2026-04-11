<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import ItemCard from '@/Components/Public/ItemCard.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    attractions: { type: Array, required: true }
});
</script>

<template>
    <PublicLayout>
        <Head title="Wahana Rekreasi - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-7xl mx-auto">
                <SectionTitle 
                    title="Wahana & Hiburan" 
                    subtitle="Pilih dan nikmati berbagai kegiatan menarik yang tersedia di tempat kami." 
                />
                
                <div v-if="attractions.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <ItemCard 
                        v-for="attraction in attractions" 
                        :key="attraction.id"
                        :title="attraction.name"
                        :description="attraction.description"
                        :imageUrl="attraction.media?.length > 0 ? attraction.media[0].original_url : null"
                        :status="attraction.status"
                        :badgeText="attraction.category"
                        :link="route('attractions.detail', attraction.id)"
                    />
                </div>
                
                <div v-else class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-forest-50 text-forest-300 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Belum ada Wahana</h3>
                    <p class="text-gray-500 mt-2">Daftar wahana sedang dalam tahap persiapan.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
