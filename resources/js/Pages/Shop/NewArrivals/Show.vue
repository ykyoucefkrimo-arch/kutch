<script setup>
import { ref } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useCart } from '@/Composables/useCart';
import { useStorage } from '@/Composables/useStorage';

const props = defineProps({
  newArrival: Object,
});

const { addItem, formatPrice } = useCart();
const { storageUrl } = useStorage();
const qty = ref(1);
const activeImage = ref(props.newArrival.main_image);
const added = ref(false);

function addToCart() {
  addItem(props.newArrival, qty.value, null, 'new_arrival');
  added.value = true;
  setTimeout(() => added.value = false, 2000);
}
</script>

<template>
  <ShopLayout>
    <Head :title="newArrival.name" />

    <!-- Breadcrumb -->
    <div class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-4 flex items-center gap-2 text-[10px] tracking-widest uppercase text-neutral-400">
        <a :href="route('home')" class="hover:text-black transition-colors">Accueil</a>
        <span>·</span>
        <span class="text-black">{{ newArrival.name }}</span>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">

        <!-- Images -->
        <div>
          <div class="aspect-square bg-neutral-100 overflow-hidden mb-3">
            <img v-if="activeImage" :src="storageUrl(activeImage)" :alt="newArrival.name"
              class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center bg-neutral-100">
              <svg class="w-20 h-20 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <!-- Thumbnails -->
          <div v-if="newArrival.images && newArrival.images.length > 0" class="flex gap-2 flex-wrap">
            <button v-if="newArrival.main_image" @click="activeImage = newArrival.main_image"
              :class="['w-16 h-16 overflow-hidden border-2 transition-colors',
                activeImage === newArrival.main_image ? 'border-black' : 'border-transparent hover:border-neutral-300']">
              <img :src="storageUrl(newArrival.main_image)" class="w-full h-full object-cover" />
            </button>
            <button v-for="img in newArrival.images" :key="img" @click="activeImage = img"
              :class="['w-16 h-16 overflow-hidden border-2 transition-colors',
                activeImage === img ? 'border-black' : 'border-transparent hover:border-neutral-300']">
              <img :src="storageUrl(img)" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Info -->
        <div>
          <p v-if="newArrival.category"
            class="text-[10px] tracking-[0.2em] uppercase text-neutral-400 mb-3">
            {{ newArrival.category.name }}
          </p>
          <h1 class="text-3xl sm:text-4xl font-bold text-black leading-tight mb-6 uppercase tracking-tight">
            {{ newArrival.name }}
          </h1>

          <!-- Price -->
          <div class="flex items-baseline gap-4 mb-8 pb-8 border-b border-neutral-200">
            <span class="text-2xl font-bold text-black">
              {{ formatPrice(newArrival.price_promo ?? newArrival.price) }}
            </span>
            <span v-if="newArrival.price_promo" class="text-base text-neutral-400 line-through">
              {{ formatPrice(newArrival.price) }}
            </span>
            <span v-if="newArrival.price_promo"
              class="text-[10px] font-bold tracking-widest uppercase bg-black text-white px-2 py-0.5">
              Promo
            </span>
          </div>

          <!-- Short desc -->
          <p v-if="newArrival.short_description" class="text-sm text-neutral-600 mb-8 leading-relaxed">
            {{ newArrival.short_description }}
          </p>

          <!-- Specs grid -->
          <div class="grid grid-cols-2 gap-px bg-neutral-200 mb-8">
            <div v-if="newArrival.material" class="bg-white p-4">
              <p class="text-[10px] tracking-widest uppercase text-neutral-400 mb-1">Matière</p>
              <p class="text-sm font-medium text-black">{{ newArrival.material }}</p>
            </div>
            <div v-if="newArrival.color" class="bg-white p-4">
              <p class="text-[10px] tracking-widest uppercase text-neutral-400 mb-1">Couleur</p>
              <p class="text-sm font-medium text-black">{{ newArrival.color }}</p>
            </div>
            <div v-if="newArrival.delivery_days" class="bg-white p-4">
              <p class="text-[10px] tracking-widest uppercase text-neutral-400 mb-1">Livraison</p>
              <p class="text-sm font-medium text-black">{{ newArrival.delivery_days }} jours</p>
            </div>
            <div class="bg-white p-4">
              <p class="text-[10px] tracking-widest uppercase text-neutral-400 mb-1">Disponibilité</p>
              <p :class="['text-sm font-medium', newArrival.in_stock ? 'text-black' : 'text-neutral-400']">
                {{ newArrival.in_stock ? 'En stock' : 'Sur commande' }}
              </p>
            </div>
          </div>

          <!-- Add to cart -->
          <div v-if="!newArrival.is_custom" class="space-y-4">
            <div class="flex items-center gap-0 border border-neutral-200 w-fit">
              <button @click="qty = Math.max(1, qty - 1)"
                class="w-10 h-10 flex items-center justify-center text-neutral-600 hover:text-black hover:bg-neutral-50 text-lg font-light transition-colors border-r border-neutral-200">
                −
              </button>
              <span class="w-12 text-center text-sm font-medium">{{ qty }}</span>
              <button @click="qty++"
                class="w-10 h-10 flex items-center justify-center text-neutral-600 hover:text-black hover:bg-neutral-50 text-lg font-light transition-colors border-l border-neutral-200">
                +
              </button>
            </div>
            <button @click="addToCart" :class="[
              'w-full py-4 text-[11px] font-bold tracking-[0.2em] uppercase transition-all',
              added ? 'bg-neutral-800 text-white' : 'bg-black text-white hover:bg-neutral-800'
            ]">
              {{ added ? '✓ Ajouté au panier' : 'Ajouter au panier' }}
            </button>
          </div>

          <!-- Custom product -->
          <div v-else class="border border-neutral-200 p-6">
            <p class="text-[10px] tracking-[0.2em] uppercase font-bold text-black mb-2">Sur mesure</p>
            <p class="text-sm text-neutral-500 mb-4 leading-relaxed">
              Cet article est fabriqué sur commande selon vos dimensions et préférences.
            </p>
            <a href="#contact"
              class="block w-full text-center bg-black text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 hover:bg-neutral-800 transition-colors">
              Demander un devis
            </a>
          </div>

          <!-- Payment note -->
          <div class="mt-4 flex items-center gap-2 text-[10px] tracking-wider text-neutral-400 uppercase">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Paiement à la livraison (Cash on delivery)
          </div>
        </div>
      </div>

      <!-- Description -->
      <div v-if="newArrival.description" class="mt-16 pt-12 border-t border-neutral-200">
        <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black mb-6">Description</h2>
        <div class="prose max-w-none text-sm text-neutral-600 leading-relaxed" v-html="newArrival.description" />
      </div>
    </div>
  </ShopLayout>
</template>
