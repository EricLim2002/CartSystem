<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
  <div>
    <div class="min-vh-100 bg-light">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
          <!-- Logo -->
          <Link :href="route('dashboard')" class="navbar-brand d-flex align-items-center">
            <ApplicationLogo class="img-fluid" style="height: 36px; width: auto;" />
          </Link>

          <!-- Hamburger -->
          <button
            class="navbar-toggler"
            type="button"
            @click="showingNavigationDropdown = !showingNavigationDropdown"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Navbar links -->
          <div :class="['collapse navbar-collapse', showingNavigationDropdown ? 'show' : '']">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <NavLink
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                  class="nav-link"
                >
                  Dashboard
                </NavLink>
              </li>
            </ul>

            <!-- User dropdown -->
            <ul class="navbar-nav ms-auto">
              <li class="nav-item dropdown">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <a
                      href="#"
                      class="nav-link dropdown-toggle"
                      role="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false"
                    >
                      {{ $page.props.auth.user.name }}
                    </a>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                  </template>
                </Dropdown>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-white shadow-sm py-3 mb-4">
        <div class="container-fluid">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="container-fluid">
        <slot />
      </main>
    </div>
  </div>
</template>
