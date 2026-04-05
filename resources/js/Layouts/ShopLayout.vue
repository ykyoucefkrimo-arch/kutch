<script setup>
import Navbar from '@/Components/Shop/Navbar.vue';
import Footer from '@/Components/Shop/Footer.vue';
</script>
<template>
  <div class="min-h-screen bg-stone-50 flex flex-col">
    <Navbar />
    <main class="flex-1">
      <slot />
    </main>
    <Footer />
  </div>
</template>
