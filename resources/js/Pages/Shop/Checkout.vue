<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import WilayaCommuneSelect from '@/Components/Shop/WilayaCommuneSelect.vue';
import { useCart } from '@/Composables/useCart';

const { items, total, formatPrice, clearCart } = useCart();

const form = useForm({
  customer_name: '',
  customer_phone: '',
  wilaya_id: null,
  commune_id: null,
  items: [],
  _hp_field: '',
});

const isEmpty = computed(() => items.value.length === 0);

function submit() {
  form.items = items.value.map(i => ({
    product_id: i.product_id,
    quantity: i.quantity,
    options: i.options,
  }));
  form.post(route('orders.store'), {
    onSuccess: () => clearCart(),
  });
}
</script>

<template>
  <ShopLayout>
    <Head title="Commander" />

    <!-- Breadcrumb -->
    <div class="border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-6 lg:px-10 py-4 flex items-center gap-2 text-[10px] tracking-widest uppercase text-neutral-400">
        <a href="/" class="hover:text-black transition-colors">Accueil</a>
        <span>·</span>
        <span class="text-black">Commander</span>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12">

      <!-- Empty cart -->
      <div v-if="isEmpty" class="text-center py-24">
        <p class="text-neutral-400 text-sm tracking-widest uppercase mb-6">Votre panier est vide</p>
        <a href="/produits"
          class="text-[10px] font-bold tracking-[0.2em] uppercase border border-black text-black px-10 py-3 hover:bg-black hover:text-white transition-colors">
          ← Voir les produits
        </a>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-5 gap-16">

        <!-- Form -->
        <div class="lg:col-span-3">
          <h1 class="text-3xl sm:text-4xl font-bold uppercase tracking-tight text-black mb-10">Commande</h1>

          <form @submit.prevent="submit" class="space-y-8">
            <!-- Honeypot -->
            <input type="text" name="_hp_field" v-model="form._hp_field" class="hidden" tabindex="-1" autocomplete="off" />

            <!-- Informations -->
            <div class="space-y-6">
              <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black pb-3 border-b border-neutral-200">
                Informations
              </h2>

              <!-- Nom complet -->
              <div>
                <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">
                  Nom &amp; Prénom *
                </label>
                <input v-model="form.customer_name" type="text" placeholder="Ex: Mohamed Amrani"
                  class="w-full border-b py-2.5 focus:outline-none bg-transparent text-sm transition-colors placeholder-neutral-300"
                  :class="form.errors.customer_name ? 'border-red-500' : 'border-neutral-300 focus:border-black'" />
                <p v-if="form.errors.customer_name" class="text-red-500 text-xs mt-1">{{ form.errors.customer_name }}</p>
              </div>

              <!-- Téléphone -->
              <div>
                <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">
                  Numéro de téléphone *
                </label>
                <input v-model="form.customer_phone" type="tel" placeholder="Ex: 0555 12 34 56"
                  class="w-full border-b py-2.5 focus:outline-none bg-transparent text-sm transition-colors placeholder-neutral-300"
                  :class="form.errors.customer_phone ? 'border-red-500' : 'border-neutral-300 focus:border-black'" />
                <p v-if="form.errors.customer_phone" class="text-red-500 text-xs mt-1">{{ form.errors.customer_phone }}</p>
              </div>
            </div>

            <!-- Livraison -->
            <div class="space-y-6">
              <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black pb-3 border-b border-neutral-200">
                Adresse de livraison
              </h2>

              <WilayaCommuneSelect
                v-model:model-wilaya="form.wilaya_id"
                v-model:model-commune="form.commune_id"
                :error-wilaya="form.errors.wilaya_id"
                :error-commune="form.errors.commune_id"
              />
            </div>

            <!-- Payment note -->
            <div class="flex items-center gap-3 text-[10px] tracking-wider text-neutral-400 uppercase py-4 border-t border-neutral-200">
              <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Paiement à la livraison (Cash on delivery)
            </div>

            <!-- Submit -->
            <button type="submit" :disabled="form.processing"
              class="w-full py-4 text-[11px] font-bold tracking-[0.2em] uppercase transition-all disabled:opacity-40 bg-black text-white hover:bg-neutral-800 flex items-center justify-center gap-3">
              <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              {{ form.processing ? 'Traitement...' : 'Confirmer la commande' }}
            </button>
          </form>
        </div>

        <!-- Récapitulatif -->
        <div class="lg:col-span-2">
          <div class="sticky top-24">
            <h2 class="text-[10px] font-bold tracking-[0.2em] uppercase text-black pb-3 border-b border-neutral-200 mb-6">
              Récapitulatif
            </h2>

            <div class="space-y-4 mb-6">
              <div v-for="item in items" :key="item.product_id" class="flex items-start gap-3">
                <div class="w-14 h-14 bg-neutral-100 overflow-hidden flex-shrink-0">
                  <img v-if="item.main_image" :src="`/storage/${item.main_image}`"
                    :alt="item.name" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-black truncate">{{ item.name }}</p>
                  <p class="text-[10px] tracking-widest uppercase text-neutral-400 mt-0.5">Qté: {{ item.quantity }}</p>
                </div>
                <p class="text-sm font-bold text-black flex-shrink-0">
                  {{ formatPrice(item.unit_price * item.quantity) }}
                </p>
              </div>
            </div>

            <div class="border-t border-neutral-200 pt-4 space-y-2">
              <div class="flex justify-between text-xs text-neutral-500 uppercase tracking-wider">
                <span>Sous-total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
              <div class="flex justify-between text-xs text-neutral-500 uppercase tracking-wider">
                <span>Livraison</span>
                <span class="text-black font-semibold">Gratuite</span>
              </div>
              <div class="flex justify-between text-sm font-bold text-black uppercase tracking-wider pt-3 border-t border-neutral-200">
                <span>Total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>
