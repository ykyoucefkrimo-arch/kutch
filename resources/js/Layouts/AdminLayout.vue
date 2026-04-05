<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const mobileOpen = ref(false);

const navItems = [
  {
    label: 'Tableau de bord', route: 'admin.dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  },
  {
    label: 'Commandes', route: 'admin.orders.index',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
  },
  {
    label: 'Produits', route: 'admin.products.index',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
  },
  {
    label: 'Catégories', route: 'admin.categories.index',
    icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'
  },
  {
    label: 'Antifraude', route: 'admin.antifraud.index',
    icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
  {
    label: 'Paramètres', route: 'admin.settings.index',
    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
  },
];
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar desktop -->
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden lg:flex flex-col">
      <div class="p-5 border-b border-gray-800">
        <div class="flex items-center gap-2 mb-1">
          <img src="/logo.png" alt="Kutch" class="h-6 w-auto brightness-0 invert opacity-80" />
          <h1 class="font-bold text-lg tracking-wider text-amber-400">KUTCH</h1>
        </div>
        <p class="text-xs text-gray-500 tracking-widest uppercase">Administration</p>
      </div>
      <nav class="flex-1 py-4 space-y-0.5 px-2 overflow-y-auto">
        <Link v-for="item in navItems" :key="item.route"
          :href="route(item.route)"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
            $page.url.startsWith('/admin/' + item.route.replace('admin.','').replace('.index','').replace('.dashboard',''))
              ? 'bg-amber-700 text-white'
              : 'text-gray-300 hover:bg-gray-800 hover:text-white'
          ]">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
          </svg>
          {{ item.label }}
        </Link>
      </nav>
      <div class="p-4 border-t border-gray-800 space-y-2">
        <a href="/" target="_blank" class="flex items-center gap-2 text-xs text-gray-400 hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Voir la boutique
        </a>
        <Link :href="route('logout')" method="post" as="button"
          class="flex items-center gap-2 text-xs text-red-400 hover:text-red-300 transition-colors w-full">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Déconnexion
        </Link>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Topbar -->
      <header class="bg-white shadow-sm h-14 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
        <!-- Mobile menu button -->
        <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <slot name="header">
          <h2 class="font-semibold text-gray-700">Admin</h2>
        </slot>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-500 hidden sm:block">{{ $page.props.auth?.user?.name }}</span>
        </div>
      </header>

      <!-- Mobile nav -->
      <div v-if="mobileOpen" class="lg:hidden bg-gray-900 text-white px-3 py-3 space-y-0.5">
        <Link v-for="item in navItems" :key="item.route" :href="route(item.route)"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white"
          @click="mobileOpen = false">
          {{ item.label }}
        </Link>
      </div>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success"
        class="bg-green-50 border-b border-green-200 px-6 py-3 text-sm text-green-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ $page.props.flash.success }}
      </div>

      <main class="flex-1 p-4 lg:p-6 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
