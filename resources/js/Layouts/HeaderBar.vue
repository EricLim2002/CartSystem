<!-- resources/js/Layouts/HeaderBar.vue -->
<template>
  <header class="fixed-header" role="banner">
    <div class="container">
      <!-- Logo / Brand -->
      <Link :href="route('catalogue')" class="brand">
        {{ appName }}
      </Link>

      <!-- Search -->
      <form class="search" @submit.prevent="onSearch">
        <input
          type="search"
          v-model="q"
          placeholder="Search products..."
          aria-label="Search"
        />
      </form>

      <!-- Right side -->
      <nav class="right">
        <!-- Cart -->
        <Link :href="route('cart.show')" class="cart" aria-label="Cart">
          🛒
          <span v-if="cartCount > 0" class="badge">{{ cartCount }}</span>
        </Link>

        <!-- Auth -->
        <template v-if="!auth">
          <Link :href="route('login')">Login</Link>
          <Link :href="route('register')">Register</Link>
        </template>
        <template v-else>
          <Link :href="route('dashboard')">Dashboard</Link>
          <Link :href="route('logout')" method="post" as="button">Logout</Link>
        </template>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import {route} from "ziggy-js";

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
.fixed-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: #fff;
  border-bottom: 1px solid #eee;
  z-index: 1000;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 16px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  font-weight: 700;
  text-decoration: none;
  color: #111;
}

.search input {
  padding: 6px 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.badge {
  display: inline-block;
  background: #ef4444;
  color: #fff;
  border-radius: 999px;
  padding: 2px 6px;
  font-size: 12px;
  margin-left: 6px;
}
</style>
