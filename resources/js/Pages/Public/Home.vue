<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import HeroSection from '@/Components/Public/HeroSection.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import ItemCard from '@/Components/Public/ItemCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    featured_villas: { type: Array, default: () => [] },
    featured_attractions: { type: Array, default: () => [] },
});

const page = usePage();
const settings = computed(() => page.props.settings || {});
</script>

<template>
    <PublicLayout>
        <Head>
            <title>{{ settings.seo_title || 'Beranda' }}</title>
            <meta name="description" :content="settings.seo_description">
            <meta name="keywords" :content="settings.seo_keywords">
        </Head>

        <!-- Hero Section -->
        <HeroSection 
            :title="settings.hero_title || 'Selamat Datang di Sims'"
            :subtitle="settings.hero_subtitle || 'Nikmati alam dan lepaskan penat Anda.'"
            :ctaLink="route('villas')"
            ctaText="Pesan Sekarang"
        />

        <!-- Layanan Kami -->
        <section id="explore" class="py-20 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle 
                    title="Jelajah Pesona Alam" 
                    subtitle="Temukan wahana bermain alam dan villa dengan view spektakuler yang menyejukkan hati." 
                />
                
                <div class="grid md:grid-cols-2 gap-12 items-center mt-12">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Pendidikan & Rekreasi</h3>
                                <p class="text-gray-600">Berbagai wahana yang memanjakan keluarga sekaligus mendidik kecintaan terhadap alam bebas.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Villa Eksotis</h3>
                                <p class="text-gray-600">Rasakan sensasi bermalam dengan tenda glamping maupun cabin kayu yang menyatu dengan lingkungan hijau pegunungan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-video rounded-3xl overflow-hidden shadow-forest border-4 border-white">
                            <img src="https://images.unsplash.com/photo-1542718610-a1d656d1884c?q=80&w=2070" class="w-full h-full object-cover" alt="Sims Nature">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Wahana Unggulan -->
        <section class="py-20 bg-forest-50 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <SectionTitle 
                        title="Wahana Unggulan" 
                        subtitle="Eksplorasi aktivitas menyenangkan bersama kerabat." 
                        :centered="false"
                        class="mb-0"
                    />
                    <Link :href="route('attractions')" class="hidden md:inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors">
                        Lihat Semua 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ItemCard 
                        v-for="attraction in featured_attractions" 
                        :key="attraction.id"
                        :title="attraction.name"
                        :description="attraction.description"
                        :imageUrl="attraction.media?.length > 0 ? attraction.media[0].original_url : null"
                        :status="attraction.status"
                        :badgeText="attraction.category"
                        :link="route('attractions.detail', attraction.id)"
                    />
                </div>
            </div>
        </section>

        <!-- Villa Unggulan -->
        <section class="py-20 bg-white px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <SectionTitle 
                        title="Akomodasi & Villa" 
                        subtitle="Lepas penat dengan bermalam di resor pegunungan eksklusif." 
                        :centered="false"
                        class="mb-0"
                    />
                    <Link :href="route('villas')" class="hidden md:inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors">
                        Lihat Semua 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ItemCard 
                        v-for="villa in featured_villas" 
                        :key="villa.id"
                        :title="villa.name"
                        :description="'Fasilitas: ' + villa.amenities"
                        :imageUrl="villa.media?.length > 0 ? villa.media[0].original_url : null"
                        :price="Number(villa.price_per_night)"
                        badgeText="Staycation"
                        :link="route('villas.detail', villa.id)"
                    />
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
