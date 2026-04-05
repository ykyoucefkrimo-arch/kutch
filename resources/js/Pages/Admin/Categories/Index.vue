<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ categories: Array });

const showForm = ref(false);
const editingCategory = ref(null);

const form = useForm({ name: '', description: '', is_active: true, sort_order: 0 });

function openCreate() {
  editingCategory.value = null;
  form.reset();
  form.is_active = true;
  showForm.value = true;
}

function openEdit(cat) {
  editingCategory.value = cat;
  form.name = cat.name;
  form.description = cat.description ?? '';
  form.is_active = cat.is_active;
  form.sort_order = cat.sort_order;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  editingCategory.value = null;
  form.reset();
}

function submit() {
  if (editingCategory.value) {
    form.put(route('admin.categories.update', editingCategory.value.id), {
      onSuccess: () => closeForm()
    });
  } else {
    form.post(route('admin.categories.store'), {
      onSuccess: () => closeForm()
    });
  }
}

function deleteCategory(id) {
  if (confirm('Supprimer cette catégorie ? Les produits associés ne seront pas supprimés.')) {
    form.delete(route('admin.categories.destroy', id));
  }
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="font-semibold text-gray-700">Catégories</h2>
        <button @click="openCreate"
          class="bg-amber-700 hover:bg-amber-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
          + Nouvelle catégorie
        </button>
      </div>
    </template>
    <Head title="Admin — Catégories" />

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showForm" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
          <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">
              {{ editingCategory ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
            </h3>
            <button @click="closeForm" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <form @submit.prevent="submit" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
              <input v-model="form.name" type="text" autofocus
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                :class="form.errors.name ? 'border-red-400' : ''" />
              <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="2"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" />
            </div>
            <div class="flex items-center gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.is_active"
                  class="rounded border-gray-300 text-amber-700 focus:ring-amber-500 w-4 h-4" />
                <span class="text-sm text-gray-700">Active</span>
              </label>
              <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Ordre</label>
                <input v-model="form.sort_order" type="number"
                  class="w-20 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500" />
              </div>
            </div>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="form.processing"
                class="flex-1 bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg text-sm transition-colors">
                {{ form.processing ? '...' : (editingCategory ? 'Mettre à jour' : 'Créer') }}
              </button>
              <button type="button" @click="closeForm"
                class="flex-1 border border-gray-300 text-gray-600 hover:bg-gray-50 py-2.5 rounded-lg text-sm transition-colors">
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Liste -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Catégorie</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Slug</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Produits</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Statut</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Ordre</th>
              <th class="px-4 py-3 w-28"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="!categories?.length">
              <td colspan="6" class="px-4 py-10 text-center text-gray-400">Aucune catégorie</td>
            </tr>
            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-medium text-gray-800">{{ cat.name }}</td>
              <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ cat.slug }}</td>
              <td class="px-4 py-3 text-gray-600">{{ cat.products_count }}</td>
              <td class="px-4 py-3">
                <span :class="['text-xs font-medium px-2 py-0.5 rounded-full', cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                  {{ cat.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-500">{{ cat.sort_order }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3 justify-end">
                  <button @click="openEdit(cat)" class="text-xs text-amber-700 hover:text-amber-900 font-medium">Modifier</button>
                  <button @click="deleteCategory(cat.id)" class="text-xs text-red-500 hover:text-red-700 font-medium">Supprimer</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
