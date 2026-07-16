import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // Ne PAS fixer `base` manuellement : laravel-vite-plugin calcule déjà le bon
    // préfixe pour les balises <script>/<link> initiales via APP_URL. Un `base`
    // personnalisé ici casse en revanche les imports dynamiques (code-splitting
    // Vue Router) qui, eux, utilisent cette valeur au runtime — d'où une page
    // blanche : le HTML se charge, mais les chunks lazy (Home.vue, etc.) 404
    // dès que le préfixe ne correspond pas exactement à l'environnement courant.
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
