<template>
  <div class="layout-wrapper">
    <!-- Header -->
    <header class="fixed-top bg-white border-bottom shadow-sm" role="banner">
      <div class="container d-flex align-items-center justify-content-between py-2">
        <!-- Logo / Brand -->
        <Link :href="route('catalogue')" class="fw-bold fs-5 text-decoration-none text-dark">
          {{ appName }}
        </Link>

        <!-- Search (only on Catalogue page) -->
        <form
          v-if="$page.component === 'Catalogue'"
          class="d-none d-md-block ms-3 flex-grow-1 mx-5"
          @submit.prevent="onSearch"
        >
          <input
            type="search"
            v-model="q"
            class="form-control"
            placeholder="Search products..."
            aria-label="Search"
          />
        </form>

        <!-- Right side -->
        <nav class="d-flex align-items-center gap-3">
          <!-- Cart -->
          <Link :href="route('cart.show')" class="position-relative text-dark fs-5" aria-label="Cart">
            🛒
            <span
              v-if="cartCount > 0"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            >
              {{ cartCount }}
            </span>
          </Link>

          <!-- Auth -->
          <template v-if="!auth">
            <Link :href="route('login')" class="btn btn-outline-primary btn-sm">Login</Link>
            <Link :href="route('register')" class="btn btn-primary btn-sm">Register</Link>
          </template>

          <template v-else>
            <ul class="navbar-nav">
              <li class="nav-item dropdown">
                <button
                  class="btn btn-link nav-link dropdown-toggle text-dark"
                  type="button"
                  id="userDropdown"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  {{ auth.name }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                  <li>
                    <DropdownLink :href="route('profile.edit')" class="dropdown-item">
                      Profile
                    </DropdownLink>
                  </li>
                  <li>
                    <DropdownLink :href="route('orders.index')" class="dropdown-item">
                      Orders
                    </DropdownLink>
                  </li>
                  <li>
                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-item">
                      Log Out
                    </DropdownLink>
                  </li>
                </ul>
              </li>
            </ul>
          </template>
        </nav>
      </div>
    </header>

    <!-- Main content -->
    <main
      :class="[
        'main-content container',
        $page.component === 'Catalogue'
          ? 'pt-5 mt-5'
          : 'd-flex flex-column align-items-center justify-content-center pt-5 mt-5 min-vh-100'
      ]"
    >
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import DropdownLink from "@/Components/DropdownLink.vue";

const page = usePage();

// Search query
const q = ref(page.props.query?.q || "");

// Props shared from Laravel
const appName = computed(() => page.props.app?.name ?? "MyShop");
const cartCount = computed(() => page.props.cart?.count ?? 0);
const auth = computed(() => page.props.auth?.user ?? null);

// Handle search with Inertia router
function onSearch() {
  router.get(route("catalogue"), { q: q.value }, { preserveState: true });
}
</script>

<style scoped>
/* Keep only essentials */
.main-content {
  padding-top: 64px; /* prevent content from hiding behind header */
}
</style>
