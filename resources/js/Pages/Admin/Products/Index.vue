<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
});

const search = ref(props.filters?.search || '');

function applyFilters() {
  router.get(route('admin.products.index'), { search: search.value || undefined }, { preserveState: true });
}

function deleteProduct(id) {
  if (confirm('Archiver ce produit ?')) {
    router.delete(route('admin.products.destroy', id));
  }
}

function restoreProduct(id) {
  router.post(route('admin.products.restore', id));
}

function formatPrice(n) {
  return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="font-semibold text-gray-700">Produits</h2>
        <Link :href="route('admin.products.create')"
          class="bg-amber-700 hover:bg-amber-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
          + Nouveau produit
        </Link>
      </div>
    </template>
    <Head title="Admin — Produits" />

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5 flex gap-3">
      <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Rechercher un produit..."
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <button @click="applyFilters"
        class="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Filtrer
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Image</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Produit</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Catégorie</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Prix</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Stock</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Statut</th>
              <th class="px-4 py-3 w-28"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!products.data?.length">
              <td colspan="7" class="px-4 py-10 text-center text-gray-400">Aucun produit</td>
            </tr>
            <tr v-for="product in products.data" :key="product.id"
              :class="['hover:bg-gray-50 transition-colors', product.deleted_at ? 'opacity-60' : '']">
              <td class="px-4 py-3">
                <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                  <img v-if="product.main_image" :src="`/storage/${product.main_image}`"
                    :alt="product.name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <p class="font-medium text-gray-800">{{ product.name }}</p>
                <p class="text-xs text-gray-400">{{ product.sku ?? '—' }}</p>
              </td>
              <td class="px-4 py-3 text-gray-500 text-xs">{{ product.category?.name ?? '—' }}</td>
              <td class="px-4 py-3">
                <p class="font-medium text-amber-900">{{ formatPrice(product.price_promo ?? product.price) }}</p>
                <p v-if="product.price_promo" class="text-xs text-gray-400 line-through">{{ formatPrice(product.price) }}</p>
              </td>
              <td class="px-4 py-3">
                <span :class="[
                  'text-xs font-medium px-2 py-0.5 rounded-full',
                  product.in_stock ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]">
                  {{ product.in_stock ? product.stock + ' en stock' : 'Rupture' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span v-if="product.deleted_at" class="text-xs text-red-500 font-medium">Archivé</span>
                <span v-else-if="product.is_active" class="text-xs text-green-600 font-medium">Actif</span>
                <span v-else class="text-xs text-gray-400 font-medium">Inactif</span>
                <span v-if="product.is_featured" class="ml-1 text-xs text-amber-600">⭐</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2 justify-end">
                  <template v-if="!product.deleted_at">
                    <Link :href="route('admin.products.edit', product.id)"
                      class="text-xs text-amber-700 hover:text-amber-900 font-medium">Modifier</Link>
                    <button @click="deleteProduct(product.id)"
                      class="text-xs text-red-500 hover:text-red-700 font-medium">Archiver</button>
                  </template>
                  <button v-else @click="restoreProduct(product.id)"
                    class="text-xs text-green-600 hover:text-green-800 font-medium">Restaurer</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex justify-center gap-1 p-4 border-t border-gray-100 flex-wrap">
        <Link v-for="link in products.links" :key="link.label"
          :href="link.url || '#'"
          :class="['px-3 py-1.5 rounded text-sm transition-colors', link.active ? 'bg-amber-700 text-white' : 'text-gray-600 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
          v-html="link.label" />
      </div>
    </div>
  </AdminLayout>
</template>
