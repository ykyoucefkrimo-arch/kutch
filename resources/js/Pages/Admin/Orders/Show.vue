<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ order: Object });

const statusForm = useForm({
  status: props.order.status,
  comment: '',
});

function updateStatus() {
  statusForm.patch(route('admin.orders.status', props.order.id));
}

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
const statusOptions = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center gap-3 flex-wrap">
        <Link :href="route('admin.orders.index')" class="text-gray-400 hover:text-gray-600 transition-colors text-sm">← Retour</Link>
        <span class="text-gray-300">|</span>
        <h2 class="font-semibold text-gray-700">{{ order.order_number }}</h2>
        <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', statusColors[order.status]]">
          {{ statusLabels[order.status] }}
        </span>
        <span v-if="order.is_flagged" class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">⚠ Suspect</span>
      </div>
    </template>
    <Head :title="`Commande ${order.order_number}`" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Colonne principale -->
      <div class="lg:col-span-2 space-y-5">

        <!-- Client -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Informations client</h3>
          <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div><p class="text-gray-400 text-xs mb-0.5">Nom</p><p class="font-medium text-gray-800">{{ order.customer_name }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Téléphone</p><p class="font-medium">{{ order.customer_phone }}</p></div>
            <div v-if="order.customer_phone2"><p class="text-gray-400 text-xs mb-0.5">Tél. 2</p><p>{{ order.customer_phone2 }}</p></div>
            <div v-if="order.customer_email"><p class="text-gray-400 text-xs mb-0.5">Email</p><p>{{ order.customer_email }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Wilaya</p><p>{{ order.wilaya?.name }}</p></div>
            <div><p class="text-gray-400 text-xs mb-0.5">Commune</p><p>{{ order.commune?.name }}</p></div>
            <div class="col-span-2"><p class="text-gray-400 text-xs mb-0.5">Adresse</p><p>{{ order.address }}</p></div>
            <div v-if="order.notes" class="col-span-2 bg-stone-50 rounded-lg p-3">
              <p class="text-gray-400 text-xs mb-0.5">Notes client</p>
              <p class="italic text-gray-600">{{ order.notes }}</p>
            </div>
          </div>
        </div>

        <!-- Articles -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Articles commandés</h3>
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="pb-2 text-left text-gray-400 font-medium text-xs">Produit</th>
                <th class="pb-2 text-right text-gray-400 font-medium text-xs">Prix unit.</th>
                <th class="pb-2 text-right text-gray-400 font-medium text-xs">Qté</th>
                <th class="pb-2 text-right text-gray-400 font-medium text-xs">Sous-total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in order.items" :key="item.id" class="border-b border-gray-50 last:border-0">
                <td class="py-2.5 font-medium text-gray-800">{{ item.product_name }}</td>
                <td class="py-2.5 text-right text-gray-600">{{ formatPrice(item.unit_price) }}</td>
                <td class="py-2.5 text-right text-gray-700">{{ item.quantity }}</td>
                <td class="py-2.5 text-right font-medium">{{ formatPrice(item.subtotal) }}</td>
              </tr>
            </tbody>
            <tfoot class="border-t border-gray-200 text-sm">
              <tr>
                <td colspan="3" class="pt-3 text-right text-gray-500">Sous-total</td>
                <td class="pt-3 text-right font-medium">{{ formatPrice(order.subtotal) }}</td>
              </tr>
              <tr>
                <td colspan="3" class="text-right text-gray-500">Livraison</td>
                <td class="text-right font-medium text-green-600">
                  {{ order.shipping_cost === 0 ? 'Gratuite' : formatPrice(order.shipping_cost) }}
                </td>
              </tr>
              <tr>
                <td colspan="3" class="pt-2 text-right font-bold text-base">Total</td>
                <td class="pt-2 text-right font-bold text-lg text-amber-900">{{ formatPrice(order.total) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Logs -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Historique</h3>
          <div v-if="!order.logs?.length" class="text-sm text-gray-400 py-3">Aucun historique</div>
          <div class="space-y-3">
            <div v-for="log in order.logs" :key="log.id" class="flex gap-3 text-sm">
              <div class="flex flex-col items-center pt-1">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500 flex-shrink-0"></div>
                <div class="w-px bg-gray-200 flex-1 mt-1"></div>
              </div>
              <div class="pb-3 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span v-if="log.status_from" :class="['text-xs px-1.5 py-0.5 rounded', statusColors[log.status_from]]">
                    {{ statusLabels[log.status_from] }}
                  </span>
                  <span v-if="log.status_from" class="text-gray-400 text-xs">→</span>
                  <span :class="['text-xs px-1.5 py-0.5 rounded font-medium', statusColors[log.status_to]]">
                    {{ statusLabels[log.status_to] }}
                  </span>
                  <span v-if="log.user" class="text-gray-400 text-xs">({{ log.user.name }})</span>
                </div>
                <p v-if="log.comment" class="text-gray-500 italic text-xs mt-1">{{ log.comment }}</p>
                <p class="text-gray-400 text-xs mt-0.5">
                  {{ new Date(log.created_at).toLocaleString('fr-DZ', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colonne actions -->
      <div class="space-y-5">
        <!-- Fraude -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Score antifraude</h3>
          <div class="flex items-center gap-4">
            <div :class="[
              'text-3xl font-bold px-4 py-3 rounded-xl',
              order.fraud_score >= 50 ? 'bg-red-100 text-red-800'
              : order.fraud_score >= 20 ? 'bg-yellow-100 text-yellow-800'
              : 'bg-green-100 text-green-800'
            ]">{{ order.fraud_score }}<span class="text-base font-normal">/100</span></div>
            <div class="text-sm">
              <p v-if="order.is_flagged" class="text-red-600 font-medium">⚠ Marquée suspecte</p>
              <p v-else class="text-green-600 font-medium">✓ Non suspecte</p>
              <p class="text-xs text-gray-400 mt-1 font-mono">{{ order.ip_address }}</p>
            </div>
          </div>
          <div v-if="order.honeypot_triggered" class="mt-3 bg-red-50 rounded-lg p-2 text-xs text-red-700">
            ⚠ Honeypot déclenché — bot possible
          </div>
        </div>

        <!-- Changer statut -->
        <div class="bg-white rounded-xl shadow-sm p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Changer le statut</h3>
          <form @submit.prevent="updateStatus" class="space-y-3">
            <select v-model="statusForm.status"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
              <option v-for="s in statusOptions" :key="s" :value="s">{{ statusLabels[s] }}</option>
            </select>
            <textarea v-model="statusForm.comment" rows="2"
              placeholder="Commentaire (optionnel)..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" />
            <button type="submit" :disabled="statusForm.processing"
              class="w-full bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg text-sm transition-colors">
              {{ statusForm.processing ? 'Mise à jour...' : 'Mettre à jour le statut' }}
            </button>
          </form>
        </div>

        <!-- Notes admin -->
        <div v-if="order.admin_notes" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
          <h3 class="font-semibold text-yellow-800 text-sm mb-1">Notes admin</h3>
          <p class="text-yellow-700 text-sm">{{ order.admin_notes }}</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
