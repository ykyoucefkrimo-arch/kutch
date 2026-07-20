import { ref, computed } from 'vue';

const CART_KEY = 'ebeniste_cart';
const items = ref(JSON.parse(localStorage.getItem(CART_KEY) || '[]'));

function save() {
    localStorage.setItem(CART_KEY, JSON.stringify(items.value));
}

export function useCart() {
    const count = computed(() => items.value.reduce((s, i) => s + i.quantity, 0));
    const total = computed(() => items.value.reduce((s, i) => s + i.unit_price * i.quantity, 0));

    // `type` distingue un vrai produit du catalogue ('product') d'une entrée
    // "nouveauté" independante ('new_arrival') — les deux ont leur propre
    // espace d'identifiants, d'où la clé composite (type, entity.id).
    function addItem(entity, quantity = 1, options = null, type = 'product') {
        const existing = items.value.find(i => i.type === type && i.entity_id === entity.id);
        if (existing) {
            existing.quantity += quantity;
        } else {
            items.value.push({
                type,
                entity_id: entity.id,
                name: entity.name,
                unit_price: entity.price_promo ?? entity.price,
                main_image: entity.main_image,
                quantity,
                options,
            });
        }
        save();
    }

    function removeItem(type, entityId) {
        items.value = items.value.filter(i => !(i.type === type && i.entity_id === entityId));
        save();
    }

    function updateQty(type, entityId, qty) {
        const item = items.value.find(i => i.type === type && i.entity_id === entityId);
        if (item) {
            if (qty <= 0) removeItem(type, entityId);
            else item.quantity = qty;
            save();
        }
    }

    function clearCart() {
        items.value = [];
        save();
    }

    function formatPrice(amount) {
        return new Intl.NumberFormat('fr-DZ').format(amount) + ' DA';
    }

    return { items, count, total, addItem, removeItem, updateQty, clearCart, formatPrice };
}
