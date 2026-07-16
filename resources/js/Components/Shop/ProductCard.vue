<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';

const props = defineProps({
  product: { type: Object, required: true }
});

const page = usePage();
const storageUrl = (path) => `${page.props.storageUrl}/${path}`;
const { addItem, formatPrice } = useCart();

const flying = ref(false);
const flyStyle = ref({});

function addToCart(event) {
  const btn = event.currentTarget;
  const btnRect = btn.getBoundingClientRect();
  const cartIcon = document.querySelector('[data-cart-icon]');
  if (!cartIcon) { addItem(props.product); return; }
  const cartRect = cartIcon.getBoundingClientRect();

  const startX = btnRect.left + btnRect.width / 2;
  const startY = btnRect.top + btnRect.height / 2;
  const endX   = cartRect.left + cartRect.width / 2;
  const endY   = cartRect.top  + cartRect.height / 2;

  flyStyle.value = {
    position: 'fixed',
    left: startX - 20 + 'px',
    top:  startY - 20 + 'px',
    '--dx': (endX - startX) + 'px',
    '--dy': (endY - startY) + 'px',
    zIndex: 9999,
    pointerEvents: 'none',
  };

  flying.value = true;
  addItem(props.product);
  setTimeout(() => { flying.value = false; }, 700);
}
</script>

<template>
  <div class="group flex flex-col bg-[#f5f5f3] cursor-pointer">

    <!-- Flying dot -->
    <Teleport to="body">
      <div v-if="flying" :style="flyStyle" class="fly-dot">
        <div class="w-10 h-10 rounded-full bg-black border-2 border-white shadow-lg overflow-hidden">
          <img v-if="product.main_image" :src="storageUrl(product.main_image)" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full bg-neutral-800"></div>
        </div>
      </div>
    </Teleport>

    <!-- Zone image -->
    <Link :href="route('products.show', product.slug)" class="relative block overflow-hidden">

      <!-- Badges -->
      <div class="absolute top-3 left-3 z-10 flex flex-col gap-1">
        <span v-if="product.price_promo"
              class="text-[10px] font-semibold px-2 py-0.5 rounded-sm bg-[#fdecea] text-[#c0392b] tracking-wide">
          Promo
        </span>
        <span v-if="product.is_custom"
              class="text-[10px] font-semibold px-2 py-0.5 rounded-sm bg-neutral-200 text-neutral-600 tracking-wide">
          Sur mesure
        </span>
      </div>

      <!-- Image centrée avec padding -->
      <div class="aspect-square flex items-center justify-center p-8">
        <img v-if="product.main_image"
             :src="storageUrl(product.main_image)"
             :alt="product.name"
             class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
        <svg v-else class="w-16 h-16 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
    </Link>

    <!-- Info -->
    <div class="px-4 pb-5 pt-3 flex flex-col flex-1">
      <!-- Nom -->
      <Link :href="route('products.show', product.slug)">
        <h3 class="text-sm font-bold text-black leading-snug hover:text-neutral-500 transition-colors">
          {{ product.name }}
        </h3>
      </Link>

      <!-- Catégorie -->
      <p v-if="product.category" class="text-[11px] text-neutral-400 mt-1 tracking-wide">
        {{ product.category.name }}
      </p>

      <!-- Prix -->
      <div class="mt-3">
        <span class="text-sm font-bold text-black">
          {{ formatPrice(product.price_promo ?? product.price) }}
        </span>
        <span v-if="product.price_promo" class="text-xs text-neutral-400 line-through ml-2 font-normal">
          {{ formatPrice(product.price) }}
        </span>
      </div>

      <!-- Bouton panier — glisse au hover -->
      <div class="mt-3 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 flex justify-end">
        <button v-if="!product.is_custom" @click="addToCart"
                class="w-9 h-9 flex items-center justify-center border border-black text-black hover:bg-black hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
        </button>
        <Link v-else :href="route('products.show', product.slug)"
              class="w-9 h-9 flex items-center justify-center border border-neutral-400 text-neutral-500 hover:border-black hover:text-black transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
          </svg>
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fly-dot {
  animation: flyToCart 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}
@keyframes flyToCart {
  0%   { transform: translate(0, 0) scale(1); opacity: 1; }
  80%  { opacity: 1; }
  100% { transform: translate(var(--dx), var(--dy)) scale(0.2); opacity: 0; }
}
</style>
