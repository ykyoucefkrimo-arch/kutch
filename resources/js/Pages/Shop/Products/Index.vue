<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
});

const localFilters = ref({ ...props.filters });

function applyFilters() {
  router.get(route('products.index'), localFilters.value, { preserveState: true });
}
function resetFilters() {
  localFilters.value = {};
  router.get(route('products.index'));
}

onMounted(() => {
  // Sur mobile, toujours scroller vers les produits
  if (window.innerWidth < 1024) {
    setTimeout(() => {
      const el = document.getElementById('products-grid');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 150);
  }
});
</script>

<template>
  <ShopLayout>
    <Head title="Boutique" />

    <!-- Page header -->
    <div class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
        <h1 class="text-4xl sm:text-5xl font-bold uppercase tracking-tight text-black">Boutique</h1>
        <p class="text-sm text-neutral-400 mt-2 tracking-wide">{{ products.total }} produit(s)</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10">
      <div class="flex flex-col lg:flex-row gap-12">

        <!-- Sidebar Filters -->
        <aside class="lg:w-56 flex-shrink-0 space-y-8 order-2 lg:order-1">
          <!-- Search -->
          <div>
            <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black mb-3">Recherche</h3>
            <input v-model="localFilters.search" type="text" placeholder="Nom du produit..."
              class="w-full border-b border-neutral-300 pb-2 text-sm focus:outline-none focus:border-black bg-transparent placeholder-neutral-300 transition-colors" />
          </div>

          <!-- Category -->
          <div>
            <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black mb-3">Catégorie</h3>
            <div class="space-y-1.5">
              <button @click="localFilters.category = undefined"
                :class="['block w-full text-left text-xs tracking-wide transition-colors',
                  !localFilters.category ? 'text-black font-semibold' : 'text-neutral-400 hover:text-black']">
                Toutes
              </button>
              <button v-for="cat in categories" :key="cat.id"
                @click="localFilters.category = cat.slug"
                :class="['block w-full text-left text-xs tracking-wide transition-colors',
                  localFilters.category === cat.slug ? 'text-black font-semibold' : 'text-neutral-400 hover:text-black']">
                {{ cat.name }}
              </button>
            </div>
          </div>

          <!-- Price range -->
          <div>
            <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black mb-3">Prix (DA)</h3>
            <div class="flex gap-2 items-center">
              <input v-model="localFilters.min_price" type="number" placeholder="Min"
                class="w-full border-b border-neutral-300 pb-1.5 text-xs focus:outline-none focus:border-black bg-transparent placeholder-neutral-300" />
              <span class="text-neutral-300 text-xs">—</span>
              <input v-model="localFilters.max_price" type="number" placeholder="Max"
                class="w-full border-b border-neutral-300 pb-1.5 text-xs focus:outline-none focus:border-black bg-transparent placeholder-neutral-300" />
            </div>
          </div>

          <div class="flex flex-col gap-2 pt-2">
            <button @click="applyFilters"
              class="w-full bg-black text-white text-[10px] font-bold tracking-[0.2em] uppercase py-2.5 hover:bg-neutral-800 transition-colors">
              Appliquer
            </button>
            <button @click="resetFilters"
              class="w-full border border-neutral-300 text-neutral-600 text-[10px] font-bold tracking-[0.2em] uppercase py-2.5 hover:border-black hover:text-black transition-colors">
              Réinitialiser
            </button>
          </div>
        </aside>

        <!-- Products Grid -->
        <div id="products-grid" class="flex-1 order-1 lg:order-2">
          <!-- Sort bar -->
          <div class="flex items-center justify-between mb-8 pb-4 border-b border-neutral-200">
            <span class="text-[10px] tracking-widest uppercase text-neutral-400">
              {{ products.total }} résultat(s)
            </span>
            <select v-model="localFilters.sort" @change="applyFilters"
              class="text-[10px] tracking-widest uppercase border-b border-neutral-300 pb-1 focus:outline-none focus:border-black bg-transparent cursor-pointer">
              <option value="default">Tri par défaut</option>
              <option value="price_asc">Prix croissant</option>
              <option value="price_desc">Prix décroissant</option>
              <option value="newest">Nouveautés</option>
            </select>
          </div>

          <!-- No results -->
          <div v-if="products.data.length === 0" class="text-center py-20">
            <p class="text-neutral-300 text-sm tracking-widest uppercase mb-6">Aucun produit trouvé</p>
            <button @click="resetFilters"
              class="text-[10px] font-bold tracking-[0.2em] uppercase border border-black text-black px-8 py-3 hover:bg-black hover:text-white transition-colors">
              Réinitialiser les filtres
            </button>
          </div>

          <!-- Grid -->
          <div v-else class="grid grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-8">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
          </div>

          <!-- Pagination -->
          <div v-if="products.last_page > 1" class="flex justify-center gap-1 mt-12">
            <Link v-for="link in products.links" :key="link.label"
              :href="link.url || '#'"
              :class="[
                'px-3 py-1.5 text-[10px] font-bold tracking-wider uppercase border transition-colors',
                link.active ? 'bg-black border-black text-white' : 'border-neutral-200 text-neutral-500 hover:border-black hover:text-black',
                !link.url ? 'opacity-30 pointer-events-none' : ''
              ]"
              v-html="link.label" preserve-scroll preserve-state />
          </div>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>
