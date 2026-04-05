<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  product: Object,
  categories: Array,
});

const isEdit = !!props.product;

const form = useForm({
  name:              props.product?.name ?? '',
  category_id:       props.product?.category_id ?? null,
  short_description: props.product?.short_description ?? '',
  description:       props.product?.description ?? '',
  price:             props.product?.price ?? '',
  price_promo:       props.product?.price_promo ?? '',
  sku:               props.product?.sku ?? '',
  stock:             props.product?.stock ?? 0,
  in_stock:          props.product?.in_stock ?? true,
  is_custom:         props.product?.is_custom ?? false,
  is_active:         props.product?.is_active ?? true,
  is_featured:       props.product?.is_featured ?? false,
  material:          props.product?.material ?? '',
  color:             props.product?.color ?? '',
  delivery_days:     props.product?.delivery_days ?? 7,
  sort_order:        props.product?.sort_order ?? 0,
  main_image:        null,
  images:            null,
});

function submit() {
  if (isEdit) {
    form.put(route('admin.products.update', props.product.id));
  } else {
    form.post(route('admin.products.store'));
  }
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.products.index')" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
          ← Retour
        </Link>
        <span class="text-gray-300">|</span>
        <h2 class="font-semibold text-gray-700">
          {{ isEdit ? 'Modifier le produit' : 'Nouveau produit' }}
        </h2>
      </div>
    </template>
    <Head :title="isEdit ? 'Modifier produit' : 'Nouveau produit'" />

    <form @submit.prevent="submit" enctype="multipart/form-data" class="max-w-3xl space-y-6">
      <!-- Informations générales -->
      <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">Informations générales</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nom -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
            <input v-model="form.name" type="text"
              class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
              :class="form.errors.name ? 'border-red-400' : 'border-gray-300'" />
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <!-- Catégorie -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
            <select v-model="form.category_id"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
              <option :value="null">-- Aucune --</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <!-- SKU -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SKU / Référence</label>
            <input v-model="form.sku" type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Prix -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prix (DA) *</label>
            <input v-model="form.price" type="number" min="0"
              class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
              :class="form.errors.price ? 'border-red-400' : 'border-gray-300'" />
            <p v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</p>
          </div>

          <!-- Prix promo -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prix promo (DA)</label>
            <input v-model="form.price_promo" type="number" min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Stock -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stock initial</label>
            <input v-model="form.stock" type="number" min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Délai livraison -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Délai livraison (jours)</label>
            <input v-model="form.delivery_days" type="number" min="1"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Matière -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Matière</label>
            <input v-model="form.material" type="text" placeholder="Bois massif, MDF..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Couleur -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
            <input v-model="form.color" type="text" placeholder="Noyer, Blanc..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Ordre -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
            <input v-model="form.sort_order" type="number"
              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>

          <!-- Description courte -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
            <textarea v-model="form.short_description" rows="2"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" />
          </div>

          <!-- Description complète -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description complète</label>
            <textarea v-model="form.description" rows="6"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
          </div>
        </div>

        <!-- Options -->
        <div class="flex flex-wrap gap-5 pt-1">
          <label v-for="opt in [
            { key: 'is_active', label: 'Produit actif' },
            { key: 'in_stock', label: 'En stock' },
            { key: 'is_custom', label: 'Sur mesure' },
            { key: 'is_featured', label: 'En vedette' },
          ]" :key="opt.key" class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" v-model="form[opt.key]"
              class="rounded border-gray-300 text-amber-700 focus:ring-amber-500 w-4 h-4" />
            <span class="text-sm text-gray-700">{{ opt.label }}</span>
          </label>
        </div>
      </div>

      <!-- Images -->
      <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">Images</h3>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Image principale</label>
          <input @change="form.main_image = $event.target.files[0]" type="file" accept="image/*"
            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />
          <div v-if="product?.main_image" class="mt-3">
            <p class="text-xs text-gray-400 mb-1">Image actuelle :</p>
            <img :src="`/storage/${product.main_image}`" class="w-24 h-24 object-cover rounded-lg border border-gray-200" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Images supplémentaires</label>
          <input @change="form.images = Array.from($event.target.files)" type="file" accept="image/*" multiple
            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />
        </div>
      </div>

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing"
          class="bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold px-8 py-3 rounded-xl transition-colors">
          {{ form.processing ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer le produit') }}
        </button>
        <Link :href="route('admin.products.index')"
          class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-3 rounded-xl transition-colors">
          Annuler
        </Link>
      </div>
    </form>
  </AdminLayout>
</template>
