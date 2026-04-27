<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { VueLenis } from 'lenis/vue';
import SplashScreen from '@/Components/Public/SplashScreen.vue';
import PageTransition from '@/Components/Public/PageTransition.vue';

const props = defineProps({
  transparentNavbar: { type: Boolean, default: false }
});

const page = usePage();
const brand = computed(() => page.props.brand);
const settings = computed(() => page.props.settings || {});

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Navbar classes based on scroll + transparent mode
const navbarClasses = computed(() => {
    if (props.transparentNavbar && !isScrolled.value) {
        return 'bg-transparent border-transparent py-4';
    }
    return 'glass-white shadow-sm border-gray-100/50 py-3';
});

const navTextClass = computed(() => {
    if (props.transparentNavbar && !isScrolled.value) {
        return 'text-white';
    }
    return 'text-forest-900';
});

const navHoverClass = computed(() => {
    if (props.transparentNavbar && !isScrolled.value) {
        return 'hover:bg-white/15';
    }
    return 'hover:bg-forest-50';
});

const navActiveClass = computed(() => {
    if (props.transparentNavbar && !isScrolled.value) {
        return 'bg-white/20 text-white';
    }
    return 'bg-forest-50 text-forest-600';
});

const currentRoute = computed(() => page.url);

function isActive(routeName) {
    try {
        const url = route(routeName);
        return currentRoute.value === new URL(url, window.location.origin).pathname;
    } catch {
        return false;
    }
}

const navItems = [
    { name: 'Beranda', route: 'home' },
    { name: 'Tentang', route: 'about' },
    { name: 'Villa', route: 'villas' },
    { name: 'Wahana', route: 'attractions' },
    { name: 'Harga', route: 'pricing' },
    { name: 'Galeri', route: 'gallery' },
    { name: 'Kontak', route: 'contact' },
];

const lenisOptions = {
    lerp: 0.1,
    smoothWheel: true,
    wheelMultiplier: 1,
};
</script>

<template>
  <VueLenis root :options="lenisOptions">
    <div class="min-h-screen flex flex-col font-body text-gray-800 bg-bg-light">
      <!-- Splash Screen -->
      <SplashScreen />

      <!-- Page Transition -->
      <PageTransition />

      <!-- Navbar -->
      <header 
        class="fixed w-full z-50 transition-all duration-500 border-b"
        :class="navbarClasses"
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center">
            <!-- Brand -->
            <div class="flex items-center gap-3">
              <Link :href="route('home')" class="flex items-center gap-2 group">
                <span 
                  class="text-2xl font-bold font-accent transition-colors"
                  :class="navTextClass"
                >
                  {{ brand.name }}
                </span>
              </Link>
            </div>
            
            <!-- Center Nav (Desktop) -->
            <nav class="hidden lg:flex items-center gap-1 px-2 py-1.5 rounded-full transition-all duration-300"
                 :class="transparentNavbar && !isScrolled ? 'bg-white/10 backdrop-blur-md border border-white/20' : 'bg-forest-50/50 border border-forest-100/50'"
            >
              <Link 
                v-for="item in navItems" 
                :key="item.route"
                :href="route(item.route)" 
                class="px-4 py-2 rounded-full text-sm font-medium font-heading transition-all duration-200"
                :class="[
                  isActive(item.route) ? navActiveClass : navTextClass,
                  !isActive(item.route) ? navHoverClass : ''
                ]"
              >
                {{ item.name }}
              </Link>
            </nav>
            
            <!-- CTA (Desktop) -->
            <div class="hidden lg:flex items-center space-x-4">
              <Link 
                :href="route('contact')" 
                class="text-sm font-semibold px-5 py-2.5 rounded-full transition-all duration-300 shadow-sm"
                :class="transparentNavbar && !isScrolled 
                  ? 'text-white border border-white/30 hover:bg-white/20' 
                  : 'text-white bg-forest-600 hover:bg-forest-700 shadow-forest'"
              >
                Hubungi Kami
              </Link>
            </div>

            <!-- Mobile menu button -->
            <button 
              aria-label="Buka Menu Mobile" 
              @click="isMobileMenuOpen = true" 
              class="lg:hidden p-2 rounded-full transition-all duration-200"
              :class="[navTextClass, navHoverClass]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
          </div>
        </div>
      </header>

      <!-- Mobile Menu Overlay -->
      <Transition name="fade">
        <div 
          v-show="isMobileMenuOpen" 
          class="lg:hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[90]" 
          @click="isMobileMenuOpen = false"
        ></div>
      </Transition>

      <!-- Mobile Menu Sidebar -->
      <div 
        class="lg:hidden fixed top-0 right-0 h-full w-4/5 sm:w-80 bg-white shadow-2xl z-[100] flex flex-col transform transition-transform duration-300 ease-in-out"
        :class="isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
      >
        <!-- Sidebar Header -->
        <div class="px-6 py-5 flex items-center justify-between border-b border-gray-50">
          <span class="text-xl font-bold font-accent text-forest-900">{{ brand.name }}</span>
          <button aria-label="Tutup Menu Mobile" @click="isMobileMenuOpen = false" class="p-2 text-gray-500 hover:text-forest-600 hover:bg-forest-50 rounded-full transition-colors flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>

        <!-- Sidebar Links -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col p-6 space-y-1">
          <Link 
            v-for="item in navItems" 
            :key="item.route"
            :href="route(item.route)" 
            class="py-3.5 px-4 rounded-xl font-semibold transition-all font-heading"
            :class="isActive(item.route) ? 'bg-forest-50 text-forest-600' : 'text-forest-900 hover:bg-forest-50 hover:text-forest-600'"
            @click="isMobileMenuOpen = false"
          >
            {{ item.name }}
          </Link>
        </div>
        
        <!-- Sidebar Footer CTA -->
        <div class="p-6 mt-auto border-t border-gray-50 bg-gray-50/50">
          <Link :href="route('contact')" class="w-full flex items-center justify-center text-white bg-forest-600 hover:bg-forest-700 px-5 py-4 rounded-xl transition-all shadow-forest shadow-sm font-semibold font-heading text-lg" @click="isMobileMenuOpen = false">
            Hubungi Kami
          </Link>
        </div>
      </div>

      <!-- Main Content -->
      <main class="flex-1 w-full overflow-x-hidden" :class="transparentNavbar ? '' : 'pt-20'">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-bg-dark border-t border-forest-800 pt-16 pb-8 mt-auto text-forest-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
              <!-- Brand -->
              <div class="col-span-1 md:col-span-2 lg:col-span-1">
                  <p class="text-3xl font-bold text-white font-accent mb-4">{{ brand.name }}</p>
                  <p class="text-forest-200 text-sm leading-relaxed mb-6">{{ brand.tagline }}</p>
                  <div class="flex space-x-4">
                      <a v-if="settings.instagram" :href="settings.instagram" target="_blank" aria-label="Kunjungi Instagram Kami" class="w-10 h-10 rounded-full bg-forest-800 flex items-center justify-center hover:bg-forest-emerald hover:text-white transition-colors">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                      </a>
                  </div>
              </div>
              
              <!-- Quick Links -->
              <div>
                  <h4 class="text-white font-semibold mb-6 flex items-center">
                      <div class="w-2 h-2 bg-forest-emerald rounded-full mr-2"></div>
                      Tautan Cepat
                  </h4>
                  <ul class="space-y-4 text-sm">
                      <li><Link :href="route('villas')" class="hover:text-forest-300 transition-colors">Daftar Villa</Link></li>
                      <li><Link :href="route('attractions')" class="hover:text-forest-300 transition-colors">Wahana Rekreasi</Link></li>
                      <li><Link :href="route('pricing')" class="hover:text-forest-300 transition-colors">Harga Tiket</Link></li>
                      <li><Link :href="route('gallery')" class="hover:text-forest-300 transition-colors">Galeri Dokumentasi</Link></li>
                  </ul>
              </div>
              
              <!-- Help -->
              <div>
                  <h4 class="text-white font-semibold mb-6 flex items-center">
                      <div class="w-2 h-2 bg-forest-emerald rounded-full mr-2"></div>
                      Informasi
                  </h4>
                  <ul class="space-y-4 text-sm">
                      <li><Link :href="route('about')" class="hover:text-forest-300 transition-colors">Tentang Kami</Link></li>
                      <li><Link :href="route('faq')" class="hover:text-forest-300 transition-colors">Tanya Jawab (FAQ)</Link></li>
                      <li><Link :href="route('contact')" class="hover:text-forest-300 transition-colors">Hubungi Kami</Link></li>
                  </ul>
              </div>
              
              <!-- Contact -->
              <div>
                  <h4 class="text-white font-semibold mb-6 flex items-center">
                      <div class="w-2 h-2 bg-forest-emerald rounded-full mr-2"></div>
                      Kunjungi Kami
                  </h4>
                  <ul class="space-y-4 text-sm text-forest-200">
                      <li v-if="settings.lokasi" class="flex gap-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-forest-emerald"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                          <span>{{ settings.lokasi }}</span>
                      </li>
                      <li v-if="settings.kontak_wa" class="flex gap-3">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-forest-emerald"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                          <span>{{ settings.kontak_wa }}</span>
                      </li>
                  </ul>
              </div>
          </div>
          
          <div class="pt-8 border-t border-forest-800 text-center text-forest-400 text-sm flex flex-col md:flex-row justify-between items-center">
              <p>&copy; {{ new Date().getFullYear() }} {{ brand.name }}. All rights reserved.</p>
              <p class="mt-2 md:mt-0">Dirancang untuk Pengalaman Liburan Terbaik.</p>
          </div>
        </div>
      </footer>
    </div>
  </VueLenis>
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
