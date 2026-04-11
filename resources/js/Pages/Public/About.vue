<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});
</script>

<template>
    <PublicLayout>
        <Head title="Tentang Kami - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0">
            <div class="max-w-4xl mx-auto">
                <SectionTitle 
                    title="Mengenal Lebih Dekat" 
                    subtitle="Menghadirkan wisata ramah keluarga dengan balutan alam asri." 
                />
                
                <!-- Hero Image About -->
                <div class="rounded-3xl overflow-hidden shadow-forest-lg mb-12 h-[60vh]">
                    <img src="https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?q=80&w=2070" class="w-full h-full object-cover" alt="Pemandangan Alam Sims" />
                </div>
                
                <!-- Content -->
                <div class="bg-white p-8 md:p-12 rounded-3xl shadow-forest text-lg text-gray-700 leading-relaxed font-body">
                    <p class="whitespace-pre-wrap">{{ settings.about_content_long || 'Informasi institusi belum diisi.' }}</p>
                </div>
                
                <!-- Vision & Mission (Optional future additions) -->
                <div class="grid md:grid-cols-2 gap-8 mt-12">
                    <div class="bg-forest-50 p-8 rounded-3xl border border-forest-100">
                        <div class="w-12 h-12 bg-forest-emerald rounded-full text-white flex items-center justify-center mb-6 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold font-heading text-forest-900 mb-4">Visi Kami</h3>
                        <p class="text-gray-600">Menjadi referensi utama pariwisata alam berkelanjutan yang menyelaraskan hiburan modern dengan ekologi hijau, memberikan pengalaman memori abadi bagi pengunjung.</p>
                    </div>
                    
                    <div class="bg-earth-wood/10 p-8 rounded-3xl border border-earth-wood/20">
                        <div class="w-12 h-12 bg-earth-wood rounded-full text-white flex items-center justify-center mb-6 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold font-heading text-forest-900 mb-4">Misi Kami</h3>
                        <p class="text-gray-600">Senantiasa memberikan pelayanan prima, fasilitas rekreasi aman bagi segala usia, menjaga konservasi lingkungan sekitar, serta memberdayakan ekonomi masyarakat wisata setempat.</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
