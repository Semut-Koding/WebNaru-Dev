<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import HeroSection from '@/Components/Public/HeroSection.vue';
import StatsCounter from '@/Components/Public/StatsCounter.vue';
import PromoBanner from '@/Components/Public/PromoBanner.vue';
import SectionTitle from '@/Components/Public/SectionTitle.vue';
import ItemCard from '@/Components/Public/ItemCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    featured_villas: { type: Array, default: () => [] },
    featured_attractions: { type: Array, default: () => [] },
    stats: { type: Array, default: () => [] },
});

const page = usePage();
const settings = computed(() => page.props.settings || {});

// Scroll reveal for sections
const sectionRefs = ref([]);

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        },
        { threshold: 0.1 }
    );

    document.querySelectorAll('.reveal').forEach((el) => {
        observer.observe(el);
    });
});
</script>

<template>
    <PublicLayout :transparentNavbar="true">
        <Head>
            <title>{{ settings.seo_title || 'Beranda' }}</title>
            <meta name="description" :content="settings.seo_description">
            <meta name="keywords" :content="settings.seo_keywords">
        </Head>

        <!-- Hero Section — Full Screen -->
        <HeroSection 
            :titleBefore="settings.hero_title || 'Jelajahi Keindahan'"
            :accentWord="settings.hero_accent || 'Alam'"
            :titleAfter="settings.hero_title_after || 'yang Memukau'"
            :subtitle="settings.hero_subtitle || 'Nikmati ketenangan alam pegunungan, wahana seru, dan villa eksklusif yang menyejukkan hati.'"
            :ctaLink="settings.kontak_wa ? `https://wa.me/${settings.kontak_wa.replace(/[^0-9]/g, '')}` : route('contact')"
            ctaText="Hubungi Kami"
            imageUrl="https://images.unsplash.com/photo-1448375240586-882707db888b?q=80&w=2070"
        />

        <!-- Stats Counter -->
        <StatsCounter :stats="stats" />

        <!-- Promo Banner -->
        <PromoBanner />

        <!-- Jelajah Pesona Alam -->
        <section id="explore" class="py-16 sm:py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle 
                    title="Jelajah Pesona Alam" 
                    subtitle="Temukan wahana bermain alam dan villa dengan view spektakuler yang menyejukkan hati." 
                />
                
                <div class="grid md:grid-cols-2 gap-8 lg:gap-12 items-center mt-12">
                    <div class="space-y-8 reveal">
                        <div class="flex items-start gap-4 p-5 rounded-2xl hover:bg-forest-50/50 transition-colors duration-300">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-forest-100 to-forest-200 flex items-center justify-center text-forest-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Pendidikan & Rekreasi</h3>
                                <p class="text-gray-500 leading-relaxed">Berbagai wahana yang memanjakan keluarga sekaligus mendidik kecintaan terhadap alam bebas.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-5 rounded-2xl hover:bg-forest-50/50 transition-colors duration-300">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-forest-100 to-forest-200 flex items-center justify-center text-forest-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Villa Eksotis</h3>
                                <p class="text-gray-500 leading-relaxed">Rasakan sensasi bermalam dengan tenda glamping maupun cabin kayu yang menyatu dengan lingkungan hijau pegunungan.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-5 rounded-2xl hover:bg-forest-50/50 transition-colors duration-300">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-forest-100 to-forest-200 flex items-center justify-center text-forest-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Alam yang Asri</h3>
                                <p class="text-gray-500 leading-relaxed">Udara segar pegunungan dengan pemandangan hijau yang menyegarkan mata dan menyehatkan pikiran.</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative reveal reveal-delay-2">
                        <div class="aspect-[4/5] sm:aspect-video rounded-3xl overflow-hidden shadow-forest-lg border-4 border-white">
                            <img src="https://images.unsplash.com/photo-1542718610-a1d656d1884c?q=80&w=2070" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" alt="Naru Forest Nature">
                        </div>
                        <!-- Decorative element -->
                        <div class="hidden lg:block absolute -bottom-6 -left-6 w-24 h-24 rounded-2xl bg-forest-100 -z-10"></div>
                        <div class="hidden lg:block absolute -top-6 -right-6 w-16 h-16 rounded-full bg-forest-emerald/20 -z-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Wahana Unggulan -->
        <section class="py-16 sm:py-24 bg-forest-50/50 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
                    <SectionTitle 
                        title="Wahana Unggulan" 
                        subtitle="Eksplorasi aktivitas menyenangkan bersama kerabat." 
                        :centered="false"
                        class="mb-0"
                    />
                    <Link :href="route('attractions')" class="hidden md:inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors group">
                        Lihat Semua 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 transition-transform group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="(attraction, index) in featured_attractions" :key="attraction.id" class="reveal" :class="`reveal-delay-${index + 1}`">
                        <ItemCard 
                            :title="attraction.name"
                            :description="attraction.description"
                            :imageUrl="attraction.media?.length > 0 ? attraction.media[0].original_url : null"
                            :status="attraction.status"
                            :badgeText="attraction.category"
                            :link="route('attractions.detail', attraction.id)"
                        />
                    </div>
                </div>

                <!-- Mobile "Lihat Semua" -->
                <div class="mt-8 text-center md:hidden">
                    <Link :href="route('attractions')" class="inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors">
                        Lihat Semua Wahana
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Villa Unggulan -->
        <section class="py-16 sm:py-24 bg-white px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
                    <SectionTitle 
                        title="Akomodasi & Villa" 
                        subtitle="Lepas penat dengan bermalam di resor pegunungan eksklusif." 
                        :centered="false"
                        class="mb-0"
                    />
                    <Link :href="route('villas')" class="hidden md:inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors group">
                        Lihat Semua 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 transition-transform group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="(villa, index) in featured_villas" :key="villa.id" class="reveal" :class="`reveal-delay-${index + 1}`">
                        <ItemCard 
                            :title="villa.name"
                            :description="'Fasilitas: ' + villa.amenities"
                            :imageUrl="villa.media?.length > 0 ? villa.media[0].original_url : null"
                            :price="Number(villa.price_per_night || villa.base_price_weekday)"
                            badgeText="Staycation"
                            :link="route('villas.detail', villa.id)"
                        />
                    </div>
                </div>

                <!-- Mobile "Lihat Semua" -->
                <div class="mt-8 text-center md:hidden">
                    <Link :href="route('villas')" class="inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors">
                        Lihat Semua Villa
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </Link>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
