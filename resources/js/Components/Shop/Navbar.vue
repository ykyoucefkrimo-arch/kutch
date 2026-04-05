<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import CartDrawer from './CartDrawer.vue';

const { count } = useCart();
const mobileOpen = ref(false);
const cartOpen = ref(false);
</script>

<template>
  <nav class="bg-white sticky top-0 z-50 border-b border-neutral-200">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
      <div class="flex items-center justify-between h-16">

        <!-- Logo Kutch -->
        <Link :href="route('home')" class="flex items-center gap-3 group">
          <img src="/logo.png" alt="Kutch" class="h-8 w-auto" />
          <span class="text-base font-bold tracking-[0.22em] text-black uppercase">Kutch</span>
        </Link>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-10">
          <Link :href="route('home')"
            class="text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-500 hover:text-black transition-colors">
            Accueil
          </Link>
          <Link :href="route('products.index')"
            class="text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-500 hover:text-black transition-colors">
            Catalogue
          </Link>
          <a href="#contact"
            class="text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-500 hover:text-black transition-colors">
            Contact
          </a>
        </div>

        <!-- Cart + Mobile button -->
        <div class="flex items-center gap-3">
          <button @click="cartOpen = true" class="relative p-2 text-neutral-600 hover:text-black transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span v-if="count > 0"
              class="absolute -top-0.5 -right-0.5 bg-black text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center font-bold">
              {{ count }}
            </span>
          </button>
          <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-neutral-600 hover:text-black">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileOpen" class="md:hidden border-t border-neutral-200 bg-white px-6 py-4 space-y-4">
      <Link :href="route('home')"
        class="block text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-600 hover:text-black">
        Accueil
      </Link>
      <Link :href="route('products.index')"
        class="block text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-600 hover:text-black">
        Catalogue
      </Link>
      <a href="#contact"
        class="block text-[11px] font-semibold tracking-[0.18em] uppercase text-neutral-600 hover:text-black">
        Contact
      </a>
    </div>
  </nav>

  <CartDrawer :open="cartOpen" @close="cartOpen = false" />
</template>
