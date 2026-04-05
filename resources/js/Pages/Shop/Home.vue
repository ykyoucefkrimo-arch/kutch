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

    <!-- Hero -->
    <section class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 sm:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <p class="text-[10px] tracking-[0.28em] uppercase text-neutral-400 mb-6">
              Artisanat — Bois — Sur mesure
            </p>
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-black leading-[1.05] uppercase tracking-tight">
              Meubles<br/>
              <span class="text-neutral-300">sur mesure</span><br/>
              artisanaux
            </h1>
            <p class="mt-8 text-sm text-neutral-500 leading-relaxed max-w-md">
              Découvrez notre collection de meubles fabriqués avec passion par nos artisans.
              Qualité, durabilité et esthétique — livrés dans les 58 wilayas d'Algérie.
            </p>
            <div class="flex flex-wrap gap-4 mt-10">
              <Link :href="route('products.index')"
                class="inline-flex items-center gap-3 bg-black text-white text-[11px] font-bold tracking-[0.18em] uppercase px-8 py-4 hover:bg-neutral-800 transition-colors">
                Voir le catalogue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </Link>
              <a href="#contact"
                class="inline-flex items-center gap-3 border border-neutral-300 text-black text-[11px] font-bold tracking-[0.18em] uppercase px-8 py-4 hover:border-black transition-colors">
                Nous contacter
              </a>
            </div>
          </div>

          <!-- Hero : grand logo Kutch en filigrane -->
          <div class="hidden lg:flex items-center justify-center bg-neutral-50 aspect-square">
            <img src="/logo.png" alt="Kutch" class="w-56 h-56 object-contain opacity-10" />
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section v-if="categories && categories.length" class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14">
        <div class="flex items-center mb-10">
          <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black whitespace-nowrap">Catégories</h2>
          <div class="h-px flex-1 bg-neutral-200 ml-8"></div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-px bg-neutral-200">
          <Link v-for="cat in categories" :key="cat.id"
            :href="route('products.index', { category: cat.slug })"
            class="bg-white px-4 py-6 text-center hover:bg-neutral-50 transition-colors group">
            <span class="block text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-500 group-hover:text-black transition-colors">
              {{ cat.name }}
            </span>
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section v-if="featuredProducts && featuredProducts.length" class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14">
        <div class="flex items-center justify-between mb-10">
          <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Sélection</h2>
          <div class="h-px flex-1 bg-neutral-200 mx-8"></div>
          <Link :href="route('products.index')"
            class="text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-400 hover:text-black transition-colors whitespace-nowrap">
            Tout voir →
          </Link>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <!-- New Products -->
    <section v-if="newProducts && newProducts.length" class="bg-neutral-50 border-b border-neutral-200">
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

    <!-- CTA "Plus de produits" style Kutch -->
    <section class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14 flex justify-center">
        <Link :href="route('products.index')"
          class="inline-block border border-black text-black text-[11px] font-bold tracking-[0.3em] uppercase px-16 py-4 hover:bg-black hover:text-white transition-colors">
          Plus de produits
        </Link>
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
