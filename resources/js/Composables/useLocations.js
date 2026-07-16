import { ref } from 'vue';
import axios from 'axios';

export function useLocations() {
    const wilayas = ref([]);
    const communes = ref([]);
    const loadingWilayas = ref(false);
    const loadingCommunes = ref(false);

    async function fetchWilayas() {
        loadingWilayas.value = true;
        try {
            // route() (Ziggy) résout le bon préfixe quel que soit l'environnement
            // (sous-dossier local /ebeniste-shop/public, /boutique en prod, etc.) —
            // un chemin absolu codé en dur ('/api/wilayas') casse dès que l'app
            // n'est pas servie à la racine du domaine.
            const { data } = await axios.get(route('api.wilayas'));
            wilayas.value = data;
        } finally {
            loadingWilayas.value = false;
        }
    }

    async function fetchCommunes(wilayaId) {
        communes.value = [];
        if (!wilayaId) return;
        loadingCommunes.value = true;
        try {
            const { data } = await axios.get(route('api.communes', wilayaId));
            communes.value = data;
        } finally {
            loadingCommunes.value = false;
        }
    }

    return { wilayas, communes, loadingWilayas, loadingCommunes, fetchWilayas, fetchCommunes };
}
