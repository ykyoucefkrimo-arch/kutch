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
            const { data } = await axios.get('/api/wilayas');
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
            const { data } = await axios.get(`/api/communes/${wilayaId}`);
            communes.value = data;
        } finally {
            loadingCommunes.value = false;
        }
    }

    return { wilayas, communes, loadingWilayas, loadingCommunes, fetchWilayas, fetchCommunes };
}
