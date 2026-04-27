import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import LenisVue from 'lenis/vue'
import 'lenis/dist/lenis.css'
import '../css/app.css'

createInertiaApp({
  title: (title) => `${title} - Naru Forest`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(LenisVue)
      .mount(el)
  },
})
