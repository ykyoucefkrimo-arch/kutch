<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  orders: Object,
  filters: Object,
  statusOptions: Array,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

function applyFilters() {
  router.get(route('admin.orders.index'), {
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, preserveScroll: true });
}

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-blue-100 text-blue-800',
  processing: 'bg-indigo-100 text-indigo-800',
  shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};
const statusLabels = {
  pending: 'En attente', confirmed: 'Confirmée', processing: 'En préparation',
  shipped: 'Expédiée', delivered: 'Livrée', cancelled: 'Annulée',
};

function formatPrice(n) {
  return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
}
</script>

<template>
  <AdminLayout>
    <template #header><h2 class="font-semibold text-gray-700">Commandes</h2></template>
    <Head title="Admin — Commandes" />

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5 flex flex-wrap gap-3">
      <input v-model="search" @keyup.enter="applyFilters" type="text"
        placeholder="N° commande, nom, téléphone..."
        class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
      <select v-model="statusFilter" @change="applyFilters"
        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        <option value="">Tous les statuts</option>
        <option v-for="s in statusOptions" :key="s" :value="s">{{ statusLabels[s] }}</option>
      </select>
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
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">N° Commande</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Client</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Wilaya</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Statut</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Score</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
              <th class="px-4 py-3 w-16"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!orders.data?.length">
              <td colspan="8" class="px-4 py-10 text-center text-gray-400">Aucune commande trouvée</td>
            </tr>
            <tr v-for="order in orders.data" :key="order.id"
              :class="['hover:bg-gray-50 transition-colors', order.is_flagged ? 'bg-red-50 hover:bg-red-50' : '']">
              <td class="px-4 py-3">
                <Link :href="route('admin.orders.show', order.id)"
                  class="font-medium text-amber-700 hover:underline">{{ order.order_number }}</Link>
                <span v-if="order.is_flagged" class="ml-1 text-red-500 text-xs">⚠ Suspect</span>
              </td>
              <td class="px-4 py-3">
                <p class="font-medium text-gray-800">{{ order.customer_name }}</p>
                <p class="text-xs text-gray-400">{{ order.customer_phone }}</p>
              </td>
              <td class="px-4 py-3 text-gray-500">{{ order.wilaya?.name ?? '—' }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ formatPrice(order.total) }}</td>
              <td class="px-4 py-3">
                <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', statusColors[order.status]]">
                  {{ statusLabels[order.status] }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="[
                  'text-xs font-bold px-2 py-0.5 rounded',
                  order.fraud_score >= 50 ? 'bg-red-100 text-red-800'
                  : order.fraud_score >= 20 ? 'bg-yellow-100 text-yellow-800'
                  : 'bg-green-100 text-green-800'
                ]">{{ order.fraud_score }}</span>
              </td>
              <td class="px-4 py-3 text-gray-400 text-xs">
                {{ new Date(order.created_at).toLocaleDateString('fr-DZ') }}
              </td>
              <td class="px-4 py-3">
                <Link :href="route('admin.orders.show', order.id)"
                  class="text-xs text-amber-700 hover:text-amber-900 font-medium">Voir →</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="orders.last_page > 1" class="flex justify-center gap-1 p-4 border-t border-gray-100 flex-wrap">
        <Link v-for="link in orders.links" :key="link.label"
          :href="link.url || '#'"
          :class="[
            'px-3 py-1.5 rounded text-sm transition-colors',
            link.active ? 'bg-amber-700 text-white' : 'text-gray-600 hover:bg-gray-100',
            !link.url ? 'opacity-40 pointer-events-none' : ''
          ]"
          v-html="link.label" />
      </div>
    </div>
  </AdminLayout>
</template>
