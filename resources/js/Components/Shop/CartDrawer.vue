<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';

defineProps({ open: Boolean });
defineEmits(['close']);

const { items, total, removeItem, updateQty, formatPrice } = useCart();
const isEmpty = computed(() => items.value.length === 0);
</script>

<template>
  <!-- Overlay -->
  <Transition name="fade">
    <div v-if="open" class="fixed inset-0 bg-black/40 z-50" @click="$emit('close')" />
  </Transition>

  <!-- Panel -->
  <Transition name="slide-right">
    <div v-if="open" class="fixed right-0 top-0 h-full w-full max-w-sm bg-white z-50 flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-200">
        <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Panier</h2>
        <button @click="$emit('close')" class="text-neutral-400 hover:text-black transition-colors p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Empty -->
      <div v-if="isEmpty" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
        <svg class="w-12 h-12 text-neutral-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <p class="text-xs tracking-widest uppercase text-neutral-400 mb-4">Votre panier est vide</p>
        <button @click="$emit('close')"
          class="text-[10px] font-bold tracking-widest uppercase border border-black text-black px-4 py-2 hover:bg-black hover:text-white transition-colors">
          Continuer les achats
        </button>
      </div>

      <!-- Items -->
      <div v-else class="flex-1 overflow-y-auto divide-y divide-neutral-100">
        <div v-for="item in items" :key="item.product_id"
          class="flex items-start gap-4 px-6 py-4">
          <div class="w-16 h-16 bg-neutral-100 flex-shrink-0 overflow-hidden">
            <img v-if="item.main_image" :src="`/storage/${item.main_image}`"
              :alt="item.name" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-black truncate leading-snug">{{ item.name }}</p>
            <p class="text-xs text-neutral-500 mt-0.5">{{ formatPrice(item.unit_price) }}</p>
            <div class="flex items-center gap-3 mt-2">
              <button @click="updateQty(item.product_id, item.quantity - 1)"
                class="w-6 h-6 border border-neutral-300 flex items-center justify-center text-neutral-600 hover:border-black hover:text-black transition-colors text-xs font-bold">
                −
              </button>
              <span class="text-xs font-medium w-4 text-center">{{ item.quantity }}</span>
              <button @click="updateQty(item.product_id, item.quantity + 1)"
                class="w-6 h-6 border border-neutral-300 flex items-center justify-center text-neutral-600 hover:border-black hover:text-black transition-colors text-xs font-bold">
                +
              </button>
            </div>
          </div>
          <button @click="removeItem(item.product_id)" class="text-neutral-300 hover:text-black transition-colors mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="!isEmpty" class="px-6 py-5 border-t border-neutral-200">
        <div class="flex justify-between items-center mb-4">
          <span class="text-[10px] tracking-widest uppercase text-neutral-500">Total</span>
          <span class="text-base font-bold text-black">{{ formatPrice(total) }}</span>
        </div>
        <Link :href="route('checkout')" @click="$emit('close')"
          class="block w-full text-center bg-black text-white text-[11px] font-bold tracking-[0.18em] uppercase py-3.5 hover:bg-neutral-800 transition-colors">
          Commander — Paiement à la livraison
        </Link>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-right-enter-active, .slide-right-leave-active { transition: transform 0.3s ease; }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }
</style>
