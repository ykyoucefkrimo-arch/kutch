<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ order: Object });

const steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
const stepLabels = {
  pending: 'En attente',
  confirmed: 'Confirmée',
  processing: 'En préparation',
  shipped: 'Expédiée',
  delivered: 'Livrée',
};
const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-blue-100 text-blue-800',
  processing: 'bg-indigo-100 text-indigo-800',
  shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

function stepIndex(status) {
  return steps.indexOf(status);
}

function formatPrice(n) {
  return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
}
</script>

<template>
  <ShopLayout>
    <Head :title="`Suivi — ${order.order_number}`" />

    <!-- Page header -->
    <div class="border-b border-neutral-200">
      <div class="max-w-3xl mx-auto px-6 lg:px-10 py-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/logo.png" alt="Kutch" class="h-6 w-auto" />
          <h1 class="text-[11px] font-bold tracking-[0.2em] uppercase text-black">Suivi de commande</h1>
        </div>
        <Link href="/" class="text-[10px] tracking-widest uppercase text-neutral-400 hover:text-black transition-colors">
          ← Accueil
        </Link>
      </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 lg:px-10 py-10 space-y-6">

      <!-- N° commande -->
      <div class="border border-neutral-200 px-5 py-4 flex items-center justify-between">
        <span class="text-[10px] tracking-widest uppercase text-neutral-400">N° de commande</span>
        <span class="font-bold text-black text-sm">{{ order.order_number }}</span>
      </div>

      <!-- Progression (sauf annulée) -->
      <div v-if="order.status !== 'cancelled'" class="bg-white border border-neutral-200 p-6 overflow-x-auto">
        <div class="flex items-start min-w-max mx-auto">
          <template v-for="(step, i) in steps" :key="step">
            <div class="flex flex-col items-center">
              <div :class="[
                'w-8 h-8 flex items-center justify-center font-bold text-xs transition-colors border',
                stepIndex(order.status) >= i
                  ? 'bg-black border-black text-white'
                  : 'border-neutral-300 text-neutral-400 bg-white'
              ]">
                <svg v-if="stepIndex(order.status) > i" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                <span v-else>{{ i + 1 }}</span>
              </div>
              <span :class="[
                'text-[9px] mt-2 text-center w-20 tracking-wide uppercase font-semibold',
                stepIndex(order.status) >= i ? 'text-black' : 'text-neutral-300'
              ]">{{ stepLabels[step] }}</span>
            </div>
            <div v-if="i < steps.length - 1"
              :class="['flex-1 h-px mx-1 mt-4', stepIndex(order.status) > i ? 'bg-black' : 'bg-neutral-200']"
              style="min-width: 40px" />
          </template>
        </div>
      </div>

      <!-- Annulée -->
      <div v-else class="border border-red-200 p-6 text-center">
        <p class="text-[10px] tracking-widest uppercase font-bold text-red-600 mb-1">Commande annulée</p>
        <p class="text-neutral-500 text-sm">Cette commande a été annulée.</p>
      </div>

      <!-- Infos + récap -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-neutral-200">
        <!-- Livraison -->
        <div class="bg-white p-5">
          <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black mb-4">Livraison</h2>
          <div class="space-y-2.5">
            <div v-for="(val, label) in {
              'Client': order.customer_name,
              'Téléphone': order.customer_phone,
              'Wilaya': order.wilaya?.name,
              'Commune': order.commune?.name,
              'Adresse': order.address,
            }" :key="label" class="flex gap-2 text-sm">
              <span class="text-neutral-400 w-20 flex-shrink-0 text-xs">{{ label }}</span>
              <span class="text-black text-xs font-medium">{{ val }}</span>
            </div>
          </div>
        </div>

        <!-- Montants -->
        <div class="bg-white p-5">
          <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black mb-4">Montants</h2>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between text-neutral-500">
              <span>Sous-total</span>
              <span>{{ formatPrice(order.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-neutral-500">
              <span>Livraison</span>
              <span>{{ order.shipping_cost === 0 ? 'Gratuite' : formatPrice(order.shipping_cost) }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-neutral-100 font-bold text-sm text-black">
              <span>Total</span>
              <span>{{ formatPrice(order.total) }}</span>
            </div>
            <p class="text-[10px] tracking-widest uppercase text-neutral-400 pt-2">
              Paiement à la livraison
            </p>
          </div>
        </div>
      </div>

      <!-- Articles -->
      <div class="border border-neutral-200">
        <div class="px-5 py-4 border-b border-neutral-100">
          <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black">Articles commandés</h2>
        </div>
        <div class="divide-y divide-neutral-100">
          <div v-for="item in order.items" :key="item.id"
            class="flex justify-between items-center px-5 py-3 text-sm">
            <div>
              <p class="font-medium text-black text-xs">{{ item.product_name }}</p>
              <p class="text-neutral-400 text-[10px] mt-0.5">{{ formatPrice(item.unit_price) }} × {{ item.quantity }}</p>
            </div>
            <span class="font-bold text-black text-xs">{{ formatPrice(item.subtotal) }}</span>
          </div>
        </div>
      </div>

      <!-- Historique -->
      <div v-if="order.logs && order.logs.length" class="border border-neutral-200">
        <div class="px-5 py-4 border-b border-neutral-100">
          <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black">Historique</h2>
        </div>
        <div class="divide-y divide-neutral-100">
          <div v-for="log in order.logs" :key="log.id" class="flex gap-4 px-5 py-3">
            <div class="w-1.5 h-1.5 rounded-full bg-black mt-1.5 flex-shrink-0"></div>
            <div>
              <span :class="['text-[10px] font-bold tracking-widest uppercase px-2 py-0.5', statusColors[log.status_to]]">
                {{ stepLabels[log.status_to] ?? log.status_to }}
              </span>
              <p v-if="log.comment" class="text-neutral-500 text-xs mt-1 italic">{{ log.comment }}</p>
              <p class="text-neutral-400 text-[10px] mt-1">
                {{ new Date(log.created_at).toLocaleString('fr-DZ', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </ShopLayout>
</template>
