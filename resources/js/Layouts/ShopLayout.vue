<script setup>
import Navbar from '@/Components/Shop/Navbar.vue';
import Footer from '@/Components/Shop/Footer.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const error = computed(() => usePage().props.flash?.error);
</script>
<template>
  <div class="min-h-screen bg-stone-50 flex flex-col">
    <Navbar />
    <div v-if="error" class="bg-red-50 border-b border-red-200 px-6 py-3 text-center text-sm text-red-600">
      {{ error }}
    </div>
    <main class="flex-1">
      <slot />
    </main>
    <Footer />
  </div>
</template>
