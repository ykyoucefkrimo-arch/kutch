<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  flaggedOrders: Object,
  bannedIps: Object,
});

const banForm = useForm({ ip_address: '', reason: '', expires_at: '' });

function banIp() {
  banForm.post(route('admin.antifraud.ban-ip'), {
    onSuccess: () => banForm.reset()
  });
}

function unbanIp(id) {
  if (confirm('Débannir cette IP ?')) {
    banForm.delete(route('admin.antifraud.unban-ip', id));
  }
}

function unflagOrder(id) {
  banForm.patch(route('admin.antifraud.unflag', id));
}

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800', confirmed: 'bg-blue-100 text-blue-800',
  processing: 'bg-indigo-100 text-indigo-800', shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800',
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
    <template #header><h2 class="font-semibold text-gray-700">Sécurité</h2></template>
    <Head title="Admin — Sécurité" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Commandes suspectes -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-semibold text-gray-800">Commandes suspectes</h3>
          <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">
            {{ flaggedOrders.total ?? 0 }}
          </span>
        </div>

        <div v-if="!flaggedOrders.data?.length" class="px-5 py-10 text-center text-gray-400 text-sm">
          <svg class="w-10 h-10 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Aucune commande suspecte
        </div>

        <div class="divide-y divide-gray-50">
          <div v-for="order in flaggedOrders.data" :key="order.id"
            class="px-5 py-3 flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2">
                <Link :href="route('admin.orders.show', order.id)"
                  class="font-medium text-sm text-amber-700 hover:underline">{{ order.order_number }}</Link>
                <span :class="['text-xs px-1.5 py-0.5 rounded', statusColors[order.status]]">
                  {{ statusLabels[order.status] }}
                </span>
              </div>
              <p class="text-xs text-gray-400 mt-0.5">
                Score <strong class="text-red-600">{{ order.fraud_score }}</strong>
                · {{ order.customer_phone }}
                · {{ order.ip_address }}
              </p>
            </div>
            <button @click="unflagOrder(order.id)"
              class="text-xs text-green-600 hover:text-green-800 font-medium whitespace-nowrap flex-shrink-0">
              Démarquer
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="flaggedOrders.last_page > 1" class="flex justify-center gap-1 p-3 border-t border-gray-100">
          <Link v-for="link in flaggedOrders.links" :key="link.label" :href="link.url || '#'"
            :class="['px-2.5 py-1 rounded text-xs', link.active ? 'bg-amber-700 text-white' : 'text-gray-600 hover:bg-gray-100', !link.url ? 'opacity-40 pointer-events-none' : '']"
            v-html="link.label" />
        </div>
      </div>

      <!-- Gestion IPs -->
      <div class="space-y-5">
        <!-- Formulaire ban IP -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-4">Bannir une adresse IP</h3>
          <form @submit.prevent="banIp" class="space-y-3">
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">Adresse IP *</label>
              <input v-model="banForm.ip_address" type="text" placeholder="ex: 192.168.1.100"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500"
                :class="banForm.errors.ip_address ? 'border-red-400' : ''" />
              <p v-if="banForm.errors.ip_address" class="text-red-500 text-xs mt-1">{{ banForm.errors.ip_address }}</p>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">Raison (optionnel)</label>
              <input v-model="banForm.reason" type="text" placeholder="Spam, fraude..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">Expiration (laisser vide = permanent)</label>
              <input v-model="banForm.expires_at" type="datetime-local"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
            <button type="submit" :disabled="banForm.processing"
              class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg text-sm transition-colors">
              {{ banForm.processing ? '...' : '🚫 Bannir cette IP' }}
            </button>
          </form>
        </div>

        <!-- Liste IPs bannies -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">IPs bannies</h3>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">
              {{ bannedIps.total ?? 0 }}
            </span>
          </div>

          <div v-if="!bannedIps.data?.length" class="px-5 py-8 text-center text-gray-400 text-sm">
            Aucune IP bannie
          </div>

          <div class="divide-y divide-gray-50">
            <div v-for="ip in bannedIps.data" :key="ip.id"
              class="px-5 py-3 flex items-start justify-between gap-3">
              <div>
                <p class="font-mono text-sm font-medium text-gray-800">{{ ip.ip_address }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ ip.reason ?? 'Aucune raison' }}
                  <span v-if="ip.expires_at" class="ml-1">
                    · Expire : {{ new Date(ip.expires_at).toLocaleDateString('fr-DZ') }}
                  </span>
                  <span v-else class="ml-1 text-red-500">· Permanent</span>
                </p>
              </div>
              <button @click="unbanIp(ip.id)"
                class="text-xs text-green-600 hover:text-green-800 font-medium whitespace-nowrap flex-shrink-0">
                Débannir
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
