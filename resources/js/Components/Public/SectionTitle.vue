<template>
    <div :class="['mb-12', centered ? 'text-center' : 'text-left']">
        <h2 class="text-3xl md:text-5xl font-bold font-heading text-forest-deep mb-4 relative inline-block">
            {{ title }}
            <!-- Decorative underline -->
            <div :class="['h-1.5 bg-forest-emerald rounded-full mt-2 opacity-80', centered ? 'w-24 mx-auto' : 'w-24']"></div>
        </h2>
        <p v-if="subtitle" class="text-lg text-gray-600 font-body max-w-2xl" :class="centered ? 'mx-auto' : ''">
            {{ subtitle }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
    centered: { type: Boolean, default: true }
});
</script>
