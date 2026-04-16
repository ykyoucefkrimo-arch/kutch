<script setup>
import { Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';
import { useCart } from '@/Composables/useCart';

defineProps({
  featuredProducts: Array,
  categories: Array,
  newProducts: Array,
});

const { formatPrice } = useCart();
</script>

<template>
  <ShopLayout>
    <Head title="Accueil" />


    <!-- New Products -->
    <section v-if="newProducts && newProducts.length" class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14">
        <div class="flex items-center mb-10">
          <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Nouveautés</h2>
          <div class="h-px flex-1 bg-neutral-300 ml-8"></div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <ProductCard v-for="product in newProducts" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section v-if="categories && categories.length" class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
        <div class="flex flex-wrap gap-4 justify-center">
          <Link v-for="cat in categories" :key="cat.id"
            :href="route('products.index', { category: cat.slug })"
            class="text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-500 hover:text-black transition-colors">
            {{ cat.name }}
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section v-if="featuredProducts && featuredProducts.data && featuredProducts.data.length" class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14">
        <div class="flex items-center justify-between mb-10">
          <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Produits</h2>
          <div class="h-px flex-1 bg-neutral-200 mx-8"></div>
          <Link :href="route('products.index')"
            class="text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-400 hover:text-black transition-colors whitespace-nowrap">
            Tout voir →
          </Link>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <ProductCard v-for="product in featuredProducts.data" :key="product.id" :product="product" />
        </div>

        <!-- Pagination -->
        <div v-if="featuredProducts.last_page > 1" class="flex justify-center gap-2 mt-12">
          <Link
            v-for="link in featuredProducts.links"
            :key="link.label"
            :href="link.url ?? '#'"
            v-html="link.label"
            class="px-4 py-2 text-xs font-bold uppercase tracking-widest border transition-colors"
            :class="link.active
              ? 'bg-black text-white border-black'
              : link.url
                ? 'border-neutral-300 text-neutral-600 hover:border-black hover:text-black'
                : 'border-neutral-200 text-neutral-300 cursor-default pointer-events-none'"
          />
        </div>
      </div>
    </section>

    <!-- Pourquoi nous — section éditoriale avec logo Kutch en accent -->
    <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 items-start">

        <!-- Logo mark décoratif -->
        <div class="flex justify-center lg:justify-start">
          <img src="/logo.png" alt="" class="w-20 h-20 object-contain opacity-10" />
        </div>

        <!-- Points forts -->
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-10">
          <div v-for="item in [
            { num: '01', title: 'Bois de qualité', desc: 'Sélection rigoureuse des essences de bois pour une durabilité maximale.' },
            { num: '02', title: 'Artisanat local', desc: 'Chaque meuble est fabriqué à la main par nos artisans algériens.' },
            { num: '03', title: 'Livraison nationale', desc: 'Paiement à la livraison, dans les 58 wilayas d\'Algérie.' },
          ]" :key="item.num">
            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-neutral-300 mb-4">{{ item.num }}</p>
            <h3 class="text-sm font-bold text-black uppercase tracking-wide mb-3">{{ item.title }}</h3>
            <p class="text-sm text-neutral-500 leading-relaxed">{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </ShopLayout>
</template>
