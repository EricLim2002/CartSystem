import './bootstrap'
import '../css/app.css'

import { createApp, h , ref, computed} from 'vue'
import { createInertiaApp, Link, router, usePage } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import DefaultLayout from './Layouts/DefaultLayout.vue'
import {route} from 'ziggy-js'
import { Ziggy } from './ziggy'
export { Ziggy };
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    const page = resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    )

    // If page doesn’t already define a layout, apply DefaultLayout
    return page.then((module) => {
      module.default.layout ??= DefaultLayout
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
