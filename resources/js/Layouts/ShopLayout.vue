<script setup>
import Footer from '@/Components/Shop/Footer.vue';
import CartDrawer from '@/Components/Shop/CartDrawer.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const error = computed(() => usePage().props.flash?.error);

// Le panier était géré par l'ancien <Navbar/> Vue (retiré ci-dessous). Le
// nouveau header (resources/views/partials/header.blade.php, Blade+Alpine)
// déclenche `window.dispatchEvent(new CustomEvent('open-cart'))` sur le clic
// panier — on l'écoute ici pour piloter le même <CartDrawer>.
const cartOpen = ref(false);
const openCart = () => { cartOpen.value = true; };
onMounted(() => window.addEventListener('open-cart', openCart));
onUnmounted(() => window.removeEventListener('open-cart', openCart));
</script>
<template>
  <div class="min-h-screen bg-white flex flex-col">
    <!-- Header global : resources/views/partials/header.blade.php (Blade+Alpine),
         inclus par app.blade.php sur toutes les pages sauf /admin. L'ancien
         composant Vue <Navbar /> a été retiré ici pour éviter le doublon —
         les deux avaient la même hauteur (spacer conservé tel quel ci-dessous). -->
    <!-- Spacer pour la navbar fixe — sauf si la page a un hero plein écran -->
    <div class="h-[72px] xl:h-[80px] shrink-0" /><!-- h-10+py-4 / xl:h-12+py-4 -->
    <div v-if="error" class="bg-red-50 border-b border-red-200 px-6 py-3 text-center text-sm text-red-600">
      {{ error }}
    </div>
    <main class="flex-1">
      <slot />
    </main>
    <Footer />
    <CartDrawer :open="cartOpen" @close="cartOpen = false" />
  </div>
</template>
