<template>
    <div class="w-full max-w-3xl mx-auto space-y-4">
        <div v-for="(item, index) in limitedFaqs" :key="index" 
             class="border border-gray-100 rounded-2xl overflow-hidden bg-white shadow-sm transition-all duration-300"
             :class="activeIndex === index ? 'ring-2 ring-forest-300 shadow-forest' : 'hover:border-forest-200'">
             
            <button @click="toggle(index)" 
                    class="w-full flex items-center justify-between p-5 text-left bg-white focus:outline-none"
                    :aria-expanded="activeIndex === index">
                <span class="font-semibold text-lg text-forest-900 group-hover:text-forest-600 transition-colors">
                    {{ item.pertanyaan }}
                </span>
                <span class="flex-shrink-0 ml-4 transition-transform duration-300 flex items-center justify-center w-8 h-8 rounded-full"
                      :class="activeIndex === index ? 'rotate-180 bg-forest-emerald text-white' : 'bg-forest-50 text-forest-600'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </span>
            </button>
            
            <div v-show="activeIndex === index" 
                 class="px-5 pb-5 text-gray-600 leading-relaxed overflow-hidden transition-all duration-500 animate-[fade-in-down_0.3s_ease-out]">
                {{ item.jawaban }}
            </div>
        </div>
        
        <div v-if="faqs.length > limit" class="text-center pt-4">
            <Link v-if="showMoreLink" :href="route('faq')" class="inline-flex items-center text-forest-600 font-semibold hover:text-forest-800 transition-colors">
                Lihat Semua Pertanyaan
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    faqs: { type: Array, required: true },
    limit: { type: Number, default: 999 },
    showMoreLink: { type: Boolean, default: false }
});

const activeIndex = ref(null);

const toggle = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};

const limitedFaqs = computed(() => {
    return props.faqs.slice(0, props.limit);
});
</script>

<style scoped>
@keyframes fade-in-down {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
