<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';

const props = defineProps({
  products:   Object,
  categories: Array,
  heroSlides: Array,
  filters:    Object,
});

// ── Hero slider ───────────────────────────────────────────────
const currentSlide = ref(0);
let autoplayTimer  = null;
const totalSlides  = () => props.heroSlides?.length ?? 0;

function goTo(index) { currentSlide.value = (index + totalSlides()) % totalSlides(); resetAutoplay(); }
function prev() { goTo(currentSlide.value - 1); }
function next() { goTo(currentSlide.value + 1); }
function resetAutoplay() {
  clearInterval(autoplayTimer);
  if (totalSlides() > 1) autoplayTimer = setInterval(() => { currentSlide.value = (currentSlide.value + 1) % totalSlides(); }, 5000);
}
onMounted(() => resetAutoplay());
onUnmounted(() => clearInterval(autoplayTimer));

const localFilters  = ref({ ...props.filters });
const showAllFilters = ref(false);

function setCategory(slug) {
  localFilters.value.category = slug || undefined;
  apply();
}

function apply() {
  router.get(route('products.index'), localFilters.value, { preserveState: true, replace: true });
}

function reset() {
  localFilters.value = {};
  router.get(route('products.index'));
}

const activeCategory = computed(() =>
  props.categories?.find(c => c.slug === localFilters.value.category)
);

const hasFilters = computed(() =>
  localFilters.value.category ||
  localFilters.value.search ||
  localFilters.value.min_price ||
  localFilters.value.max_price
);
</script>

<template>
  <ShopLayout>
    <Head title="Boutique" />

    <!-- Hero Slider -->
    <section v-if="heroSlides && heroSlides.length"
             class="relative w-full overflow-hidden bg-neutral-100 -mt-[72px] xl:-mt-[80px]"
             style="height: clamp(300px, 60vh, 700px);">
      <div class="absolute inset-0">
        <transition-group name="hero-fade" tag="div" class="relative w-full h-full">
          <div v-for="(slide, index) in heroSlides" :key="slide.id"
               v-show="index === currentSlide" class="absolute inset-0">
            <img :src="slide.image_url" :alt="slide.caption ?? ''"
                 class="w-full h-full object-cover" />
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/40 to-transparent pointer-events-none" />
          </div>
        </transition-group>
      </div>

      <template v-if="heroSlides.length > 1">
        <button @click="prev" aria-label="Slide précédente"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center text-white/70 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button @click="next" aria-label="Slide suivante"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center text-white/70 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <div class="absolute bottom-6 right-6 lg:right-10 flex gap-1.5">
          <button v-for="(_, i) in heroSlides" :key="i" @click="goTo(i)"
                  class="w-1.5 h-1.5 rounded-full transition-all"
                  :class="i === currentSlide ? 'bg-white w-4' : 'bg-white/40 hover:bg-white/70'" />
        </div>
      </template>
    </section>

    <!-- En-tête page -->
    <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-10 pb-6">
      <h1 class="text-3xl sm:text-4xl font-bold text-black tracking-tight">
        {{ activeCategory ? activeCategory.name : 'Tous les produits' }}
      </h1>
      <p class="text-sm text-neutral-400 mt-1">{{ products.total }} article(s)</p>
    </div>

    <!-- Barre de filtres horizontale -->
    <div class="border-t border-b border-neutral-200 sticky top-[72px] xl:top-[80px] bg-white z-40">
      <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="flex items-center gap-2 py-3 overflow-x-auto scrollbar-none">

          <!-- Bouton tous les filtres -->
          <button @click="showAllFilters = !showAllFilters"
                  :class="['flex items-center gap-2 shrink-0 px-4 py-1.5 text-[11px] font-bold tracking-[0.12em] uppercase border transition-colors',
                    showAllFilters ? 'bg-black text-white border-black' : 'border-neutral-300 text-black hover:border-black']">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M11 12h2"/>
            </svg>
            Filtres
          </button>

          <div class="w-px h-5 bg-neutral-200 shrink-0 mx-1"></div>

          <!-- Chip Toutes -->
          <button @click="setCategory(null)"
                  :class="['shrink-0 px-4 py-1.5 text-[11px] font-bold tracking-[0.12em] uppercase border transition-colors',
                    !localFilters.category ? 'bg-black text-white border-black' : 'border-neutral-300 text-neutral-500 hover:border-black hover:text-black']">
            Toutes
          </button>

          <!-- Chips catégories -->
          <button v-for="cat in categories" :key="cat.id"
                  @click="setCategory(cat.slug)"
                  :class="['shrink-0 px-4 py-1.5 text-[11px] font-bold tracking-[0.12em] uppercase border transition-colors',
                    localFilters.category === cat.slug
                      ? 'bg-black text-white border-black'
                      : 'border-neutral-300 text-neutral-500 hover:border-black hover:text-black']">
            {{ cat.name }}
          </button>

          <!-- Sort à droite -->
          <div class="ml-auto flex items-center gap-3 shrink-0">
            <span v-if="hasFilters"
                  @click="reset"
                  class="text-[10px] uppercase tracking-widest text-neutral-400 hover:text-black cursor-pointer transition-colors">
              Effacer
            </span>
            <select v-model="localFilters.sort" @change="apply"
                    class="text-[11px] font-bold tracking-[0.12em] uppercase border border-neutral-300 px-3 py-1.5 focus:outline-none focus:border-black bg-white cursor-pointer">
              <option value="default">Pertinence</option>
              <option value="newest">Nouveautés</option>
              <option value="price_asc">Prix ↑</option>
              <option value="price_desc">Prix ↓</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Panneau filtres avancés (search + prix) -->
    <div v-if="showAllFilters" class="border-b border-neutral-200 bg-neutral-50">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-5 flex flex-wrap gap-6 items-end">
        <!-- Recherche -->
        <div>
          <label class="block text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-500 mb-1.5">Recherche</label>
          <input v-model="localFilters.search" type="text" placeholder="Nom du produit…"
                 class="border-b border-neutral-300 pb-1.5 text-sm focus:outline-none focus:border-black bg-transparent placeholder-neutral-300 w-52 transition-colors" />
        </div>
        <!-- Prix -->
        <div>
          <label class="block text-[10px] font-bold tracking-[0.15em] uppercase text-neutral-500 mb-1.5">Prix (DA)</label>
          <div class="flex gap-2 items-center">
            <input v-model="localFilters.min_price" type="number" placeholder="Min"
                   class="w-24 border-b border-neutral-300 pb-1.5 text-sm focus:outline-none focus:border-black bg-transparent placeholder-neutral-300" />
            <span class="text-neutral-300 text-xs">—</span>
            <input v-model="localFilters.max_price" type="number" placeholder="Max"
                   class="w-24 border-b border-neutral-300 pb-1.5 text-sm focus:outline-none focus:border-black bg-transparent placeholder-neutral-300" />
          </div>
        </div>
        <!-- Boutons -->
        <div class="flex gap-3">
          <button @click="apply"
                  class="bg-black text-white text-[10px] font-bold tracking-[0.15em] uppercase px-6 py-2 hover:bg-neutral-800 transition-colors">
            Appliquer
          </button>
          <button @click="reset"
                  class="border border-neutral-300 text-neutral-600 text-[10px] font-bold tracking-[0.15em] uppercase px-6 py-2 hover:border-black hover:text-black transition-colors">
            Réinitialiser
          </button>
        </div>
      </div>
    </div>

    <!-- Grille produits -->
    <div id="products-grid" class="max-w-7xl mx-auto px-6 lg:px-10 py-10">

      <!-- Aucun résultat -->
      <div v-if="products.data.length === 0" class="text-center py-24">
        <p class="text-neutral-300 text-sm tracking-widest uppercase mb-6">Aucun produit trouvé</p>
        <button @click="reset"
                class="text-[10px] font-bold tracking-[0.2em] uppercase border border-black text-black px-8 py-3 hover:bg-black hover:text-white transition-colors">
          Réinitialiser les filtres
        </button>
      </div>

      <!-- Grille 4 colonnes -->
      <div v-else class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex justify-center gap-1 mt-14">
        <Link v-for="link in products.links" :key="link.label"
              :href="link.url || '#'"
              :class="[
                'px-3 py-1.5 text-[10px] font-bold tracking-wider uppercase border transition-colors',
                link.active  ? 'bg-black border-black text-white' : 'border-neutral-200 text-neutral-500 hover:border-black hover:text-black',
                !link.url    ? 'opacity-30 pointer-events-none' : ''
              ]"
              v-html="link.label" preserve-scroll preserve-state />
      </div>
    </div>
  </ShopLayout>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }

.hero-fade-enter-active, .hero-fade-leave-active { transition: opacity 0.8s ease; }
.hero-fade-enter-from, .hero-fade-leave-to { opacity: 0; }
</style>
