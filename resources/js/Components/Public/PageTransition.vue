<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const isLoading = ref(false)
const progress = ref(0)
let progressInterval = null

const startLoading = () => {
  isLoading.value = true
  progress.value = 0
  
  // Simulate progress
  progressInterval = setInterval(() => {
    if (progress.value < 90) {
      progress.value += Math.random() * 15
    }
  }, 200)
}

const finishLoading = () => {
  progress.value = 100
  clearInterval(progressInterval)
  
  setTimeout(() => {
    isLoading.value = false
    progress.value = 0
  }, 300)
}

onMounted(() => {
  router.on('start', startLoading)
  router.on('finish', finishLoading)
})

onUnmounted(() => {
  clearInterval(progressInterval)
})
</script>

<template>
  <Teleport to="body">
    <!-- Progress Bar -->
    <Transition name="fade">
      <div v-if="isLoading" class="fixed top-0 left-0 right-0 z-[9998]">
        <div class="h-[3px] bg-forest-200/20">
          <div 
            class="h-full bg-gradient-to-r from-forest-emerald via-forest-400 to-forest-emerald transition-all duration-300 ease-out rounded-r-full"
            :style="{ width: `${Math.min(progress, 100)}%` }"
          ></div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
