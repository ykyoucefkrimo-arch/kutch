<script setup>
import { ref } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  newArrival: Object,
  categories: Array,
});

const isEdit = !!props.newArrival;
const storageUrl = (path) => `${usePage().props.storageUrl}/${path}`;

// Valeurs initiales statiques — ne dépendent pas de la réactivité d'Inertia
const initMainImage = props.newArrival?.main_image ?? null;
const initImages = props.newArrival?.images ?? [];

const previewMainImage = ref(null);
const previewImages = ref([]);
const deletedImages = ref([]);

function onMainImageChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  form.main_image = file;
  if (previewMainImage.value) URL.revokeObjectURL(previewMainImage.value);
  previewMainImage.value = URL.createObjectURL(file);
}

function clearMainImage() {
  if (previewMainImage.value) URL.revokeObjectURL(previewMainImage.value);
  previewMainImage.value = null;
  form.main_image = null;
}

function toggleDeleteImage(img) {
  const idx = deletedImages.value.indexOf(img);
  if (idx === -1) {
    deletedImages.value.push(img);
    form.delete_images = [...deletedImages.value];
  } else {
    deletedImages.value.splice(idx, 1);
    form.delete_images = [...deletedImages.value];
  }
}

function onImagesChange(e) {
  const files = Array.from(e.target.files);
  form.images = files;
  previewImages.value.forEach(url => URL.revokeObjectURL(url));
  previewImages.value = files.map(f => URL.createObjectURL(f));
}

function removeNewImage(i) {
  URL.revokeObjectURL(previewImages.value[i]);
  previewImages.value.splice(i, 1);
  const newFiles = [...form.images];
  newFiles.splice(i, 1);
  form.images = newFiles.length ? newFiles : null;
}

const form = useForm({
  name:              props.newArrival?.name ?? '',
  category_id:       props.newArrival?.category_id ?? null,
  short_description: props.newArrival?.short_description ?? '',
  description:       props.newArrival?.description ?? '',
  price:             props.newArrival?.price ?? '',
  price_promo:       props.newArrival?.price_promo ?? '',
  sku:               props.newArrival?.sku ?? '',
  stock:             props.newArrival?.stock ?? 0,
  in_stock:          props.newArrival?.in_stock ?? true,
  is_custom:         props.newArrival?.is_custom ?? false,
  material:          props.newArrival?.material ?? '',
  color:             props.newArrival?.color ?? '',
  delivery_days:     props.newArrival?.delivery_days ?? 7,
  sort_order:        props.newArrival?.sort_order ?? 0,
  main_image:        null,
  images:            null,
  delete_images:     [],
});

function submit() {
  if (isEdit) {
    form.transform(data => ({ ...data, _method: 'PUT' }))
        .post(route('admin.new-arrivals.update', props.newArrival.id), {
          forceFormData: true,
        });
  } else {
    form.post(route('admin.new-arrivals.store'), {
      forceFormData: true,
    });
  }
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.new-arrivals.index')" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
          ← Retour
        </Link>
        <span class="text-gray-300">|</span>
        <h2 class="font-semibold text-gray-700">
          {{ isEdit ? 'Modifier la nouveauté' : 'Nouvelle nouveauté' }}
        </h2>
      </div>
    </template>
    <Head :title="isEdit ? 'Modifier nouveauté' : 'Nouvelle nouveauté'" />

    <form @submit.prevent="submit" enctype="multipart/form-data" class="max-w-3xl space-y-6">
      <!-- Informations générales -->
      <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-2">Informations générales</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nom -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
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
            <p v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</p>
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
            { key: 'in_stock', label: 'En stock' },
            { key: 'is_custom', label: 'Sur mesure' },
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
          <label class="block text-sm font-medium text-gray-700 mb-2">Image principale {{ isEdit ? '' : '*' }}</label>
          <input @change="onMainImageChange" type="file" accept="image/*"
            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />
          <p v-if="form.errors.main_image" class="text-red-500 text-xs mt-1">{{ form.errors.main_image }}</p>

          <!-- Aperçu nouvelle image sélectionnée -->
          <div v-if="previewMainImage" class="mt-3">
            <p class="text-xs text-gray-400 mb-1">Nouvelle image :</p>
            <div class="relative inline-block">
              <img :src="previewMainImage" class="w-24 h-24 object-cover rounded-lg border-2 border-amber-400" />
              <button type="button" @click="clearMainImage"
                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold transition-colors">
                ×
              </button>
            </div>
          </div>

          <!-- Image actuelle -->
          <div v-if="initMainImage && !previewMainImage" class="mt-3">
            <p class="text-xs text-gray-400 mb-1">Image actuelle :</p>
            <img :src="storageUrl(initMainImage)" class="w-24 h-24 object-cover rounded-lg border border-gray-200" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Images supplémentaires</label>
          <input @change="onImagesChange" type="file" accept="image/*" multiple
            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />

          <!-- Aperçu nouvelles images sélectionnées -->
          <div v-if="previewImages.length" class="mt-3 flex flex-wrap gap-2">
            <div v-for="(url, i) in previewImages" :key="i" class="relative">
              <img :src="url" class="w-20 h-20 object-cover rounded-lg border-2 border-amber-400" />
              <button type="button" @click="removeNewImage(i)"
                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold transition-colors">
                ×
              </button>
            </div>
          </div>

          <!-- Images existantes -->
          <div v-if="initImages.filter(img => !deletedImages.includes(img)).length" class="mt-3 flex flex-wrap gap-2">
            <div v-for="img in initImages.filter(img => !deletedImages.includes(img))" :key="img" class="relative">
              <img :src="storageUrl(img)" class="w-20 h-20 object-cover rounded-lg border border-gray-200" />
              <button type="button" @click="toggleDeleteImage(img)"
                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold transition-colors">
                ×
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing"
          class="bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold px-8 py-3 rounded-xl transition-colors">
          {{ form.processing ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer la nouveauté') }}
        </button>
        <Link :href="route('admin.new-arrivals.index')"
          class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-3 rounded-xl transition-colors">
          Annuler
        </Link>
      </div>
    </form>
  </AdminLayout>
</template>
