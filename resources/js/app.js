import './bootstrap'
import '../css/app.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp, h, ref } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import GuestLayout from './Layouts/GuestLayout.vue'
import { Ziggy } from './ziggy'
export { Ziggy };
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// ✅ global cart state

export const cartCount = ref(0);

export const setCartCount = (count) => {
  cartCount.value = Number(count || 0);
};

export const incrementCart = (qty = 1) => {
  cartCount.value = Number(cartCount.value || 0) + Number(qty || 1);
};

export const deductCart = (qty = 1) => {
  cartCount.value = Math.max(0, Number(cartCount.value || 0) - Number(qty || 1));
};

createInertiaApp({
  title: (title) => `${appName}`,
  resolve: (name) => {
    const page = resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    )

    // If page doesn’t already define a layout, apply GuestLayout
    return page.then((module) => {
      module.default.layout ??= GuestLayout
      return module
    })
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(plugin)
    app.component('Link', Link)
    app.use(ZiggyVue, Ziggy)

    // ✅ make cartCount available globally in all templates
    app.config.globalProperties.$cartCount = cartCount

    app.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
