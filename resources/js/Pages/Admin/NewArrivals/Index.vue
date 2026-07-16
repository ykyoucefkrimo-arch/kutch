<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    selected: Array,   // produits actuellement en nouveauté (max `max`)
    available: Array,  // produits actifs éligibles à l'ajout
    max: Number,
});

const page = usePage();
const storageUrl = (path) => `${page.props.storageUrl}/${path}`;

const isFull = computed(() => props.selected.length >= props.max);

const addForm = useForm({ product_id: '' });

const add = () => {
    if (!addForm.product_id) return;
    addForm.post(route('admin.new-arrivals.store'), {
        preserveScroll: true,
        onSuccess: () => addForm.reset('product_id'),
    });
};

const remove = (product) => {
    router.delete(route('admin.new-arrivals.destroy', product.id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Nouveautés" />
    <AdminLayout>
        <div class="max-w-4xl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Nouveautés</h1>
                <p class="text-gray-500 mt-1">
                    Choisissez jusqu'à {{ max }} produits mis en avant : ils s'affichent en premier sur la page d'accueil.
                </p>
            </div>

            <!-- Flash -->
            <div v-if="page.props.flash?.success" class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                {{ page.props.flash.error }}
            </div>

            <!-- Ajout -->
            <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter un produit en nouveauté</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select v-model="addForm.product_id" :disabled="isFull"
                        class="flex-1 rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">{{ isFull ? `Limite de ${max} atteinte` : 'Sélectionner un produit…' }}</option>
                        <option v-for="p in available" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <button @click="add" :disabled="isFull || !addForm.product_id || addForm.processing"
                        class="bg-gray-900 hover:bg-gray-800 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium px-5 py-2 rounded-lg whitespace-nowrap">
                        Ajouter
                    </button>
                </div>
                <p v-if="isFull" class="text-amber-600 text-xs mt-2">
                    Vous avez atteint la limite de {{ max }} nouveautés. Retirez-en une pour en ajouter une autre.
                </p>
                <p v-else-if="!available.length" class="text-gray-400 text-xs mt-2">
                    Aucun produit actif disponible à ajouter.
                </p>
            </div>

            <!-- Sélection actuelle -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Produits en nouveauté</h2>
                    <span class="text-sm text-gray-400">{{ selected.length }} / {{ max }}</span>
                </div>

                <div v-if="!selected.length" class="p-8 text-center text-gray-400 text-sm">
                    Aucune nouveauté sélectionnée. En attendant, la page d'accueil affiche les 3 produits les plus récents.
                </div>

                <ul v-else class="divide-y divide-gray-100">
                    <li v-for="p in selected" :key="p.id" class="flex items-center gap-4 p-4">
                        <img v-if="p.main_image" :src="storageUrl(p.main_image)" :alt="p.name"
                            class="w-14 h-14 rounded-lg object-cover border border-gray-100" />
                        <div v-else class="w-14 h-14 rounded-lg bg-gray-100 grid place-items-center text-gray-300 text-xl">🪑</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ p.name }}</p>
                            <p class="text-sm text-gray-400">{{ p.category?.name ?? 'Sans catégorie' }}</p>
                        </div>
                        <button @click="remove(p)"
                            class="text-red-600 hover:text-red-700 text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-red-50">
                            Retirer
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </AdminLayout>
</template>
