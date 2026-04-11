<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import FaqAccordion from '@/Components/Public/FaqAccordion.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const faqData = computed(() => {
    try {
        return JSON.parse(settings.value.faq || '[]');
    } catch (e) {
        return [];
    }
});
</script>

<template>
    <PublicLayout>
        <Head title="Tanya Jawab (FAQ) - Sims" />
        
        <div class="pt-10 pb-20 px-4 md:px-0 bg-forest-50/50 min-h-[80vh]">
            <div class="max-w-4xl mx-auto">
                <SectionTitle 
                    title="Frequently Asked Questions" 
                    subtitle="Temukan jawaban cepat atas pertanyaan umum seputar kunjungan Anda di Sims." 
                />
                
                <div class="mt-12 bg-white p-8 md:p-12 rounded-3xl shadow-forest border border-forest-50 z-10 relative">
                    <FaqAccordion v-if="faqData.length > 0" :faqs="faqData" />
                    <div v-else class="text-center py-10 text-gray-500">
                        Belum ada data FAQ yang diisi.
                    </div>
                </div>
                
                <div class="text-center mt-12 bg-forest-900 text-white rounded-3xl p-10 shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
                    <h3 class="text-2xl font-bold font-heading mb-4 relative z-10">Masih punya pertanyaan?</h3>
                    <p class="text-forest-100 mb-8 relative z-10">Tim layanan pelanggan kami siap membantu memberikan informasi yang Anda butuhkan, kapan saja.</p>
                    <a :href="`https://wa.me/${settings.kontak_wa?.replace(/\D/g,'')}`" target="_blank" class="inline-flex items-center px-8 py-3 bg-forest-emerald hover:bg-forest-400 text-white rounded-full font-bold transition-all transform hover:scale-105 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Hubungi WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
