<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const storageUrl = (path) => `${page.props.storageUrl}/${path}`;

const props = defineProps({
    newArrivals: Array,
    max: Number,
});

const isFull = computed(() => props.newArrivals.length >= props.max);

function formatPrice(n) {
    return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
}

function remove(item) {
    if (confirm(`Retirer "${item.name}" des nouveautés ?`)) {
        router.delete(route('admin.new-arrivals.destroy', item.id));
    }
}
</script>

<template>
    <Head title="Nouveautés" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-semibold text-gray-700">Nouveautés</h2>
                <Link v-if="!isFull" :href="route('admin.new-arrivals.create')"
                    class="bg-amber-700 hover:bg-amber-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    + Nouvelle nouveauté
                </Link>
            </div>
        </template>

        <p class="text-gray-500 text-sm mb-5">
            Jusqu'à {{ max }} entrées mises en avant en tête de la page d'accueil, avec leur propre fiche
            (nom, prix, stock, images...) — totalement indépendantes de votre catalogue produits.
        </p>

        <p v-if="isFull" class="mb-5 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 text-sm">
            Vous avez atteint la limite de {{ max }} nouveautés. Retirez-en une pour en ajouter une autre.
        </p>

        <div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash?.error" class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ page.props.flash.error }}
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Catégorie</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Prix</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Stock</th>
                            <th class="px-4 py-3 w-40"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="!newArrivals.length">
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                Aucune nouveauté pour l'instant. La section "Nouveautés" reste masquée sur la page d'accueil tant qu'aucune n'est ajoutée.
                            </td>
                        </tr>
                        <tr v-for="item in newArrivals" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                                    <img v-if="item.main_image" :src="storageUrl(item.main_image)"
                                        :alt="item.name" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ item.name }}</p>
                                <p class="text-xs text-gray-400">{{ item.sku ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ item.category?.name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-amber-900">{{ formatPrice(item.price_promo ?? item.price) }}</p>
                                <p v-if="item.price_promo" class="text-xs text-gray-400 line-through">{{ formatPrice(item.price) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="[
                                    'text-xs font-medium px-2 py-0.5 rounded-full',
                                    item.in_stock ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                ]">
                                    {{ item.in_stock ? item.stock + ' en stock' : 'Rupture' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 justify-end">
                                    <a :href="route('new-arrivals.show', item.slug)" target="_blank"
                                        class="text-xs text-gray-400 hover:text-gray-700 font-medium">Voir</a>
                                    <Link :href="route('admin.new-arrivals.edit', item.id)"
                                        class="text-xs text-amber-700 hover:text-amber-900 font-medium">Modifier</Link>
                                    <button @click="remove(item)"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium">Retirer</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
