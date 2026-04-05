<script setup>
// Settings can be passed as prop or hardcoded for now
</script>

<template>
  <footer id="contact" class="bg-black text-white mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16">

      <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8">
        <!-- Brand -->
        <div class="md:col-span-2">
          <div class="flex items-center gap-3 mb-6">
            <img src="/logo.png" alt="Kutch" class="h-9 w-auto brightness-0 invert" />
            <span class="text-base font-bold tracking-[0.22em] uppercase">Kutch</span>
          </div>
          <p class="text-neutral-400 text-sm leading-relaxed max-w-xs">
            Artisan ébéniste — Meubles sur mesure fabriqués avec passion pour sublimer votre intérieur.
          </p>
        </div>

        <!-- Navigation -->
        <div>
          <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-neutral-500 mb-5">Navigation</h4>
          <ul class="space-y-3">
            <li><a href="/" class="text-sm text-neutral-300 hover:text-white transition-colors">Accueil</a></li>
            <li><a href="/produits" class="text-sm text-neutral-300 hover:text-white transition-colors">Catalogue</a></li>
            <li><a href="#contact" class="text-sm text-neutral-300 hover:text-white transition-colors">Contact</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase text-neutral-500 mb-5">Contact</h4>
          <ul class="space-y-3 text-sm text-neutral-300">
            <li>+213 555 000 000</li>
            <li>Algérie — 58 wilayas</li>
            <li class="text-neutral-500 text-xs pt-2">Paiement à la livraison uniquement</li>
          </ul>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="border-t border-neutral-900 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-[11px] tracking-wider text-neutral-600">
          © {{ new Date().getFullYear() }} Kutch — Tous droits réservés
        </p>
        <p class="text-[11px] tracking-wider text-neutral-600 uppercase">
          Artisanat Algérien
        </p>
      </div>
    </div>
  </footer>
</template>
