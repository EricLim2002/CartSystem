import './bootstrap'
import '../css/app.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp, h , ref, computed} from 'vue'
import { createInertiaApp, Link, router, usePage } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import GuestLayout from './Layouts/GuestLayout.vue'
import { Ziggy } from './ziggy'
export { Ziggy };
import { ZiggyVue } from 'ziggy-js';
import {route} from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

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
    app.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
