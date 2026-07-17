<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Shop/ProductCard.vue';
const props = defineProps({
  heroSlides: Array,
  featuredProducts: Array,
  categories: Array,
  newProducts: Array,
});

// ── Slider ────────────────────────────────────────────────────
const currentSlide = ref(0);
let autoplayTimer = null;

// ── Category carousel ─────────────────────────────────────────
const catTrack  = ref(null);
const catIndex  = ref(0);
const catVisibleMax = ref(window.innerWidth < 640 ? 2 : 5);
// S'il y a moins de categories que de slots visibles, on reduit les slots au
// nombre reel de categories pour que la rangee reste centree au lieu de laisser
// un vide a droite.
const catVisible = computed(() => Math.min(catVisibleMax.value, props.categories?.length || catVisibleMax.value));

// Nouveautés : 2 produits sur téléphone (< 768px), 3 sur les écrans plus grands.
const newLimit = ref(window.innerWidth < 768 ? 2 : 3);

function onCatResize() {
  catVisibleMax.value = window.innerWidth < 640 ? 2 : 5;
  catIndex.value = 0;
  newLimit.value = window.innerWidth < 768 ? 2 : 3;
}

function catCanPrev() { return catIndex.value > 0; }
function catCanNext() {
  if (!props.categories) return false;
  return catIndex.value < props.categories.length - catVisible.value;
}
function catPrev() { if (catCanPrev()) catIndex.value--; }
function catNext() { if (catCanNext()) catIndex.value++; }

const totalSlides = () => props.heroSlides?.length ?? 0;

function goTo(index) {
  currentSlide.value = (index + totalSlides()) % totalSlides();
  resetAutoplay();
}
function prev() { goTo(currentSlide.value - 1); }
function next() { goTo(currentSlide.value + 1); }

function resetAutoplay() {
  clearInterval(autoplayTimer);
  if (totalSlides() > 1) {
    autoplayTimer = setInterval(() => {
      currentSlide.value = (currentSlide.value + 1) % totalSlides();
    }, 5000);
  }
}

onMounted(() => {
  resetAutoplay();
  window.addEventListener('resize', onCatResize, { passive: true });
});
onUnmounted(() => {
  clearInterval(autoplayTimer);
  window.removeEventListener('resize', onCatResize);
});
</script>

<template>
  <ShopLayout>
    <Head title="Accueil" />

    <!-- Hero Slider — remonte derrière la navbar fixe (-72px du spacer ShopLayout) -->
    <section v-if="heroSlides && heroSlides.length"
      class="relative w-full overflow-hidden bg-neutral-100 -mt-[72px]"
      style="height: clamp(400px, 90vh, 900px);">

      <!-- Slides -->
      <div class="absolute inset-0">
        <transition-group name="hero-fade" tag="div" class="relative w-full h-full">
          <div v-for="(slide, index) in heroSlides" :key="slide.id"
            v-show="index === currentSlide"
            class="absolute inset-0">
            <img :src="slide.image_url" :alt="slide.caption ?? ''"
              class="w-full h-full object-cover" />
            <!-- Overlay léger en bas -->
            <div class="absolute inset-x-0 bottom-0 h-40
              bg-gradient-to-t from-black/40 to-transparent pointer-events-none" />
          </div>
        </transition-group>
      </div>

      <!-- Légende -->
      <div v-if="heroSlides[currentSlide]?.caption || heroSlides[currentSlide]?.subtitle"
        class="absolute bottom-6 left-6 lg:left-10 text-white">
        <component
          :is="heroSlides[currentSlide]?.link ? Link : 'div'"
          :href="heroSlides[currentSlide]?.link ?? undefined"
          class="group">
          <p v-if="heroSlides[currentSlide]?.caption"
            class="text-xs font-bold tracking-[0.2em] uppercase opacity-90">
            {{ heroSlides[currentSlide].caption }}
          </p>
          <p v-if="heroSlides[currentSlide]?.subtitle"
            class="text-[11px] tracking-widest uppercase opacity-60 mt-0.5">
            {{ heroSlides[currentSlide].subtitle }}
          </p>
        </component>
      </div>

      <!-- Flèches (seulement si > 1 slide) -->
      <template v-if="heroSlides.length > 1">
        <button @click="prev" aria-label="Slide précédente"
          class="absolute left-4 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center
            text-white/70 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button @click="next" aria-label="Slide suivante"
          class="absolute right-4 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center
            text-white/70 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- Indicateurs (points) -->
        <div class="absolute bottom-6 right-6 lg:right-10 flex gap-1.5">
          <button v-for="(_, i) in heroSlides" :key="i" @click="goTo(i)"
            :aria-label="`Slide ${i + 1}`"
            class="w-1.5 h-1.5 rounded-full transition-all"
            :class="i === currentSlide ? 'bg-white w-4' : 'bg-white/40 hover:bg-white/70'" />
        </div>
      </template>
    </section>

    <!-- New Products -->
    <section v-if="newProducts && newProducts.length" class="border-b border-neutral-200">
      <div class="w-[90%] mx-auto py-14">
        <div class="flex items-center mb-10 ml-[5%]">
          <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Nouveautés</h2>
          <div class="h-px flex-1 bg-neutral-300 ml-8"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
          <ProductCard v-for="product in newProducts.slice(0, newLimit)" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section v-if="categories && categories.length">
      <div class="w-[90%] mx-auto py-14">
        <h2 class="text-center text-sm font-bold text-black mb-10">Que recherchez-vous ?</h2>

        <div class="relative">
          <!-- Flèche gauche -->
          <button v-if="catCanPrev()" @click="catPrev"
            class="absolute -left-5 top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white border border-neutral-200 rounded-full flex items-center justify-center shadow-sm hover:border-black transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <!-- Track -->
          <div ref="catTrack" class="overflow-hidden">
            <div class="flex transition-transform duration-500 ease-in-out gap-4"
                 :style="{ transform: `translateX(calc(-${catIndex} * (100% / ${catVisible}) - ${catIndex} * (1rem / ${catVisible})))` }">
              <Link v-for="cat in categories" :key="cat.id"
                    :href="route('products.index', { category: cat.slug })"
                    class="group shrink-0 flex flex-col"
                    :style="{ width: `calc((100% - ${catVisible - 1}rem) / ${catVisible})` }">
                <!-- Image -->
                <div class="aspect-[3/4] overflow-hidden bg-neutral-100 mb-3">
                  <img v-if="cat.image_url" :src="cat.image_url" :alt="cat.name"
                       class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
                <!-- Nom + séparateur -->
                <p class="text-[11px] font-bold tracking-[0.1em] uppercase text-black group-hover:text-neutral-500 transition-colors">
                  {{ cat.name }}
                </p>
                <div class="mt-2 h-px bg-neutral-200 w-full"></div>
              </Link>
            </div>
          </div>

          <!-- Flèche droite -->
          <button v-if="catCanNext()" @click="catNext"
            class="absolute -right-5 top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white border border-neutral-200 rounded-full flex items-center justify-center shadow-sm hover:border-black transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
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
          <img :src="`${$page.props.appUrl}/logo.png`" alt="" class="w-20 h-20 object-contain opacity-10" />
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
