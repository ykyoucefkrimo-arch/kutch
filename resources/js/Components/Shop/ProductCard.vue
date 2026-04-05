<script setup>
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';

const props = defineProps({
  product: { type: Object, required: true }
});

const { addItem, formatPrice } = useCart();

function addToCart() {
  addItem(props.product);
}
</script>

<template>
  <div class="group bg-white overflow-hidden">
    <!-- Image -->
    <Link :href="route('products.show', product.slug)" class="block">
      <div class="relative w-full overflow-hidden bg-neutral-100" style="padding-top: 100%;">
        <img v-if="product.main_image"
          :src="`/storage/${product.main_image}`"
          :alt="product.name"
          class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        <div v-else class="absolute inset-0 flex items-center justify-center bg-neutral-100">
          <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-1">
          <span v-if="product.price_promo"
            class="bg-black text-white text-[10px] font-bold px-2 py-0.5 tracking-widest uppercase">Promo</span>
          <span v-if="product.is_custom"
            class="bg-neutral-700 text-white text-[10px] font-bold px-2 py-0.5 tracking-widest uppercase">Sur mesure</span>
        </div>
      </div>
    </Link>

    <!-- Info -->
    <div class="pt-2 pb-3 sm:pt-3 sm:pb-4">
      <p v-if="product.category" class="text-[10px] tracking-[0.18em] uppercase text-neutral-400 mb-1">
        {{ product.category.name }}
      </p>
      <Link :href="route('products.show', product.slug)">
        <h3 class="text-sm font-medium text-black hover:text-neutral-600 transition-colors leading-snug mb-2">
          {{ product.name }}
        </h3>
      </Link>

      <div class="flex items-center justify-between">
        <div>
          <span class="text-sm font-bold text-black">
            {{ formatPrice(product.price_promo ?? product.price) }}
          </span>
          <span v-if="product.price_promo" class="text-xs text-neutral-400 line-through ml-2">
            {{ formatPrice(product.price) }}
          </span>
        </div>
        <button v-if="!product.is_custom" @click="addToCart"
          class="text-[10px] font-bold tracking-widest uppercase border border-black text-black px-3 py-1.5 hover:bg-black hover:text-white transition-colors">
          + Panier
        </button>
        <Link v-else :href="route('products.show', product.slug)"
          class="text-[10px] font-bold tracking-widest uppercase border border-neutral-400 text-neutral-600 px-3 py-1.5 hover:border-black hover:text-black transition-colors">
          Devis
        </Link>
      </div>
    </div>
  </div>
</template>
