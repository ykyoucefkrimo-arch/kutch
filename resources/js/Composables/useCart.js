import { ref, computed } from 'vue';

const CART_KEY = 'ebeniste_cart';
const items = ref(JSON.parse(localStorage.getItem(CART_KEY) || '[]'));

function save() {
    localStorage.setItem(CART_KEY, JSON.stringify(items.value));
}

export function useCart() {
    const count = computed(() => items.value.reduce((s, i) => s + i.quantity, 0));
    const total = computed(() => items.value.reduce((s, i) => s + i.unit_price * i.quantity, 0));

    function addItem(product, quantity = 1, options = null) {
        const existing = items.value.find(i => i.product_id === product.id);
        if (existing) {
            existing.quantity += quantity;
        } else {
            items.value.push({
                product_id: product.id,
                name: product.name,
                unit_price: product.price_promo ?? product.price,
                main_image: product.main_image,
                quantity,
                options,
            });
        }
        save();
    }

    function removeItem(productId) {
        items.value = items.value.filter(i => i.product_id !== productId);
        save();
    }

    function updateQty(productId, qty) {
        const item = items.value.find(i => i.product_id === productId);
        if (item) {
            if (qty <= 0) removeItem(productId);
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
