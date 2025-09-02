<template>
    <div class="layout-wrapper">
        <!-- Header -->
        <header class="fixed-header" role="banner">
            <div class="container">
                <!-- Logo / Brand -->
                <Link :href="route('catalogue')" class="brand">
                    {{ appName }}
                </Link>

                <!-- Search (only on Catalogue page) -->
                <form v-if="$page.component === 'Catalogue'" class="search" @submit.prevent="onSearch">
                    <input type="search" v-model="q" placeholder="Search products..." aria-label="Search" />
                </form>
                
                  <slot v-if="$page.component !== 'Catalogue'" name="header" />

                <!-- Right side -->
<nav class="right">
    <!-- Cart -->
    <Link :href="route('cart.show')" class="cart" aria-label="Cart">
        🛒
        <span v-if="cartCount > 0" class="badge bg-danger">{{ cartCount }}</span>
    </Link>

    <!-- Auth -->
    <template v-if="!auth">
        <Link :href="route('login')" class="btn btn-link">Login</Link>
        <Link :href="route('register')" class="btn btn-link">Register</Link>
    </template>

    <template v-else>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <!-- Bootstrap Dropdown -->
                <button
                    class="btn btn-link nav-link dropdown-toggle p-0"
                    type="button"
                    id="userDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    {{ auth.name }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
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
            :class="$page.component === 'Catalogue'
                ? 'p-4 pt-20 main-content d-flex flex-column min-vh-100 mt-5' 
                : 'main-content pt-20 align-items-center justify-content-center d-flex flex-column min-vh-100'"
        >
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

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

/* Main content padding to avoid being hidden behind fixed header */
.main-content {
    padding-top: 64px;
}
.nav-item.dropdown {
  position: relative; /* anchor point for absolute dropdown */
}

.nav-item.dropdown .dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  min-width: 160px;
  border-radius: 8px;
  border: 1px solid #e6e6e6;
  background: #fff;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
  z-index:3000;
}
</style>
