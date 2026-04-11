<template>
    <Link :href="link" class="group block relative rounded-2xl overflow-hidden bg-white shadow-forest hover:shadow-forest-lg transition-all duration-300 transform hover:-translate-y-2 border border-forest-50 focus:outline-none focus:ring-4 focus:ring-forest-300">
        <!-- Image Cover -->
        <div class="relative h-64 overflow-hidden bg-gray-100">
            <img v-if="imageUrl" :src="imageUrl" :alt="title" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-forest-50/50">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
            
            <!-- Badges -->
            <div class="absolute top-4 left-4 flex gap-2">
                <span v-if="status === 'coming_soon' || status === 'maintenance'" 
                      class="px-3 py-1 text-xs font-bold uppercase tracking-wider text-white rounded-full shadow-md backdrop-blur-sm"
                      :class="status === 'coming_soon' ? 'bg-sunrise-orange/90' : 'bg-red-500/90'">
                    {{ status === 'coming_soon' ? 'Segera Hadir' : 'Maintenance' }}
                </span>
                <span v-else-if="badgeText" class="px-3 py-1 text-xs font-bold uppercase tracking-wider text-forest-900 bg-forest-300 rounded-full shadow-md">
                    {{ badgeText }}
                </span>
            </div>
            
            <div v-if="price" class="absolute bottom-4 right-4 px-4 py-2 bg-white/90 backdrop-blur-md rounded-xl shadow-lg font-bold text-forest-900">
                Rp {{ price.toLocaleString('id-ID') }}
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <h3 class="text-xl font-bold font-heading text-gray-900 group-hover:text-forest-600 transition-colors mb-2">
                {{ title }}
            </h3>
            <p class="text-gray-600 line-clamp-3 text-sm leading-relaxed mb-4">
                {{ description }}
            </p>
            <div class="flex items-center text-forest-emerald font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Lihat Detail
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    imageUrl: { type: String, default: null },
    status: { type: String, default: 'active' }, // active, coming_soon, maintenance
    price: { type: Number, default: null },
    badgeText: { type: String, default: null },
    link: { type: String, required: true }
});
</script>
