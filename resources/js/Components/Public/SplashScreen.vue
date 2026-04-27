<script setup>
import { ref, onMounted } from 'vue'

const isVisible = ref(true)
const isExiting = ref(false)

onMounted(() => {
  // Wait for everything to load, then slide up
  const hideAfterLoad = () => {
    setTimeout(() => {
      isExiting.value = true
      setTimeout(() => {
        isVisible.value = false
      }, 800) // match animation duration
    }, 600) // small delay after load for smooth feel
  }

  if (document.readyState === 'complete') {
    hideAfterLoad()
  } else {
    window.addEventListener('load', hideAfterLoad, { once: true })
  }
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      :class="['splash-screen', { 'splash-exit': isExiting }]"
    >
      <!-- Background Pattern -->
      <div class="splash-bg"></div>
      
      <!-- Content -->
      <div class="splash-content">
        <div class="splash-brand">
          <h1 class="splash-title">Naru</h1>
          <p class="splash-subtitle">Forest</p>
        </div>
        <div class="splash-loader">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.splash-screen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1B4D3E;
  overflow: hidden;
}

.splash-bg {
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(ellipse at 30% 20%, rgba(16, 185, 129, 0.15) 0%, transparent 60%),
    radial-gradient(ellipse at 70% 80%, rgba(6, 95, 70, 0.2) 0%, transparent 60%);
}

.splash-content {
  position: relative;
  z-index: 1;
  text-align: center;
  animation: fade-in-up 0.8s ease-out;
}

.splash-brand {
  margin-bottom: 2rem;
}

.splash-title {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  font-weight: 700;
  color: white;
  letter-spacing: -0.02em;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.splash-subtitle {
  font-family: 'Outfit', sans-serif;
  font-size: 1.5rem;
  font-weight: 300;
  color: rgba(167, 243, 208, 0.8);
  letter-spacing: 0.3em;
  text-transform: uppercase;
}

/* Loading dots */
.splash-loader {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

.splash-loader span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(167, 243, 208, 0.6);
  animation: loader-bounce 1.4s ease-in-out infinite;
}

.splash-loader span:nth-child(2) { animation-delay: 0.16s; }
.splash-loader span:nth-child(3) { animation-delay: 0.32s; }

@keyframes loader-bounce {
  0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
  40% { transform: scale(1); opacity: 1; }
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
