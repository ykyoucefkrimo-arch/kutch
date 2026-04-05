<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ settings: Object });

const form = useForm({ ...props.settings });

function save() {
  form.put(route('admin.settings.update'));
}
</script>

<template>
  <AdminLayout>
    <template #header><h2 class="font-semibold text-gray-700">Paramètres</h2></template>
    <Head title="Admin — Paramètres" />

    <div class="max-w-2xl space-y-6">
      <form @submit.prevent="save">
        <!-- Boutique -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4 mb-5">
          <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">Informations boutique</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la boutique *</label>
            <input v-model="form.shop_name" type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
              <input v-model="form.shop_phone" type="text"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.shop_email" type="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <textarea v-model="form.shop_address" rows="2"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Texte pied de page</label>
            <textarea v-model="form.footer_text" rows="2"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" />
          </div>
        </div>

        <!-- Réseaux sociaux -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4 mb-5">
          <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">Réseaux sociaux & Contact</h3>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (avec indicatif)</label>
            <input v-model="form.whatsapp" type="text" placeholder="+213555000000"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Page Facebook</label>
            <input v-model="form.facebook" type="url" placeholder="https://facebook.com/..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Page Instagram</label>
            <input v-model="form.instagram" type="url" placeholder="https://instagram.com/..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>
        </div>

        <div class="flex gap-3">
          <button type="submit" :disabled="form.processing"
            class="bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold px-8 py-3 rounded-xl transition-colors">
            {{ form.processing ? 'Enregistrement...' : 'Sauvegarder les paramètres' }}
          </button>
          <div v-if="form.wasSuccessful" class="flex items-center text-sm text-green-600 font-medium">
            ✓ Enregistré
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
