import { ref } from 'vue';

export function useCartAnimation() {
    const flyingItem = ref(null); // { x, y, size, image }

    function animateToCart(event, product) {
        const btn = event.currentTarget;
        const btnRect = btn.getBoundingClientRect();

        // Position de l'icône panier dans la navbar
        const cartIcon = document.querySelector('[data-cart-icon]');
        const cartRect = cartIcon ? cartIcon.getBoundingClientRect() : { left: window.innerWidth - 50, top: 20 };

        const startX = btnRect.left + btnRect.width / 2;
        const startY = btnRect.top + btnRect.height / 2;

        flyingItem.value = {
            x: startX,
            y: startY,
            targetX: cartRect.left + cartRect.width / 2,
            targetY: cartRect.top + cartRect.height / 2,
            image: product.main_image,
            id: Date.now(),
        };

        setTimeout(() => { flyingItem.value = null; }, 800);
    }

    return { flyingItem, animateToCart };
}
