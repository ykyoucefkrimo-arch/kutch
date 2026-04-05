<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  ordersByStatus: Object,
});

function formatPrice(n) {
  return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
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

const kpiCards = [
  { label: "Commandes aujourd'hui", key: 'orders_today', color: 'text-gray-900', format: 'number' },
  { label: 'Commandes ce mois', key: 'orders_month', color: 'text-gray-900', format: 'number' },
  { label: 'CA ce mois (livré)', key: 'revenue_month', color: 'text-amber-700', format: 'price' },
  { label: "CA aujourd'hui", key: 'revenue_today', color: 'text-green-700', format: 'price' },
  { label: 'En attente', key: 'pending_orders', color: 'text-yellow-600', format: 'number' },
  { label: 'Suspects', key: 'flagged_orders', color: 'text-red-600', format: 'number' },
  { label: 'Produits actifs', key: 'total_products', color: 'text-gray-900', format: 'number' },
  { label: 'Rupture de stock', key: 'out_of_stock', color: 'text-orange-600', format: 'number' },
];
</script>

<template>
  <AdminLayout>
    <template #header><h2 class="font-semibold text-gray-700">Tableau de bord</h2></template>
    <Head title="Admin — Dashboard" />

    <!-- KPI -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div v-for="card in kpiCards" :key="card.key" class="bg-white rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide leading-tight">{{ card.label }}</p>
        <p :class="['text-2xl font-bold mt-2', card.color]">
          {{ card.format === 'price' ? formatPrice(stats[card.key] ?? 0) : (stats[card.key] ?? 0) }}
        </p>
      </div>
    </div>

    <!-- Recent orders -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Dernières commandes</h3>
        <Link :href="route('admin.orders.index')" class="text-sm text-amber-700 hover:text-amber-900 font-medium">
          Voir tout →
        </Link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">N°</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Client</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Wilaya</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Statut</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!recentOrders?.length">
              <td colspan="6" class="px-4 py-8 text-center text-gray-400">Aucune commande</td>
            </tr>
            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3">
                <Link :href="route('admin.orders.show', order.id)"
                  class="font-medium text-amber-700 hover:underline">{{ order.order_number }}</Link>
                <span v-if="order.is_flagged" class="ml-1 text-xs text-red-500">⚠</span>
              </td>
              <td class="px-4 py-3 text-gray-700">{{ order.customer_name }}</td>
              <td class="px-4 py-3 text-gray-500">{{ order.wilaya?.name ?? '—' }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ formatPrice(order.total) }}</td>
              <td class="px-4 py-3">
                <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', statusColors[order.status]]">
                  {{ statusLabels[order.status] }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-400 text-xs">
                {{ new Date(order.created_at).toLocaleDateString('fr-DZ') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
