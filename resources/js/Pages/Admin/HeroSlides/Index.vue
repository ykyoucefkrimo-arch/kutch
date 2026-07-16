<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ slides: Array });

// ── Ajout d'une nouvelle slide ────────────────────────────────
const addForm = useForm({
  image: null,
  caption: '',
  subtitle: '',
  link: '',
});

const addPreview = ref(null);
const showAddForm = ref(false);

function onFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  addForm.image = file;
  addPreview.value = URL.createObjectURL(file);
}

function submitAdd() {
  addForm.post(route('admin.hero.store'), {
    forceFormData: true,
    onSuccess: () => {
      addForm.reset();
      addPreview.value = null;
      showAddForm.value = false;
    },
  });
}

// ── Édition inline ────────────────────────────────────────────
const editId = ref(null);
const editForm = useForm({ caption: '', subtitle: '', link: '', image: null });
const editPreview = ref(null);

function startEdit(slide) {
  editId.value = slide.id;
  editForm.caption  = slide.caption  ?? '';
  editForm.subtitle = slide.subtitle ?? '';
  editForm.link     = slide.link     ?? '';
  editForm.image    = null;
  editPreview.value = null;
}

function onEditFile(e) {
  const file = e.target.files[0];
  if (!file) return;
  editForm.image = file;
  editPreview.value = URL.createObjectURL(file);
}

function submitEdit(slide) {
  editForm.post(route('admin.hero.update', slide.id), {
    forceFormData: true,
    onSuccess: () => { editId.value = null; editPreview.value = null; },
  });
}

// ── Toggle actif/inactif ──────────────────────────────────────
function toggle(slide) {
  router.patch(route('admin.hero.toggle', slide.id), {}, { preserveScroll: true });
}

// ── Suppression ───────────────────────────────────────────────
const confirmDeleteId = ref(null);

function deleteSlide(id) {
  router.delete(route('admin.hero.destroy', id), {
    preserveScroll: true,
    onSuccess: () => { confirmDeleteId.value = null; },
  });
}

// ── Réordonnancement drag & drop ──────────────────────────────
const draggingId = ref(null);
const localSlides = computed(() => [...props.slides].sort((a, b) => a.sort_order - b.sort_order));

function onDragStart(id) { draggingId.value = id; }
function onDragOver(e) { e.preventDefault(); }

function onDrop(targetId) {
  if (draggingId.value === null || draggingId.value === targetId) return;

  const sorted = [...localSlides.value];
  const fromIdx = sorted.findIndex(s => s.id === draggingId.value);
  const toIdx   = sorted.findIndex(s => s.id === targetId);

  sorted.splice(toIdx, 0, sorted.splice(fromIdx, 1)[0]);
  const ids = sorted.map(s => s.id);

  router.post(route('admin.hero.reorder'), { ids }, { preserveScroll: true });
  draggingId.value = null;
}
</script>

<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-gray-700">Images Hero</h2>
    </template>
    <Head title="Admin — Images Hero" />

    <!-- Modal confirmation suppression -->
    <div v-if="confirmDeleteId !== null"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
        <p class="text-sm font-semibold text-gray-800 mb-2">Supprimer cette slide ?</p>
        <p class="text-sm text-gray-500 mb-5">L'image sera définitivement supprimée du serveur.</p>
        <div class="flex gap-3 justify-end">
          <button @click="confirmDeleteId = null"
            class="px-4 py-2 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
            Annuler
          </button>
          <button @click="deleteSlide(confirmDeleteId)"
            class="px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">
            Supprimer
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-4xl space-y-6">

      <!-- Bouton ajouter -->
      <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
          {{ localSlides.length }} slide{{ localSlides.length !== 1 ? 's' : '' }} —
          glissez-déposez pour réordonner
        </p>
        <button @click="showAddForm = !showAddForm"
          class="flex items-center gap-2 bg-amber-700 hover:bg-amber-800 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Ajouter une slide
        </button>
      </div>

      <!-- Formulaire d'ajout -->
      <div v-if="showAddForm" class="bg-white rounded-xl shadow-sm p-6 border border-amber-100">
        <h3 class="font-semibold text-gray-800 mb-4">Nouvelle slide</h3>
        <form @submit.prevent="submitAdd" enctype="multipart/form-data" class="space-y-4">

          <!-- Upload image -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image * (max 5 Mo)</label>
            <div
              class="relative border-2 border-dashed rounded-xl overflow-hidden transition-colors"
              :class="addPreview ? 'border-amber-400' : 'border-gray-300 hover:border-amber-400'">
              <input type="file" accept="image/*" @change="onFileChange"
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
              <div v-if="!addPreview" class="py-12 flex flex-col items-center gap-2 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm">Cliquer ou glisser une image ici</span>
                <span class="text-xs">JPG, PNG, WEBP</span>
              </div>
              <img v-else :src="addPreview" class="w-full h-56 object-cover" />
            </div>
            <p v-if="addForm.errors.image" class="text-xs text-red-600 mt-1">{{ addForm.errors.image }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Légende</label>
              <input v-model="addForm.caption" type="text" placeholder="ex : Studio KUTCH, Alger"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Sous-titre</label>
              <input v-model="addForm.subtitle" type="text" placeholder="ex : Mobilier sur mesure"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Lien (optionnel)</label>
              <input v-model="addForm.link" type="text" placeholder="ex : /produits ou https://..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
            </div>
          </div>

          <div class="flex gap-3 pt-2">
            <button type="submit" :disabled="addForm.processing || !addForm.image"
              class="bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors">
              {{ addForm.processing ? 'Upload...' : 'Ajouter la slide' }}
            </button>
            <button type="button" @click="showAddForm = false; addForm.reset(); addPreview = null"
              class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm hover:bg-gray-50">
              Annuler
            </button>
          </div>
        </form>
      </div>

      <!-- Liste des slides -->
      <div v-if="localSlides.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">Aucune slide. Ajoutez votre première image hero.</p>
      </div>

      <div class="space-y-3">
        <div
          v-for="slide in localSlides" :key="slide.id"
          draggable="true"
          @dragstart="onDragStart(slide.id)"
          @dragover="onDragOver"
          @drop="onDrop(slide.id)"
          class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 transition-opacity"
          :class="draggingId === slide.id ? 'opacity-40' : 'opacity-100'">

          <!-- Vue compacte -->
          <div v-if="editId !== slide.id" class="flex items-stretch">
            <!-- Poignée drag + miniature -->
            <div class="flex items-center gap-3 p-3 flex-1 min-w-0">
              <div class="cursor-grab text-gray-300 hover:text-gray-500 shrink-0 select-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8h16M4 16h16"/>
                </svg>
              </div>
              <img :src="slide.image_url" :alt="slide.caption"
                class="w-28 h-16 object-cover rounded-lg shrink-0 border border-gray-100" />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">
                  {{ slide.caption || '(sans légende)' }}
                </p>
                <p v-if="slide.subtitle" class="text-xs text-gray-500 truncate">{{ slide.subtitle }}</p>
                <p v-if="slide.link" class="text-xs text-amber-600 truncate mt-0.5">→ {{ slide.link }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 px-4 border-l border-gray-100 shrink-0">
              <!-- Toggle actif -->
              <button @click="toggle(slide)" :title="slide.is_active ? 'Désactiver' : 'Activer'"
                class="p-2 rounded-lg transition-colors"
                :class="slide.is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
              <!-- Éditer -->
              <button @click="startEdit(slide)" title="Modifier"
                class="p-2 rounded-lg text-gray-400 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <!-- Supprimer -->
              <button @click="confirmDeleteId = slide.id" title="Supprimer"
                class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Formulaire d'édition inline -->
          <div v-else class="p-5">
            <p class="text-sm font-semibold text-gray-800 mb-4">Modifier la slide</p>
            <form @submit.prevent="submitEdit(slide)" enctype="multipart/form-data" class="space-y-4">

              <!-- Aperçu / remplacement image -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remplacer l'image (optionnel)</label>
                <div class="relative border-2 border-dashed rounded-xl overflow-hidden"
                  :class="editPreview ? 'border-amber-400' : 'border-gray-200 hover:border-amber-300'">
                  <input type="file" accept="image/*" @change="onEditFile"
                    class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                  <img :src="editPreview || slide.image_url" class="w-full h-44 object-cover" />
                  <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded z-0">
                    Cliquer pour changer
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Légende</label>
                  <input v-model="editForm.caption" type="text"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sous-titre</label>
                  <input v-model="editForm.subtitle" type="text"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Lien</label>
                  <input v-model="editForm.link" type="text"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
                </div>
              </div>

              <div class="flex gap-3">
                <button type="submit" :disabled="editForm.processing"
                  class="bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold px-5 py-2 rounded-xl text-sm transition-colors">
                  {{ editForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
                <button type="button" @click="editId = null; editPreview = null"
                  class="px-5 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50">
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
